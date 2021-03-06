<?php

namespace InoOicServer\OpenIdConnect\Request;


abstract class AbstractRequest implements RequestInterface
{

    /**
     * The HTTP request object.
     * @var \Zend\Http\Request
     */
    protected $httpRequest;

    /**
     * List of reasons, why the request is invalid.
     * @var array
     */
    protected $invalidReasons;


    /**
     * Constructor.
     *
     * @param \Zend\Http\Request $httpRequest            
     */
    public function __construct(\Zend\Http\Request $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }


    /**
     * Returns the underlying HTTP request object.
     * 
     * @return \Zend\Http\Request
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }


    /**
     * Returns true, if the request is a POST request.
     * 
     * @return boolean
     */
    public function isPostRequest()
    {
        return (\Zend\Http\Request::METHOD_POST == $this->httpRequest->getMethod());
    }


    /**
     * Returns true, if the request is valid.
     * 
     * @return boolean
     */
    public function isValid()
    {
        return (count($this->getInvalidReasons()) == 0);
    }


    /**
     * Returns an array of messages describing why the request is invalid.
     * 
     * @return array
     */
    public function getInvalidReasons()
    {
        if (! is_array($this->invalidReasons)) {
            $this->invalidReasons = $this->validate();
        }
        
        return $this->invalidReasons;
    }


    /**
     * Validates the request and returns an array of reason messages.
     * 
     * @return array
     */
    protected function validate()
    {
        return array();
    }


    /**
     * Returns HTTP GET or POST argument value based on the current request method.
     * 
     * @param string $name
     * @return mixed
     */
    protected function _getParam($name)
    {
        if ($this->isPostRequest()) {
            return $this->_getPostParam($name);
        }
        
        return $this->_getGetParam($name);
    }


    /**
     * Returns the POST parameter with the provided name.
     * 
     * @param string $name
     * @return string
     */
    protected function _getPostParam($name)
    {
        return $this->httpRequest->getPost($name);
    }


    /**
     * Returns the GET parameter with the supplied name.
     * 
     * @param string $name
     * @return string
     */
    protected function _getGetParam($name)
    {
        return $this->httpRequest->getQuery($name);
    }


    /**
     * Returns the required HTTP header.
     * 
     * @param string $name
     * @return \Zend\Http\Header\HeaderInterface|\ArrayIterator|null
     */
    protected function _getHeader($name)
    {
        return $this->httpRequest->getHeader($name);
    }
}