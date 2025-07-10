<?php declare(strict_types=1);
/**
 * SitemapCommandTest.php
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
use Toolkit\Cli\App;
use Toolkit\Cli\Helper\FlagHelper;

class SitemapCommandTest extends TestCase
{

    /**
     * 测试生成网站地图的命令
     *
     * @return void
     */
    public function testGenerate(): void
    {
        /** @var App $app */
        $app = bean('cliApp');
        $input = input();
        $input->setFlags([
            '--dir', '/tmp',
            '--name', 'sitemap',
            '--num', '20',
            '--progress', '20',
            '--type', 'txt'
        ]);
        $input->setCommand('sitemap:gen');
        // 执行命令
        $app->run();
        $dir = $input->getOpt('dir');
        $name = $input->getOpt('name');
        $type = $input->getOpt('type');
        $path = rtrim($dir, '/'). DIRECTORY_SEPARATOR. $name. '.'. $type;
        // 判断网站地图文件是否生成成功
        $this->assertFileExists($path);
        // 删除数据
        unlink($path);
    }

    public function testPush(): void
    {

        /** @var App $app */
        $app = bean('cliApp');
        $input = input();
        $input->setFlags([
            'https://www.baidu.com/',
            '--engine', 'baidu',
        ]);
        $input->setCommand('sitemap:push');
        $app->run();
    }

}
