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

namespace Swoft\SitemapPusher\Annotation\Mapping;

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

    private string $name = '';

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

}
