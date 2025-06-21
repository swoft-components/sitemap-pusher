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

use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftComponents\SitemapPusher\Contract\DataSourceInterface;
use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Class CustomSource
 *
 * @since 2.0.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class CustomDataSource implements DataSourceInterface
{

    /**
     * @var int 指向本地数据读取的起始偏移量
     */
    private int $offset = 0;

    /**
     * @var array 自定义数据源的数据
     */
    private array $data = [];

    /**
     * 获取当前数据源的数据，每次获取指定分页的记录数，返回数据不足分页表示数据获取完毕.
     *
     * @param Sitemap $sitemap
     * @param int $size
     * @return array
     */
    public function getData(Sitemap $sitemap, int $size): array
    {

        // 偏移量复制值
        $offsetCopyVal = null;
        // 剩余的数据量
        $left = count($this->data) - $this->offset;
        // 防止作为最后一个状态时进入递归死循环
        if ($left == 0) {
            return [];
        }
        // 表示数据已经不够一页，需要从下个数据源读取
        if ($size - $left > 0) {
            $offsetCopyVal = $this->offset;
            $this->offset += $left;
            $sitemap->nextDataSource();
            if ($left > 0) {
                return array_merge(array_slice($this->data, $offsetCopyVal, $left), $sitemap->getData($size - $left));
            }
            return $sitemap->getData($size);
        } else {
            $offsetCopyVal = $this->offset;
            $this->offset += $size;
            return array_slice($this->data, $offsetCopyVal, $size);
        }
    }

    /**
     * 获取当前数据源的总记录数
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

}
