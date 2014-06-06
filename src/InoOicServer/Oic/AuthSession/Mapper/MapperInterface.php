<?php

namespace InoOicServer\Oic\AuthSession\Mapper;

use InoOicServer\Oic\AuthSession\AuthSession;


interface MapperInterface
{


    /**
     * @param AuthSession $authSession
     */
    public function save(AuthSession $authSession);


    /**
     * @param string $id
     * @return AuthSession|null
     */
    public function fetch($id);
}