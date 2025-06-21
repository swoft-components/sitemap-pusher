<?php declare(strict_types=1);
/**
 * Response.php
 *
 * 版权所有(c) 2025 刘杰（king.2oo8@163.com）。保留所有权利。
 *
 * 未经事先书面许可，任何单位或个人不得将本软件的任何部分以任何形式（包括但不限于复制、
 * 传播、披露等）进行使用、传播或向第三方披露。
 *
 * @author 刘杰
 * @contact king.2oo8@163.com
 */

namespace SwoftComponents\SitemapPusher;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use SwoftComponents\Stdlib\Contract\ResponseInterface;

/**
 * Class Response
 *
 * @since 2.0.1
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Response implements ResponseInterface
{
    use PrototypeTrait;

    /**
     * error code
     *
     * @var int
     */
    private int $errorCode = 0;

    /**
     * error message
     *
     * @var string
     */
    private string $errorMessage = 'success';

    /**
     * result data
     *
     * @var mixed
     */
    private $result = null;

    /**
     * raw error object
     *
     * @var mixed
     */
    private $rawData = null;

    /**
     * 获取一个新 Response 实例
     *
     * @param int|null $code
     * @param string|null $message
     * @param null $result
     * @return Response
     */
    public static function new(?int $code = null, ?string $message = null, $result = null): Response
    {
        $instance = self::__instance();
        $instance->errorCode = $code ?? $instance->errorCode;
        $instance->errorMessage = $message ?? $instance->errorMessage;
        $instance->result = $result ?? $instance->result;
        return $instance;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     * @return Response
     */
    public function setErrorCode(int $errorCode): Response
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return Response
     */
    public function setErrorMessage(string $errorMessage): Response
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return Response
     */
    public function setResult($result): Response
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param $raw
     * @return $this
     */
    public function setRawData($raw): Response
    {
        $this->rawData = $raw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

}
