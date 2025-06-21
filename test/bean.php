<?php
/**
 * bean.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

return [
    'config' => [
        'path' => __DIR__ . '/config',
    ],
    'sitemap-generator' => [
        'dataSourceList' => [
            bean('custom-datasource')
        ],
    ],
    'custom-datasource' => [
        'class' => \Swoft\SitemapPusher\DataSource\CustomDataSource::class,
        'data' => [
            'https://www.liujie.xin/',
            'https://www.liujie.xin/index.html',
            'https://www.liujie.xin/about.html',
        ],
    ],
];
