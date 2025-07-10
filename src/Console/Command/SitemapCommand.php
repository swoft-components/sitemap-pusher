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

use Swoft\Bean\Container;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Exception\CommandFlagException;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use SwoftComponents\SitemapPusher\Helper\ConsoleHelper;
use SwoftComponents\SitemapPusher\Response;
use SwoftComponents\SitemapPusher\Site\Baidu;
use SwoftComponents\SitemapPusher\Sitemap;
use SwoftComponents\SitemapPusher\Writer\TxtWriter;
use SwoftComponents\SitemapPusher\Writer\XmlWriter;
use Throwable;

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
        } catch (Throwable $t) {
            output()->error("Generate sitemap failed: " . $t->getMessage());
        }
    }

    /**
     * @CommandMapping(name="push", alias="push", desc="Push url to search engine.")
     *
     * @CommandOption(name="engine", type="array", short="eg", default={"baidu"}, desc="Push url to search engine.", mode=Command::OPT_IS_ARRAY)
     * @CommandArgument(name="url", type="string", required=true, desc="Push url to search engine.", mode=Command::ARG_REQUIRED)
     *
     *
     * @param Input $input
     * @param Output $output
     * @return void
     * @throws CommandFlagException
     */
    public function push(Input $input, Output $output): void
    {
        $engines = $input->getSameOpt(['engine', 'eg'], ['baidu']);
        $url = $input->getRequiredArg('url');
        // 开始执行推送操作
        $enginesMap = [
            'baidu' => Baidu::BEAN_NAME,
        ];
        foreach ($engines as $engine) {
            if (!isset($enginesMap[$engine])) {
                $output->error("The '$engine' push engine is not supported.");
                continue;
            }
            if (!Container::getInstance()->has($enginesMap[$engine])) {
                $output->error("The bean of '$engine' is not found.");
                continue;
            }
            /** @var Response $response */
            $response = bean($enginesMap[$engine])->submit([$url]);
            if ($response->getErrorCode() == 0) {
                $output->success("Push url: $url to $engine {$response->getErrorMessage()}.");
            } else {
                $output->error($response->getRawData());
            }
        }
    }

}
