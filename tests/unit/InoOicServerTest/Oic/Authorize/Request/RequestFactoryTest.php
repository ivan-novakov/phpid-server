<?php

namespace InoOicServerTest\Oic\Authorize\Request;

use Zend\Http;
use InoOicServer\Oic\Authorize\Request\RequestFactory;


class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{


    public function testSetOptionAuthCookieName()
    {
        $authCookieName = 'foo';
        
        $factory = new RequestFactory(array(
            'auth_cookie_name' => $authCookieName
        ));
        
        $this->assertSame($authCookieName, $factory->getOption('auth_cookie_name'));
    }


    public function testCreateRequest()
    {
        $authCookieName = 'foocookie';
        $authSessionId = '123abc';
        $params = array(
            'client_id' => 'testclient',
            'redirect_uri' => 'https://redirect/',
            'response_type' => 'foo',
            'scope' => 'bar',
            'state' => '123456'
        );
        
        $httpRequest = new Http\Request();
        $httpRequest->getQuery()->fromArray($params);
        $httpRequest->getHeaders()->addHeaders(array(
            'Cookie' => $authCookieName . '=' . $authSessionId
        ));
        
        $factory = new RequestFactory(array(
            RequestFactory::OPT_AUTH_COOKIE_NAME => $authCookieName
        ));
        $request = $factory->createRequest($httpRequest);
        
        $this->assertInstanceOf('InoOicServer\Oic\Authorize\Request\Request', $request);
        $this->assertSame($params['client_id'], $request->getClientId());
        $this->assertSame($params['redirect_uri'], $request->getRedirectUri());
        $this->assertSame($params['response_type'], $request->getResponseType());
        $this->assertSame($params['scope'], $request->getScope());
        $this->assertSame($params['state'], $request->getState());
        $this->assertSame($httpRequest, $request->getHttpRequest());
        
        $this->assertSame($authSessionId, $request->getAuthenticationSessionId());
    }
}