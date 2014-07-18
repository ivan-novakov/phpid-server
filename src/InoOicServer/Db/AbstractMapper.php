<?php

namespace InoOicServer\Db;

use DateTime;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Adapter\AdapterInterface as DbAdapter;
use InoOicServer\Oic\EntityFactoryInterface;
use InoOicServer\Oic\EntityInterface;
use Zend\Db\Sql\PreparableSqlInterface;


abstract class AbstractMapper
{

    /**
     * @var DbAdapter
     */
    protected $dbAdapter;

    /**
     * @var EntityFactoryInterface
     */
    protected $factory;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var Sql
     */
    protected $sql;


    /**
     * Constructor.
     *
     * @param DbAdapter $dbAdapter
     */
    public function __construct(DbAdapter $dbAdapter, EntityFactoryInterface $factory, HydratorInterface $hydrator)
    {
        $this->setDbAdapter($dbAdapter);
        $this->setFactory($factory);
        $this->setHydrator($hydrator);
    }


    /**
     * @return DbAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }


    /**
     * @param DbAdapterr $dbAdapter
     */
    public function setDbAdapter(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }


    /**
     * @return EntityFactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }


    /**
     * @param EntityFactoryInterface $factory
     */
    public function setFactory(EntityFactoryInterface $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }


    /**
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }


    /**
     * @return Sql
     */
    public function getSql()
    {
        if (! $this->sql instanceof Sql) {
            $this->sql = new Sql($this->getDbAdapter());
        }

        return $this->sql;
    }


    /**
     * Returns true if an entity with the provided identifier exists in the storage.
     *
     * @param mixed $identifier
     * @return boolean
     */
    abstract public function existsEntity($identifier);


    /**
     * Creates a new entity instance and hydrates it with the provided data.
     *
     * @param array $entityData
     * @return EntityInterface
     */
    public function createEntityFromData(array $entityData)
    {
        return $this->getFactory()->createEntityFromData($entityData);
    }


    /**
     * Executes a query expecting one or zero results. In case of a result, creates a new entity
     * and hydrates it with tha data.
     *
     * @param Select $select
     * @param array $params
     * @throws Exception\InvalidResultException
     * @return \InoOicServer\Oic\EntityInterface|null
     */
    public function executeSingleEntityQuery(Select $select, array $params = array())
    {
        $results = $this->executeSelect($select, $params);

        if (! $results->count()) {
            return null;
        }

        if ($results->count() > 1) {
            throw new Exception\InvalidResultException(sprintf("Expected only one record, %d records has been returned", $results->count()));
        }

        $entity = $this->createEntityFromData($results->current());

        return $entity;
    }


    /**
     * Creates a statement from the select object and executes it. Returns the result.
     *
     * @param Select $select
     * @param array $params
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function executeSelect(Select $select, array $params = array())
    {
        $statement = $this->getSql()->prepareStatementForSqlObject($select);
        $results = $statement->execute($params);

        return $results;
    }


    /**
     * Saves the data to the storage. If the entity does not exist, a new record will be created.
     *
     * @param unknown $entityIdentifier
     * @param unknown $entityTable
     * @param array $entityData
     */
    protected function createOrUpdateEntity($entityIdentifier, $entityTable, array $entityData)
    {
        if ($this->existsEntity($entityIdentifier)) {
            $sqlObject = $this->getSql()->update();
            $sqlObject->table($entityTable);
            $sqlObject->set($entityData);
        } else {
            $sqlObject = $this->getSql()->insert();
            $sqlObject->into($entityTable);
            $sqlObject->values($entityData);
        }

        $this->executeSqlObject($sqlObject);
    }


    protected function executeSqlObject(PreparableSqlInterface $sqlObject, array $params = array())
    {
        $statement = $this->getSql()->prepareStatementForSqlObject($sqlObject);
        $statement->execute($params);
    }
}