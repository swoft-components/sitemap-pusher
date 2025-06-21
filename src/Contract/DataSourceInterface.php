<?php declare(strict_types=1);
/**
 * DataSourceInterface.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Contract;

use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Interface DataSourceInterface
 *
 * @since 2.0.0
 */
interface DataSourceInterface
{

    /**
     * 获取当前数据源的数据，每次获取指定分页的记录数，返回数据不足分页表示数据获取完毕.
     *
     * @param Sitemap $sitemap Sitemap实例
     * @param int $size 每次获取的记录数
     * @return array
     */
    public function getData(Sitemap $sitemap, int $size): array;

    /**
     * 获取当前数据源的总记录数
     *
     * @return int
     */
    public function count(): int;

}
