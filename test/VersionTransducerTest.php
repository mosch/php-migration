<?php
require './src/MigrationInterface.php';
require './src/VersionProviderInterface.php';
require './src/VersionTransducer.php';

use Migration\VersionTransducer;
use Migration\VersionProviderInterface;
use Migration\MigrationInterface;

class TestVersionProvider implements VersionProviderInterface
{
    private $version;

    public function __construct($startAt = null)
    {
        $this->version = '2016-01-01-230556';
    }

    public function getCurrentVersion()
    {
        return $this->version;
    }

    public function setCurrentVersion($version)
    {
        $this->version = $version;
    }
}

class TestMigration1 implements MigrationInterface
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }


    public function getVersionName()
    {
        return $this->version;
    }

    public function migrate()
    {
    }
}

class VersionTransducerTest extends PHPUnit_Framework_TestCase
{
    public function testPushAndPop()
    {
        $a = new VersionTransducer(new TestVersionProvider());
        $a->addMigration(new TestMigration1('2016-01-01-230556'));
        $a->addMigration($this->createMigration('2016-01-01-230557'));
        $a->addMigration($this->createMigration('2016-01-01-230558'));
        $a->addMigration($this->createMigration('2016-01-01-230559'));

        $a->migrateUp();
    }

    private function createMigration($versionName)
    {
        $observer = $this->getMockBuilder('TestMigration1')
                        ->setConstructorArgs(array($versionName))
                        ->setMethods(array('migrate'))
                        ->getMock();

        $observer->expects($this->once())
                ->method('migrate');

        return $observer;
    }
}
