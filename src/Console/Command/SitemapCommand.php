<?php declare(strict_types=1);
/**
 * SitemapCommand.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Console\Command;

use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use SwoftComponents\SitemapPusher\Helper\ConsoleHelper;
use SwoftComponents\SitemapPusher\Sitemap;
use SwoftComponents\SitemapPusher\Writer\TxtWriter;
use SwoftComponents\SitemapPusher\Writer\XmlWriter;

/**
 * Class SitemapCommand
 *
 * @since 1.0.3
 * @Command(name="sitemap", desc="sitemap-pusher command for generate sitemap.")
 *
 */
class SitemapCommand
{

    /**
     * @CommandMapping(name="generate", alias="gen", desc="Generate sitemap.")
     *
     * @CommandOption(name="dir", type="string", short="d", default="./", desc="Set a path for generating sitemap.")
     * @CommandOption(name="name", type="string", default="sitemap", desc="Set a name for generating sitemap.")
     * @CommandOption(name="num", type="int", short="n", default="50", desc="Set a page size for generating sitemap whith a large amount of data.")
     * @CommandOption(name="progress", type="int", short="p", default="200", desc="Print logs to indicate processing progress and estimated completion time for each specified value of data processed.")
     * @CommandOption(name="type", type="string", short="t", default="xml", desc="Generate sitemap type.")
     *
     * @return void
     */
    public function generate(): void
    {
        $dir = input()->getOpt('dir', './');
        $name = input()->getOpt('name', 'sitemap');
        $num = input()->getOpt('num', 50);
        $progress = input()->getOpt('progress', 200);
        $type = input()->getOpt('type', 'xml');
        try {
            $writer = null;
            if (!in_array($type, ['xml', 'txt'])) {
                output()->info("Generate sitemap type must be xml or txt.");
                return;
            }
            // 网站地图目标文件地址
            $filePath = rtrim($dir, '/'). DIRECTORY_SEPARATOR. $name. '.'. $type;
            if (!ConsoleHelper::confirm("Sitemap generating: $filePath, Ensure continue?")) {
                output()->writeln("Sitemap generating canceled.");
                return;
            }
            if ($type === 'xml') {
                $writer = XmlWriter::new($filePath, 'w');
            } else {
                $writer = TxtWriter::new($filePath, 'w');
            }
            /** @var Sitemap $sitemap */
            $sitemap = bean(Sitemap::BEAN_NAME);
            $sitemap->generate($writer, $num, $progress);
            output()->info("Generate sitemap success.");
        } catch (\Throwable $t) {
            output()->error("Generate sitemap failed: " . $t->getMessage());
        }
    }

}
