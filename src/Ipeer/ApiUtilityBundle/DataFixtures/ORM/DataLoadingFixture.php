<?php

namespace Ipeer\ApiUtilityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * DataLoadingFixture
 */
abstract class DataLoadingFixture extends AbstractFixture {

    protected static $data;

    /**
     * {@inheritDoc}
     */
    public final function load(ObjectManager $manager)
    {
        $data = $this->makeData();

        for($i = 0; $i < count($data); $i++) {
            $manager->persist($data[$i]);
        }

        $manager->flush();
    }

    /**
     * @return array
     *
     */
    abstract protected function makeData();

}
