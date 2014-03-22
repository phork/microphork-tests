<?php
    namespace Controllers;
    
    use \Phork\Core\Controllers\Standard;
    use \Phork\Test\Controllers\StandardDefault;
    use \Phork\Test\Controllers\StandardRestful;
    
    /**
     * @covers \Phork\Core\Controllers\Standard
     */
    class StandardTest extends \PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            init_bootstrap();
            \Phork::instance()->init(PHK_ENV);
        }
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            destroy_bootstrap();
        }
        
        /**
         * @coversNothing
         */
        public function testInitializeDefault() 
        {
            $standard = new StandardDefault();
            $this->assertInstanceOf('Phork\Core\Controllers\Standard', $standard);
            return $standard;
        }
        
        /**
         * @depends testInitializeDefault
         * @covers \Phork\Core\Controllers\Standard::run
         * @covers \Phork\Core\Controllers\Standard::displayIndex
         */
        public function testRunDefault($standard) 
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            \Phork::router()->init('get', '', array());
            
            $standard->run();
            $this->expectOutputString('hello world!');
        }
        
        /**
         * @depends testInitializeDefault
         * @covers \Phork\Core\Controllers\Standard::run
         * @covers \Phork\Core\Controllers\Standard::displayFatal
         */
        public function testRunDefault404($standard) 
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            \Phork::router()->init('get', '/xxx/xxx', array());
            
            $standard->run();
            $this->expectOutputString('error');
        }
        
        /**
         * @coversNothing
         */
        public function testInitializeRestful() 
        {
            $standard = new StandardRestful();
            $this->assertInstanceOf('Phork\Core\Controllers\Standard', $standard);
            return $standard;
        }
        
        /**
         * @depends testInitializeRestful
         * @covers \Phork\Core\Controllers\Standard::run
         * @covers \Phork\Core\Controllers\Standard::displayIndex
         */
        public function testRunGetRestful($standard) 
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            \Phork::router()->init('get', '', array());
            
            $standard->run();
            $this->expectOutputString('hello world!');
        }
        
        /**
         * @depends testInitializeRestful
         * @covers \Phork\Core\Controllers\Standard::run
         * @covers \Phork\Core\Controllers\Standard::displayIndex
         */
        public function testRunPostRestful($standard) 
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            \Phork::router()->init('post', '', array());
            
            $standard->run();
            $this->expectOutputString('hello world!');
        }
        
        /**
         * @depends testInitializeRestful
         * @covers \Phork\Core\Controllers\Standard::run
         * @covers \Phork\Core\Controllers\Standard::displayIndex
         */
        public function testRunDeleteRestful($standard) 
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            \Phork::router()->init('delete', '', array());
            
            $standard->run();
            $this->expectOutputString('error');
        }
        
        /**
         * @coversNothing
         */
        public function getHelperPath()
        {
            return __DIR__.'/../../helpers/controllers/';
        }
    }
