<?php declare(strict_types=1);
/**
 * DataSourceItem.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\DataSource;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class DataSourceItem
 *
 * @since 1.0.5
 * @Bean(scope=Bean::PROTOTYPE)
 */
class DataSourceItem
{

    /**
     * URL地址
     *
     * @var string
     */
    private string $loc;

    /**
     * 最后修改时间, 格式：YYYY-MM-DD 或更精确的 YYYY-MM-DDTHH:MM:SS+TIMEZONE, 例如：2024-01-01T12:00:00+08:00
     *
     * @var string|null
     */
    private ?string $lastMod = null;

    /**
     * 更新频率, 可选值: always, hourly, daily, weekly, monthly, yearly, never
     *
     * @var string|null
     */
    private ?string $changeFreq = null;

    /**
     * 优先级, 0.0-1.0
     *
     * @var float|null
     */
    private ?float $priority = null;

    /**
     * 创建新的实例
     *
     * @param string $loc
     * @param string|null $lastMod
     * @param string|null $changeFreq
     * @param string|null $priority
     * @return static
     */
    public static function new(string $loc, string $lastMod = null, string $changeFreq = null, float $priority = null): self
    {
        $bean = \bean(self::class);
        $bean->loc = $loc;
        $bean->lastMod = $lastMod;
        $bean->changeFreq = $changeFreq;
        $bean->priority = $priority;
        return $bean;
    }

    /**
     * 获取URL地址
     *
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * 获取最后修改时间
     *
     * @return string|null
     */
    public function getLastMod(): ?string
    {
        return $this->lastMod;
    }

    /**
     * 获取更新频率
     *
     * @return string|null
     */
    public function getChangeFreq(): ?string
    {
        return $this->changeFreq;
    }

    /**
     * 获取优先级
     *
     * @return float|null
     */
    public function getPriority(): ?float
    {
        return $this->priority;
    }

}
