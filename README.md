# SitemapPusher

## 概述

SitemapPusher 是一个专门为 Swoft2 开发者设计的网站地图组件，旨在简化向各大站长平台提交网站链接的过程。通过自定义数据源生成sitemap，自动化的链接提交，帮助站长加速搜索引擎对新发布或更新内容的收录速度。

### 版本说明

当前最新版本：**v1.0.6-alpha**

稳定版：v1.0.2（此版本不支持命令行工具）

### 功能特色

+ 自定义数组数据源（`CustomDataSource::class`）

  用户可以直接通过配置数组内容，写入sitemap。
  ```php
  // 自定义数据源至少有一个字段，后续三个字段为可选字段，分别对应: lastmod, changefreq, priority
  return [
    'app' => [
        'data' => [
            ['https://www.liujie.xin/'],
            ['https://www.liujie.xin/index.html'],
            ['https://www.liujie.xin/about.html'],
        ],
    ]
  ];
  ```
+ 用户自定义数据源（`@DataSource`）

  使用 @DataSource 注解绑定到相应的自定义数据源类，数据源类必须实现 `DataSourceInterface::class`

+ 分页执行，降低执行过程中的内存占用率

  通过配置参数`$pageSize`，数据源均支持分页执行，以便分批次写入sitemap 文件，降低内存的占用率。

+ 大数据量网站地图生成，进度提示，预计完成时间提示

  生成网站地图参数可以配置参数 `logPerNum`，每写入`$logPerNum`条数据，会触发一个事件，默认事件会打印当前网站地图执行的进度，和预计完成时间。同时支持用户自定义事件处理。

+ 自定义事件处理函数

    + sitemap 生成前事件

    + 进度报告事件

    + 异常处理事件

    + sitemap 生成后事件

      用户可以根据需求自定义相应的事件处理函数。

+ 百度地图主动推送

  当前版本支持百度地图的主动推送功能，可以通过 swoft 事件触发机制，快速集成到系统中。
  
+ 百度地图主动推送命令支持（新功能）
  首先需要再 bean 定义中配置好 site 和 token
  ```php
  return [
    ...
    Baidu::BEAN_NAME => [
        'token' => '百度主动推送token',
        'site' => '要被推送的网站域名',
    ],
    ...
  ];
+ ```
  ```bash
  # 主动推送 url 到百度搜索引擎（目前只集成了baidu搜索引擎的主动推送）
  php bin/swoft sitemap:push [url] --engine=baidu
  ```

+ 命令行工具

  提供 `php swoft sitemap:generate -d=/tmp -name=sitemap.txt -n 50 -p 100` 命令行工具，手动生成网站地图。

    + --dir, -d 指定网站地图的生成路径，默认值为当前目录 `./`
    + --name, -name 指定网站地图的名称，默认值为`sitemap.xml`
    + --num, -n 指定分页大小，对大量数据生成地图的网站，数据要分批进行写入，默认值为`50`
    + --progress, -p 指定每隔多少条记录写入，日志显示当前的执行进度，和预计完成时间，默认值为 `200`
    + --type, -t 指定生成的网站地图类型，默认值为 `xml`，可选值为 `txt` 和 `xml`

### 快速开始

#### 网站地图生成

网站地图生成示例代码：

```php
/** @var Sitemap $sitemap */
$sitemap = bean(Sitemap::BEAN_NAME);
// 不同类型的 Writer 对应最后生成的不同的 sitemap 类型
$writer = TxtWriter::new(\Swoft::getAlias('@base/public/sitemap.txt'), 'w');
$sitemap->generate($writer, 50, 50);
```

设置自定义数据源，支持设置数据获取的优先级（决定获取数据源的顺序，priority 值越大优先级越高）。

```php
/**
 * Class TagSource
 * @since 2.0.0
 * @DataSource(priority=100)
 */
class TagSource implements DataSourceInterface
{

    /**
     * 获取当前数据源的数据，每次获取指定分页的记录数，返回数据不足分页表示数据获取完毕.
     *
     * @param Sitemap $sitemap
     * @param int $size
     * @return DataSourceItem[]
     */
    public function getData(Sitemap $sitemap, int $size): array
    {
        return [];
    }

    /**
     * 获取当前数据源的总记录数
     *
     * @return int
     */
    public function count(): int
    {
        return 0;
    }

}
```

#### 手动生成

```bash
# 通过命令在当前目录生成网站地图
php bin/sowft sitemap:gen
# 设置目录 -d=/tmp
# 设置名称 -name=sitemap
# 设置类型 -t=xml (目前支持 txt 和 xml 两种格式)
# 设置分页大小 -n=200 数据量比较大，可以设置 500，看情况定
# 设置进度汇报参数，-p=500 表示，每写入500条记录，就会提示用户当前执行进度，和预计完成时间。
php bin/swoft sitemap:gen -d=/tmp -name=sitemap.txt -n 50 -p 100
```
