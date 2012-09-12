<?php

namespace PhpIdServer\User\Serializer;

use PhpIdServer\User\Serializer\Exception\AdapterSerializeException;
use PhpIdServer\Util\Options;
use PhpIdServer\User\User;


/**
 * The user serializer objects serializes the user object into a string suitable for saving into a session.
 * 
 */
class Serializer
{

    /**
     * Serializer options.
     * 
     * @var Options
     */
    protected $_options = NULL;

    /**
     * Serializer adapter.
     * 
     * @var \Zend\Serializer\Adapter\AdapterInterface
     */
    protected $_adapter = NULL;


    /**
     * Constructor.
     * 
     * @param array|Traversable $options
     */
    public function __construct ($options = array())
    {
        $this->_options = new Options($options);
    }


    /**
     * Serializes the user object and returns the data.
     * 
     * @param User $user
     * @throws Exception\AdapterSerializeException
     * @return string
     */
    public function serialize (User $user)
    {
        try {
            $data = $this->getAdapter()
                ->serialize($user);
        } catch (\Exception $e) {
            throw new Exception\AdapterSerializeException($e);
        }
        
        return $data;
    }


    /**
     * Unserializes user data and returns the user object.
     * 
     * @param string $data
     * @throws Exception\AdapterUnserializeException
     * @throws Exception\InvalidUnserializationException
     * @return User
     */
    public function unserialize ($data)
    {
        try {
            $user = $this->getAdapter()
                ->unserialize($data);
        } catch (\Exception $e) {
            throw new Exception\AdapterUnserializeException($e);
        }
        
        if (! ($user instanceof User)) {
            throw new Exception\InvalidUnserializationException('The result is not a User object');
        }
        
        return $user;
    }


    /**
     * Sets the serializer adapter.
     * 
     * @param \Zend\Serializer\Adapter\AdapterInterface $adapter
     */
    public function setAdapter (\Zend\Serializer\Adapter\AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
    }


    /**
     * Returns the serializer adapter.
     * 
     * @throws Exception\MissingAdapterException
     * @throws Exception\MissingOptionException
     * @return \Zend\Serializer\Adapter\AdapterInterface
     */
    public function getAdapter ()
    {
        if (! ($this->_adapter instanceof \Zend\Serializer\Adapter\AdapterInterface)) {
            $adapterConfig = $this->_options->get('adapter');
            if (! $adapterConfig) {
                throw new Exception\MissingAdapterException();
            }
            
            if (! isset($adapterConfig['name'])) {
                throw new Exception\MissingOptionException('adapter/name');
            }
            
            $name = $adapterConfig['name'];
            $options = array();
            if (isset($adapterConfig['options']) && is_array($adapterConfig['options'])) {
                $options = $adapterConfig['options'];
            }
            
            $this->_adapter = \Zend\Serializer\Serializer::factory($name, $options);
        }
        
        return $this->_adapter;
    }
}