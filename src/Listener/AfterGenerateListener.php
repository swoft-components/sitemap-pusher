<?php declare(strict_types=1);
/**
 * AfterGenerateListener.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace Swoft\SitemapPusher\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\SitemapPusher\SitemapPusherEvent;

/**
 * Class AfterGenerateListener
 *
 * @since 1.0.0
 * @Listener(event=SitemapPusherEvent::AFTER_GENERATE)
 */
class AfterGenerateListener implements EventHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(EventInterface $event): void
    {
        CLog::info('Sitemap generated successfully.');
    }

}
