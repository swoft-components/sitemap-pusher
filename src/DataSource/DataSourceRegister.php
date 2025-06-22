<?php declare(strict_types=1);
/**
 * DataSourceRegister.php
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

use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Class DataSourceRegister
 *
 * @since 1.0.1
 */
class DataSourceRegister
{

    /**
     * @var array 数据源类列表
     */
    private static array $dataSources = [];

    public static function addDataSource(string $dataSourceClass, int $priority): void
    {
        self::$dataSources[] = [$dataSourceClass, $priority];
    }

    /**
     * 注册数据源到 sitemap
     *
     * @param Sitemap $sitemap Sitemap 对象实例
     * @return int 注册的数据源数量
     */
    public static function register(Sitemap $sitemap): int
    {
        foreach (self::$dataSources as [$dataSourceClass, $priority]) {
            $sitemap->addDataSource(bean($dataSourceClass), $priority);
        }
        return count(self::$dataSources);
    }

}
