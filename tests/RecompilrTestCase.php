<?php
namespace recompilr\Tests {
    /**
    * Base Recompilr test case class
    * Class RecompilrTestCase
    * @package recompilr
    */
    abstract class RecompilrTestCase extends \PHPUnit_Framework_TestCase {
        protected $dir;
        /**
         * Perform setUp tasks
         */
        protected function setUp()
        {
            $this->dir = __DIR__.DIRECTORY_SEPARATOR.'Mock'.DIRECTORY_SEPARATOR;
        }

        /**
         * Perform clean up / tear down tasks
         */
        protected function tearDown()
        {
        }
    }
}