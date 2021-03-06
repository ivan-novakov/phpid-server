<?php

namespace InoOicServer\Client\Authentication;


/**
 * The class contains the result of a client authentication.
 */
class Result extends \Zend\Stdlib\Message
{


    /**
     * Constructor. 
     * 
     * @param boolean $authenticated
     * @param string $reason
     */
    public function __construct($methodClass, $authenticated = null, $reason = 'undefined')
    {
        $this->setMetadata('class', $methodClass);
        $this->setResult($authenticated, $reason);
    }


    /**
     * Sets the result data.
     * 
     * @param boolean $authenticated
     * @param string $reason
     */
    public function setResult($authenticated, $reason = 'undefined')
    {
        $this->setMetadata(
            array(
                'authenticated' => (boolean) $authenticated,
                'reason' => $reason
            ));
    }


    /**
     * Sets the result as authenticated.
     */
    public function setAuthenticated()
    {
        $this->setResult(true);
    }


    /**
     * Sets the result as not authenticated.
     * 
     * @param string $reason
     */
    public function setNotAuthenticated($reason)
    {
        $this->setResult(false, $reason);
    }


    /**
     * Returns true, if the result is set as authenticated.
     */
    public function isAuthenticated()
    {
        return (boolean) $this->getMetadata('authenticated');
    }


    /**
     * Returns the reason, why the result is set as unauthenticated.
     * 
     * @return string
     */
    public function getNotAuthenticatedReason()
    {
        return (string) $this->getMetadata('reason');
    }


    public function getMethodClass()
    {
        return (string) $this->getMetadata('class');
    }
}