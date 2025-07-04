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

use SwoftComponents\SitemapPusher\Annotation\Mapping\DataSource;
use SwoftComponents\SitemapPusher\Contract\DataSourceInterface;
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
     * @return DataSourceItem[]
     */
    public function getData(Sitemap $sitemap, int $size): array
    {
        // 剩余的数据量
        $left = count($this->data) - $this->offset;
        // 偏移量复制值
        $offsetCopyVal = $this->offset;
        // 表示数据已经不够一页，需要从下个数据源读取
        if ($size - $left > 0) {
            $this->offset += $left;
            $sitemap->nextDataSource();
            if ($left > 0) {
                // 将数据封装为 DataSourceItem 对象
                $list = array_map(function ($item) {
                    $loc = $item[0];
                    $lastMod = $item[1] ?? null;
                    $changeFreq = $item[2] ?? null;
                    $priority = $item[3] ?? null;
                    return DataSourceItem::new($loc, $lastMod, $changeFreq, $priority);
                }, array_slice($this->data, $offsetCopyVal, $left));
                return array_merge($list, $sitemap->getData($size - $left));
            }
            return $sitemap->getData($size);
        } else {
            $this->offset += $size;
            return array_map(function ($item) {
                [$loc, $lastMod, $changeFreq, $priority] = $item;
                return DataSourceItem::new($loc, $lastMod, $changeFreq, $priority);
            }, array_slice($this->data, $offsetCopyVal, $size));
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
