<?php declare(strict_types=1);
/**
 * DataSource.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class DataSource
 *
 * @since 1.0.0
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("name", type="string")
 * })
 */
class DataSource
{

    /**
     * 数据源名称
     *
     * @var string|mixed
     */
    private string $name = '';

    /**
     * 优先级, 默认值为 0, 优先级越大, 越先执行
     *
     * @var int
     */
    private int $priority = 0;

    /**
     * DataSource constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['priority'])) {
            $this->priority = (int)$values['priority'];
        }
    }

    /**
     * 获取数据源名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取当前数据源的优先级
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

}
