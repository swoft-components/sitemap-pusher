<?php declare(strict_types=1);
/**
 * AbstractWriter.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Concern;

use SwoftComponents\SitemapPusher\Contract\WriterInterface;
use SwoftComponents\SitemapPusher\DataSource\DataSourceItem;

/**
 * Class AbstractWriter
 *
 * @since 1.0.5
 */
abstract class AbstractWriter implements WriterInterface
{

    /**
     * 文件句柄
     *
     * @var resource
     */
    private $file;

    /**
     * 文件路径
     *
     * @var string
     */
    private string $filePath;

    /**
     * 获取文件句柄
     *
     * @return resource
     */
    protected function getFile()
    {
        return $this->file;
    }

    /**
     * 获取文件路径
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * 创建 Writer实例
     *
     * @param string $filePath
     * @param string $flags
     * @return self
     */
    public static function new(string $filePath, string $flags): self
    {
        $bean = new static();
        $bean->file = fopen($filePath, $flags);
        $bean->filePath = $filePath;
        return $bean;
    }

    abstract public function write(DataSourceItem $item): void;

    /**
     * 关闭文件句柄
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
        }
    }

}
