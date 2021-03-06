<?php

namespace InoOicServerTest\Context;

use InoOicServer\Context\AuthorizeContextManager;


class AuthorizeContextManagerTest extends \PHPUnit_Framework_TestCase
{


    public function testLoadContext()
    {
        $context = $this->createContextMock();
        
        $storage = $this->createStorageMock();
        $storage->expects($this->once())
            ->method('load')
            ->will($this->returnValue($context));
        
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = new AuthorizeContextManager($storage, $requestFactory);
        $this->assertSame($context, $manager->loadContext());
    }


    public function testPersistContext()
    {
        $context = $this->createContextMock();
        
        $storage = $this->createStorageMock();
        $storage->expects($this->once())
            ->method('save')
            ->with($context);
        
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = new AuthorizeContextManager($storage, $requestFactory);
        $manager->persistContext($context);
    }


    public function testUnpersistContext()
    {
        $context = $this->createContextMock();
        
        $storage = $this->createStorageMock();
        $storage->expects($this->once())
            ->method('clear');
        
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = new AuthorizeContextManager($storage, $requestFactory);
        $manager->unpersistContext();
    }


    public function testInitContextWithInitialRequest()
    {
        $request = $this->getMockBuilder('InoOicServer\OpenIdConnect\Request\Authorize\Simple')
            ->disableOriginalConstructor()
            ->getMock();
        $httpRequest = $this->createHttpRequest();
        
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        $requestFactory->expects($this->once())
            ->method('createRequest')
            ->with($httpRequest)
            ->will($this->returnValue($request));
        
        $context = $this->createContextMock();
        $context->expects($this->once())
            ->method('setRequest')
            ->with($request);
        
        $contextFactory = $this->getMock('InoOicServer\Context\AuthorizeContextFactory');
        $contextFactory->expects($this->once())
            ->method('createContext')
            ->will($this->returnValue($context));
        
        $storage = $this->createStorageMock();
        
        $manager = $this->getMockBuilder('InoOicServer\Context\AuthorizeContextManager')
            ->setConstructorArgs(array(
            $storage,
            $requestFactory,
            $contextFactory,
            $httpRequest
        ))
            ->setMethods(array(
            'isInitialHttpRequest'
        ))
            ->getMock();
        $manager->expects($this->once())
            ->method('isInitialHttpRequest')
            ->with($httpRequest)
            ->will($this->returnValue(true));
        
        $context = $manager->initContext();
    }


    public function testInitExistingContext()
    {
        $httpRequest = $this->createHttpRequest();
        
        $context = $this->createContextMock();
        
        $storage = $this->createStorageMock();
        
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = $this->getMockBuilder('InoOicServer\Context\AuthorizeContextManager')
            ->setConstructorArgs(array(
            $storage,
            $requestFactory,
            null,
            $httpRequest
        ))
            ->setMethods(array(
            'isInitialHttpRequest',
            'loadContext'
        ))
            ->getMock();
        
        $manager->expects($this->once())
            ->method('isInitialHttpRequest')
            ->with($httpRequest)
            ->will($this->returnValue(false));
        
        $manager->expects($this->once())
            ->method('loadContext')
            ->will($this->returnValue($context));
        
        $this->assertSame($context, $manager->initContext());
    }


    public function testInitContextWithMissingContext()
    {
        $httpRequest = $this->createHttpRequest();
        $context = $this->createContextMock();
        $storage = $this->createStorageMock();
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = $this->getMockBuilder('InoOicServer\Context\AuthorizeContextManager')
            ->setConstructorArgs(array(
            $storage,
            $requestFactory,
            null,
            $httpRequest
        ))
            ->setMethods(array(
            'isInitialHttpRequest',
            'loadContext',
            'createContext'
        ))
            ->getMock();
        
        $manager->expects($this->once())
            ->method('isInitialHttpRequest')
            ->with($httpRequest)
            ->will($this->returnValue(false));
        
        $manager->expects($this->once())
            ->method('loadContext')
            ->will($this->returnValue(null));
        
        $manager->expects($this->once())
            ->method('createContext')
            ->will($this->returnValue($context));
        
        $this->assertSame($context, $manager->initContext());
    }


    /**
     * @dataProvider dataForIsExpiredContext
     */
    public function testIsExpiredContext($authTimeString, $nowTimeString, $expirationPeriod, $result)
    {
        $authTime = new \DateTime($authTimeString);
        $nowTime = new \DateTime($nowTimeString);
        
        $authenticationInfo = $this->getMockBuilder('InoOicServer\Authentication\Info')
            ->setMethods(array(
            'getTime'
        ))
            ->getMock();
        $authenticationInfo->expects($this->once())
            ->method('getTime')
            ->will($this->returnValue($authTime));
        
        $context = $this->createContextMock();
        $context->expects($this->once())
            ->method('getAuthenticationInfo')
            ->will($this->returnValue($authenticationInfo));
        
        $storage = $this->createStorageMock();
        $requestFactory = $this->createAuthorizeRequestFactoryMock();
        
        $manager = new AuthorizeContextManager($storage, $requestFactory);
        $manager->setTimeout($expirationPeriod);
        
        $this->assertSame($result, $manager->isExpiredContext($context, $nowTime));
    }
    
    /*
     * -----------------------------------------------------------------------------
     */
    public function dataForIsExpiredContext()
    {
        return array(
            array(
                'auth_time' => '2014-01-30 10:00:00',
                'now_time' => '2014-01-30 10:31:00',
                'expiration_period' => '1800',
                'result' => true
            ),
            array(
                'auth_time' => '2014-01-30 10:00:00',
                'now_time' => '2014-01-30 10:29:00',
                'expiration_period' => '1800',
                'result' => false
            )
        );
    }


    protected function createContextMock()
    {
        $context = $this->getMockBuilder('InoOicServer\Context\AuthorizeContext')
            ->disableOriginalConstructor()
            ->getMock();
        
        return $context;
    }


    protected function createStorageMock()
    {
        $storage = $this->getMock('InoOicServer\Context\Storage\StorageInterface');
        return $storage;
    }


    protected function createAuthorizeRequestFactoryMock()
    {
        $factory = $this->getMock('InoOicServer\OpenIdConnect\Request\Authorize\RequestFactory');
        return $factory;
    }


    protected function createHttpRequest()
    {
        return $this->getMock('Zend\Http\Request');
    }
}