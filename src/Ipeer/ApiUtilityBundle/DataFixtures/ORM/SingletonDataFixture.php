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
 * So we create a separate final static method, getData, that will return this sample data
 * It acts as a singleton (guard), so if the objects are already in memory (eg. within the same test case) it returns the existing ones
 *
 * Then the actual sample data gets defined in makeData(), which in turn should only be called once
 * Accessing makeData() through getData() prevents duplicate objects from occupying memory
 *
 * We make load() and getData() final, so the concrete fixtures are forced to use makeData() to accomplish the above goals
 * In addition makeData() is protected, so the test cases are forced to use getData()
 *
 */
abstract class SingletonDataFixture extends AbstractFixture implements FixtureInterface {

    /**
     * @var array
     */
    private static $data = array();

    /**
     * {@inheritDoc}
     */
    public final function load(ObjectManager $manager)
    {
        $data = $this->getData();

        for($i = 0; $i < count($data); $i++) {
            $manager->persist($data[$i]);
        }

        $manager->flush();
    }

    /**
     * @return array
     *
     * This function should only get called once (singleton via getData)
     * It should be extended to actually create the data
     */
    abstract protected function makeData();

    /**
     * @return array
     *
     * If the data has not been created, create it
     * Otherwise return it
     *
     * See note on the SingletonFixture class
     */
    public final static function getData() {
        if(null == self::$data || count(self::$data) === 0) {
            $childFixture = new static();
            self::$data = $childFixture->makeData(); // use static:: to call overridden makeData() in child classes
        }
        return self::$data;
    }
}