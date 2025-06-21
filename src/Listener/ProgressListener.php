<?php declare(strict_types=1);
/**
 * ProgressListener.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use SwoftComponents\SitemapPusher\Sitemap;
use SwoftComponents\SitemapPusher\SitemapPusherEvent;

/**
 * Class ProgressListener
 *
 * @since 1.0.0
 * @Listener(event=SitemapPusherEvent::GENERATE_PROGRESS)
 */
class ProgressListener implements EventHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(EventInterface $event): void
    {
        $currentNum = $event->getParam('currentNum');
        $total = $event->getParam('total');
        $logPerNum = $event->getParam('logPerNum');
        $duration = $event->getParam('duration');
        /** @var Sitemap $sitemap */
        $sitemap = $event->getTarget();
        // 预测完成时间
        $predictTime = $sitemap->predictTime($currentNum, $total, $logPerNum, $duration);
        CLog::info(sprintf('Sitemap progress: %d/%d, finished: %s', $currentNum, $total, $predictTime));
    }
}
