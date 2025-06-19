<?php declare(strict_types=1);
/**
 * Baidu.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace Swoft\SitemapPusher\Site;

use Swoft\SitemapPusher\Contract\SitemapInterface;
use Swoft\SitemapPusher\Exception\SitemapPusherException;
use Swoft\Stdlib\Contract\ResponseInterface;
use Swoft\Stdlib\Response;

/**
 * Class Baidu
 *
 * @since 1.0.0
 */
class Baidu implements SitemapInterface
{

    /**
     * 百度推送接口
     *
     * @var string $api
     */
    private string $api = 'http://data.zz.baidu.com/urls?site=%s&token=%s';

    /**
     * 在搜索资源平台申请的推送用的准入密钥
     *
     * @var string $token
     */
    private string $token;

    /**
     * 站点域名（含协议）
     *
     * @var string $site
     */
    private string $site;

    /**
     * 提交站点地图中的连接
     *
     * @var array $urls 站点地图中的连接
     * @throws SitemapPusherException
     */
    public function submit(array $urls): ResponseInterface
    {
        if (empty($this->site) || empty($this->token)) {
            throw new SitemapPusherException('站点域名或准入密钥未配置');
        }
        $url = sprintf($this->api, $this->site, $this->token);
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        // 如果返回结果为空
        if (empty($result)) {
            throw new SitemapPusherException('Baidu sitemap response data is empty.');
        }
        // 如果返回结果不是 JSON 格式
        $map = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SitemapPusherException('Baidu sitemap response data is not JSON format.');
        }
        // 提交成功返回: {"remain":8,"success":1}
        if (isset($map['success'])) {
            return Response::new()->setRawData($result)
                ->setErrorCode(0)
                ->setErrorMessage('success')
                ->setResult([
                    'success' => $map['success'],
                    'remain' => $map['remain'],
                ]);
        }
        // 提交失败返回: {"error":401,"message":"token is not valid"}
        if (isset($map['error']) && isset($map['message'])) {
            return Response::new()
                ->setRawData($result)
                ->setErrorCode($map['error'])
                ->setErrorMessage($map['message']);
        }
        // 非法格式返回
        throw new SitemapPusherException(sprintf('Baidu sitemap response data is invalid. %s', $result));
    }

}
