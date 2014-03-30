<?php
    namespace Controllers;
    
    use \Phork\Core\Controllers\Redirect;
    
    /**
     * @covers \Phork\Core\Controllers\Redirect
     */
    class RedirectTest extends \PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            init_bootstrap();
            \Phork::instance()->init(PHK_ENV);
            \Phork::config()->router->urls->set('base', '');
            
            \Phork::router()->setRoutes(array(
                '^/permanent/?' => '/redirect/status=301/home/permanent/',
                '^/standard/?' => '/redirect/home/standard/'
            ));
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
        public function testInitialize() 
        {
            $redirect = new Redirect();
            $this->assertInstanceOf('Phork\Core\Controllers\Redirect', $redirect);
            return $redirect;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Redirect::run
         */
        public function testRunPermanent($redirect) 
        {
            \Phork::instance()->register('output', \Phork\Test\OutputPrintHeaders::instance());
            \Phork::router()->init('get', '/permanent/', array());
            
            $redirect->run();
            $this->expectOutputString('HTTP/1.0 301 Moved Permanently' . 'location: /home/permanent/');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Redirect::run
         */
        public function testRunStandard($redirect) 
        {
            \Phork::instance()->register('output', \Phork\Test\OutputPrintHeaders::instance());
            \Phork::router()->init('get', '/standard/', array());
            
            $redirect->run();
            $this->expectOutputString('HTTP/1.0 200 OK' . 'location: /home/standard/');
        }
    }
