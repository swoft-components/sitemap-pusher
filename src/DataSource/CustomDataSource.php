<?php declare(strict_types=1);
/**
 * CustomSource.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\DataSource;

use Swoft;
use Swoft\Log\Helper\CLog;
use SwoftComponents\SitemapPusher\Annotation\Mapping\DataSource;
use SwoftComponents\SitemapPusher\Contract\DataSourceInterface;
use SwoftComponents\SitemapPusher\Exception\SitemapPusherException;
use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Class CustomSource
 *
 * @since 1.0.0
 * @DataSource(CustomDataSource::BEAN_NAME)
 */
class CustomDataSource implements DataSourceInterface
{

    /**
     * 容器中注册的对象名称
     */
    const BEAN_NAME = 'customDataSource';

    /**
     * 数据源文件的路径
     *
     * @var string|null
     */
    private ?string $filepath = null;

    /**
     * 文件的句柄.
     *
     * @var resource|null
     */
    private $file = null;

    /**
     * @throws SitemapPusherException
     */
    private function initFile(?string $filepath): void
    {
        if ($this->file) {
            return;
        }
        // 判断是否是有效的数据文件
        if (!$filepath) {
            return;
        }
        $filepath = Swoft::getAlias($filepath);
        // 判断文件是由有效
        if(!is_file($filepath)) {
            throw new SitemapPusherException(sprintf('Invalid data file path: %s.', $filepath));
        }
        // 获取数据文件句柄
        $handler = fopen($filepath, 'r');
        if (!$handler) {
            throw new SitemapPusherException(sprintf('File: %s, open error.', $filepath));
        }
        $this->file = $handler;
    }

    /**
     * 获取当前数据源的数据，每次获取指定分页的记录数，返回数据不足分页表示数据获取完毕.
     *
     * @param Sitemap $sitemap
     * @param int $size
     * @return DataSourceItem[]
     * @throws SitemapPusherException
     */
    public function getData(Sitemap $sitemap, int $size): array
    {
        $this->initFile($this->filepath);
        // 已经读取的行数
        $lineNum = 0;
        $list = [];
        while (!feof($this->file)){
            // 读取一行数据
            $line = fgets($this->file);
            if ($line === false) {
                // 不是文件结尾代表有错误
                if (!feof($this->file)) {
                    CLog::warning(sprintf('File: %s, read error.', $this->filepath));
                }
                break;
            }
            // 获取当前行的网站地图数据
            [$loc, $lastMod, $changeFreq, $priority] = explode(',', $line);
            // 如果没有 url 则跳过
            if (empty($loc)) {
                continue;
            }
            $loc = trim($loc);
            $lastMod = !empty(trim($lastMod)) ? trim($lastMod) : date('Y-m-d\TH:i:sP');
            $changeFreq = !empty(trim($changeFreq)) ? trim($changeFreq) : 'daily';
            $priority = !empty(trim($priority)) ? floatval($priority) : 1.0;
            $list[] = DataSourceItem::new($loc, $lastMod, $changeFreq, $priority);
            // 记录处理的数据行数
            ++ $lineNum;
            // 已经读取的行数足够一页
            if ($lineNum >= $size) {
                return $list;
            }
        }
        // 数据不足一页，需要从下个数据源读取
        $sitemap->nextDataSource();
        return array_merge($list, $sitemap->getData($size - $lineNum));
    }

    /**
     * 获取当前数据源的总记录数
     *
     * @return int
     * @throws SitemapPusherException
     */
    public function count(): int
    {
        $this->initFile($this->filepath);
        $lineNum = 0;
        // 获取文件中总数据行数
        while (!feof($this->file)){
            // 读取一行数据
            $line = fgets($this->file);
            if ($line === false) {
                // 不是文件结尾代表有错误
                if (!feof($this->file)) {
                    CLog::warning(sprintf('File: %s, read error.', $this->filepath));
                }
                break;
            }
            // 获取当前行的网站地图数据
            [$loc,] = explode(',', $line);
            // 如果没有 url 则跳过
            if (empty($loc)) {
                continue;
            }
            ++$lineNum;
        }
        // 重置文件指针
        rewind($this->file);
        return $lineNum;
    }

    /**
     * 销毁文件句柄资源对象
     */
    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
        }
    }

}
