<?php

namespace InoOicServer\Oic\AuthCode;

use InoOicServer\Util\ConvertToDateTimeTrait;
use DateTime;


/**
 * The OIC authentication code entity.
 */
class AuthCode
{
    
    use ConvertToDateTimeTrait;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var DateTime
     */
    protected $createTime;

    /**
     * @var DateTime
     */
    protected $expirationTime;

    /**
     * @var string
     */
    protected $scope;


    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }


    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }


    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }


    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }


    /**
     * @return DateTime
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


    /**
     * @param string|DateTime $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $this->convertToDateTime($createTime);
    }


    /**
     * @return DateTime
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }


    /**
     * @param string|DateTime $expirationTime
     */
    public function setExpirationTime($expirationTime)
    {
        $this->expirationTime = $this->convertToDateTime($expirationTime);
    }


    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }


    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }
}