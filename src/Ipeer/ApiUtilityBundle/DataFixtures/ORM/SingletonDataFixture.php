<?php

namespace Ipeer\ApiUtilityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * SingletonFixture
 *
 * This class is meant to be a workaround to how caching works in liip_functional_test: (cache_sqlite_db: true)
 *
 * load() gets called once, and would normally contain the definitions of the sample data/objects
 * Documentation suggests allowing the sample data objects be accessible through a static array on the class
 * However, since after the first test load() has been cached, any static assignments made in load() don't get run
 * This is a problem because the static properties of the fixture get reset each test case
 * As a result, the sample data is no longer accessible and "gets lost"
 *
 *
 */
abstract class SingletonDataFixture extends AbstractFixture implements FixtureInterface {

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var SingletonDataFixture
     */
    protected static $instance;

    /**
     * {@inheritDoc}
     */
    public final function load(ObjectManager $manager)
    {
        static::$instance = $this;
        $data = self::getInstance()->data;

        for($i = 0; $i < count($data); $i++) {
            $manager->persist($data[$i]);
        }

        $manager->flush();
    }

    /**
     * @return array
     *
     * This function should only get called once per case (singleton via getInstance)
     */
    abstract protected function makeData();

    /**
     * @return SingletonDataFixture
     */
    private final static function getInstance()
    {
        if (null === static::$instance) {
            $ref = new static();
            $ref->data = $ref->makeData($ref);
            static::$instance = $ref;
        }

        if(count(static::$instance->data) < 1) {
            static::$instance->data = static::$instance->makeData();
        }

        return static::$instance;
    }

    /**
     * @return array
     */
    public final static function getData() {
        return self::getInstance()->data;
    }

    /**
     * @param string $name
     * @param object $object
     */
    public function setReference($name, $object) {
        if(null !== $this->referenceRepository) {
            // this is being executed during a load()
            parent::setReference($name, $object);
        }
    }
}