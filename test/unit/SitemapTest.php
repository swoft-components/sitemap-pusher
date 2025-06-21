<?php declare(strict_types=1);
/**
 * SitemapTest.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponentsTest\SitemapPusher\Unit;

use PHPUnit\Framework\TestCase;
use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Class SitemapTest
 *
 */
class SitemapTest extends TestCase
{

    public function testGenerate(): void
    {
        /** @var Sitemap $sitemap */
        $sitemap = bean('sitemap-generator');
        $this->assertIsObject($sitemap);
        $this->assertInstanceOf(Sitemap::class, $sitemap);
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR. 'sitemap.xml';
        $sitemap->generate($path, 5, 5);
        // 判断网站地图文件是否生成成功
        $this->assertFileExists($path);
        echo file_get_contents($path);
    }

}
