<?php declare(strict_types=1);
/**
 * CustomDataSourceTest.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponentsTest\SitemapPusher\Unit;

use PHPUnit\Framework\TestCase;
use SwoftComponents\SitemapPusher\DataSource\CustomDataSource;

class CustomDataSourceTest extends TestCase
{
    public function testCount()
    {
        /** @var CustomDataSource $bean */
        $bean = bean(CustomDataSource::BEAN_NAME);
        $this->assertInstanceOf(CustomDataSource::class, $bean);
        $num = $bean->count();
        $this->assertEquals($this->getSourceDataLines(), $num);
    }

    /**
     * 获取源数据的行数
     *
     * @return int
     */
    private function getSourceDataLines(): int
    {
        // 获取源数据内容
        $source = file_get_contents(config('app.filepath'));
        $lines = explode(PHP_EOL, $source);
        $lineNum = 0;
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            [$loc,] = explode(',', $line);
            if (empty($loc)) {
                continue;
            }
            $lineNum++;
        }
        return $lineNum;
    }

}
