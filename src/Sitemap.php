<?php declare(strict_types=1);
/**
 * Sitemap.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher;

use SplPriorityQueue;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use SwoftComponents\SitemapPusher\Contract\DataSourceInterface;
use SwoftComponents\SitemapPusher\DataSource\DataSourceRegister;

/**
 * Class Sitemap
 *
 * @since 1.0.0
 * @Bean(Sitemap::BEAN_NAME, scope=Bean::SINGLETON)
 */
class Sitemap
{

    use PrototypeTrait;

    /**
     * 容器中注册的对象名称
     *
     * @var string
     */
    const BEAN_NAME = 'sitemap-generator';

    /**
     * 数据源对象队列，用于存储数据源对象及其优先级
     *
     * @var SplPriorityQueue $dataSourceQueue
     */
    private SplPriorityQueue $dataSourceQueue;

    /**
     * 当前数据源对象
     *
     * @var DataSourceInterface|null $dataSource
     */
    private ?DataSourceInterface $dataSource = null;

    /**
     * 初始化函数，对象通过容器实例化时候优先执行
     */
    public function init(): void
    {
        // 初始化优先队列
        $this->dataSourceQueue = new SplPriorityQueue();
    }

    /**
     * 添加数据源
     *
     * @param DataSourceInterface $dataSource 数据源对象
     * @param int $priority 优先级整数值，越大优先级越高
     * @return void
     */
    public function addDataSource(DataSourceInterface $dataSource, int $priority): void
    {
        $this->dataSourceQueue->insert($dataSource, $priority);
    }

    /**
     * 切换到下一个数据源
     *
     * @return void
     */
    public function nextDataSource(): void
    {
        if ($this->dataSourceQueue->isEmpty()) {
            $this->dataSource = null;
            return;
        }
        $this->dataSource = $this->dataSourceQueue->extract();
    }

    /**
     * 生成 sitemap 文件
     *
     * @param string $filePath sitemap 文件路径
     * @param int $pageSize 分页大小，默认为 20
     * @param int $logPerNum 日志记录间隔，默认为 300
     * @return void
     */
    public function generate(string $filePath, int $pageSize = 50, int $logPerNum = 300): void
    {
        // 注册数据源
        DataSourceRegister::register($this);
        // 获取总记录数
        $total = $this->getCount();
        // 切换到第一个数据源
        $this->nextDataSource();
        // 触发生成前事件
        \Swoft::triggerByArray(SitemapPusherEvent::BEFORE_GENERATE, $this, [
            'filePath' => $filePath,
            'pageSize' => $pageSize,
            'logPerNum' => $logPerNum,
            'total' => $total,
        ]);
        $file = fopen($filePath, 'w');
        // 当前已经写入的记录数
        $currentNum = 0;
        $durationStart = microtime(true);
        // 数据记录列表，默认为空数组，用于存储每次获取到的数据记录
        $list = [];
        try {
            do {
                $list = $this->getData($pageSize);
                foreach ($list as $url) {
                    // 生成 sitemap 记录
                    fwrite($file, "$url\n");
                    ++$currentNum;
                    if ($currentNum % $logPerNum === 0 || $currentNum === $total) {
                        // 记录当前时间.
                        $durationEnd = microtime(true);
                        \Swoft::triggerByArray(SitemapPusherEvent::GENERATE_PROGRESS, $this, [
                            'filePath' => $filePath,
                            'pageSize' => $pageSize,
                            'logPerNum' => $logPerNum,
                            'total' => $total,
                            'currentNum' => $currentNum,
                            'list' => $list,
                            'duration' => intval($durationEnd - $durationStart),
                        ]);
                        // 重置开始时间
                        $durationStart = microtime(true);
                    }
                }
            } while (count($list) === $pageSize);
        } catch (\Throwable $t) {
            // 触发生成异常事件
            \Swoft::triggerByArray(SitemapPusherEvent::GENERATE_EXCEPTION, $this, [
                'filePath' => $filePath,
                'pageSize' => $pageSize,
                'logPerNum' => $logPerNum,
                'total' => $total,
                'currentNum' => $currentNum,
                'list' => $list,
                'exception' => $t,
            ]);
        } finally {
            fclose($file);
            // 触发生成后事件
            \Swoft::triggerByArray(SitemapPusherEvent::AFTER_GENERATE, $this, [
                'filePath' => $filePath,
                'pageSize' => $pageSize,
                'logPerNum' => $logPerNum,
                'total' => $total,
                'currentNum' => $currentNum,
            ]);
        }
    }

    /**
     * 获取指定分页大小的数据
     *
     * @param int $size 指定分页大小
     * @return array
     */
    public function getData(int $size): array
    {
        if ($this->dataSource === null) {
            return [];
        }
        return $this->dataSource->getData($this, $size);
    }

    /**
     * 获取数据源列表中所有数据源的总记录数
     *
     * @return int
     */
    private function getCount(): int
    {
        $count = 0;
        // 遍历会导致优先队列清空，需要用副本.
        $queue = clone $this->dataSourceQueue;
        foreach ($queue as $dataSource) {
            $count += $dataSource->count();
        }
        return $count;
    }

    /**
     * 预测完成时间
     *
     * @param int $current 当前记录数
     * @param int $total 总记录数
     * @param int $logPerNum 每次记录日志间隔条数
     * @param int $seconds 每次记录日志之间耗时（秒）
     * @param string $format 时间格式
     * @return string
     */
    public function predictTime(int $current, int $total, int $logPerNum, int $seconds, string $format = 'Y-m-d H:i:s'): string
    {
        // 计算总耗时
        $totalSeconds = (int) (($total - $current) / $logPerNum * $seconds);
        // 计算完成时间
        return date($format, time() + $totalSeconds);
    }

}
