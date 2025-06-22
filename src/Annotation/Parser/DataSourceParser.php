<?php declare(strict_types=1);
/**
 * DataSourceParser.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use SwoftComponents\SitemapPusher\Annotation\Mapping\DataSource;
use SwoftComponents\SitemapPusher\Contract\DataSourceInterface;
use SwoftComponents\SitemapPusher\Sitemap;

/**
 * Class DataSourceParser
 * @since 1.0.0
 * @AnnotationParser(annotation=DataSource::class)
 */
class DataSourceParser extends Parser
{

    /**
     * Parse the annotation object to array.
     *
     * @param int $type
     * @param DataSource $annotationObject
     *
     * @return array
     * @throws AnnotationException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@DataSource` must be defined on class method!');
        }
        // 判断数据源是否实现了数据源接口
        if (in_array($this->className, class_implements(DataSourceInterface::class), true)) {
            throw new AnnotationException("`$this->className` must implement `DataSourceInterface`!");
        }
        $name = $annotationObject->getName();
        $name = empty($name) ? $this->className : $name;
        // 注册到 Sitemap 类中
        Sitemap::registerDataSource($name);
        // 此相当于一个 Bean 定义(实例名称，类名，生命周期，别名)
        return [$name, $this->className, Bean::PROTOTYPE, ''];
    }

}
