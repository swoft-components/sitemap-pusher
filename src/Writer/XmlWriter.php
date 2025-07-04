<?php declare(strict_types=1);
/**
 * XmlWriter.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Writer;

use SwoftComponents\SitemapPusher\Concern\AbstractWriter;
use SwoftComponents\SitemapPusher\Contract\WriterInterface;
use SwoftComponents\SitemapPusher\DataSource\DataSourceItem;

/**
 * Class XmlWriter
 *
 * @since 1.0.5
 */
class XmlWriter extends AbstractWriter implements WriterInterface
{

    private string $header = '<?xml version="1.0" encoding="UTF-8"?>';

    private string $urlSetStart = '<urlset>';

    private string $urlSetEnd = '</urlset>';

    /**
     * 是否已经写入头部
     *
     * @var bool
     */
    private bool $beforeDone = false;

    /**
     * 是否已经写入尾部
     *
     * @var bool
     */
    private bool $afterDone = false;

    /**
     * 写入数据
     *
     * @param DataSourceItem $item
     * @return void
     */
    public function write(DataSourceItem $item): void
    {
        if (!$this->beforeDone) {
            fwrite($this->getFile(), $this->header. PHP_EOL);
            fwrite($this->getFile(), $this->urlSetStart. PHP_EOL);
            $this->beforeDone = true;
        }
        fwrite($this->getFile(), '  <url>'. PHP_EOL);
        fwrite($this->getFile(), "    <loc>{$item->getLoc()}</loc>". PHP_EOL);
        if ($item->getLastmod()) {
            fwrite($this->getFile(), "    <lastmod>{$item->getLastmod()}</lastmod>". PHP_EOL);
        }
        if ($item->getChangefreq()) {
            fwrite($this->getFile(), "    <changefreq>{$item->getChangefreq()}</changefreq>". PHP_EOL);
        }
        if ($item->getPriority()) {
            fwrite($this->getFile(), "    <priority>{$item->getPriority()}</priority>". PHP_EOL);
        }
        fwrite($this->getFile(), '  </url>'. PHP_EOL);
    }

    /**
     * 析构函数，在对象被销毁时自动执行
     */
    public function __destruct()
    {
        if ($this->beforeDone && !$this->afterDone) {
            fwrite($this->getFile(), $this->urlSetEnd. PHP_EOL);
            $this->afterDone = true;
        }
        // 析构父类
        parent::__destruct();
    }

}
