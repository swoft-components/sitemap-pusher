<?php declare(strict_types=1);
/**
 * BaiduTest.php
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
use SwoftComponents\SitemapPusher\Site\Baidu;
use SwoftComponents\SitemapPusher\Response;

class BaiduTest extends TestCase
{

    public function testSubmit(): void
    {
        $urls = [
            'https://www.baidu.com'
        ];
        $response = bean(Baidu::class)->submit($urls);
        $this->assertInstanceOf(Response::class, $response);
        echo $response->getRawData(). PHP_EOL;
    }

}
