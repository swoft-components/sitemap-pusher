<?php declare(strict_types=1);
/**
 * Autoloader.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace Swoft\SitemapPusher;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Helper\ComposerJSON;
use Swoft\SwoftComponent;

class Autoloader extends SwoftComponent
{

    /**
     * @inheritDoc
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function metadata(): array
    {
        $jsonFile = dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * 默认注册的 bean 定义
     *
     * @return array
     */
    public function beans(): array
    {
        return [
            'sitemap-generator' => [
                'class' => Sitemap::class,
                '__option' => [
                    'scope' => Bean::PROTOTYPE,
                ],
            ],
            'pusher-baidu' => [
                'class' => \Swoft\SitemapPusher\Site\Baidu::class,
                'token' => 'default',
                'site' => 'https://www.baidu.com',
            ],
        ];
    }

}
