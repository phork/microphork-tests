<?php
    use \Phork\Core\Bootstrap;
    use \Phork\Test\OutputNoHeaders;
    
    /**
     * @covers \Phork\Core\Bootstrap
     */
    class BootstrapTest extends PHPUnit_Framework_TestCase 
    {
        static protected $singletons = array();
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            //re-register all the singletons so destroy_bootstrap can destroy them properly
            foreach (static::$singletons as $name=>$object) {
                \Phork::instance()->register($name, $object, true);    
            }
            destroy_bootstrap();
        }
        
        /**
         * @covers \Phork\Core\Bootstrap::__construct
         * @covers \Phork\Core\Bootstrap::register
         */
        public function testInitialize() 
        {
            init_bootstrap();
            return \Phork::instance();
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::dereference
         * @expectedException PhorkException
         */
        public function testDereference($bootstrap)
        {
            $bootstrap->dereference();
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::register
         * @expectedException PhorkException
         */
        public function testRegisterException($bootstrap)
        {
            $bootstrap->register('xxx', new StdClass());
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::deregister
         * @expectedException PhorkException
         */
        public function testDeregisterException($bootstrap)
        {
            $bootstrap->deregister('xxx');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::register
         * @covers \Phork\Core\Bootstrap::deregister
         * @covers \Phork\Core\Bootstrap::__isset
         * @covers \Phork\Core\Bootstrap::__get
         * @covers \Phork\Core\Bootstrap::__callStatic
         */
        public function testRegistry($bootstrap)
        {
            $foo = new StdClass();
            
            $result = $bootstrap->register('foo', $foo, true);
            $this->assertInstanceOf('\Phork\Core\Bootstrap', $result);
            
            $result = empty($bootstrap->foo);
            $this->assertFalse($result);
            
            $result = $bootstrap->foo;
            $this->assertEquals($foo, $result);
            
            $result = \Phork::foo();
            $this->assertEquals($foo, $result);
            
            $result = $bootstrap->deregister('foo');
            $this->assertEquals($foo, $result);
            
            $result = empty($bootstrap->foo);
            $this->assertTrue($result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::__get
         * @expectedException PhorkException
         */
        public function testRegistryException($bootstrap)
        {
            $bootstrap->xxx;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::__callStatic
         * @expectedException PhorkException
         */
        public function testRegistryStaticException($bootstrap)
        {
            \Phork::xxx();
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::init
         */
        public function testInit($bootstrap)
        {
            $bootstrap->init(PHK_ENV);
            
            $this->assertInstanceOf('\Phork\Core\Config', $bootstrap->config);
            $this->assertInstanceOf('\Phork\Core\Event', $bootstrap->event);
            $this->assertInstanceOf('\Phork\Core\Error', $bootstrap->error);
            $this->assertInstanceOf('\Phork\Core\Language', $bootstrap->language);
            $this->assertInstanceOf('\Phork\Core\Router', $bootstrap->router);
            $this->assertInstanceOf('\Phork\Core\Output', $bootstrap->output);
            
            static::$singletons['event'] = $bootstrap->event;
            static::$singletons['output'] = $bootstrap->output;
            static::$singletons['loader'] = $bootstrap->loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::initPackage
         */
        public function testInitPackage($bootstrap)
        {
            $result = $bootstrap->initPackage('Debug', null, false);
            $this->assertEquals('\Phork\Pkg\Debug\Debug', $result);
            
            $package = new $result();
            $this->assertInstanceOf('\Phork\Pkg\Debug\Debug', $package);
            
            $this->assertInstanceOf('Phork\Core\Config', \Phork::config()->debug);
            
            $result = $bootstrap->initPackage('Debug');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::initPackage
         */
        public function testInitPackageCallback($bootstrap)
        {
            $result = $bootstrap->initPackage('Debug', function($result, $type) {
                return $type;
            });
            
            $this->assertEquals('Pkg', $result);    
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::fatal
         */
        public function testRun($bootstrap)
        {
            //replace the output class with one that doesn't send headers
            $bootstrap->deregister('output');
            $bootstrap->register('output', OutputNoHeaders::instance(true), true);
            
            //replace the router with a new one with faked data
            $class = get_class($bootstrap->router);
            $router = new $class('');
            $router->init('get', 'home/index');
            $bootstrap->register('router', $router);
            
            ob_start();
            $bootstrap->run();
            $result = ob_get_clean();
            
            $this->assertTrue(is_string($result));
            $this->assertStringStartsWith('<!DOCTYPE html>', $result);
            
            return $bootstrap;
        }
        
        /**
         * @depends testRun
         * @covers \Phork\Core\Bootstrap::fatal
         */
        public function testRunFatal($bootstrap)
        {
            //replace the router with a new one with faked data
            $class = get_class($bootstrap->router);
            $router = new $class('');
            $router->init('get', 'xxx/index');
            $bootstrap->register('router', $router);
        
            ob_start();
            $bootstrap->run();
            $result = ob_get_clean();
            
            $this->assertTrue(is_string($result));
            $this->assertStringStartsWith('404', $result);
        }
        
        /**
         * @depends testRun
         * @covers \Phork\Core\Bootstrap::fatal
         */
        public function testFatal($bootstrap)
        {
            ob_start();
            $bootstrap->fatal(404);
            $result = ob_get_clean();
            
            $this->assertTrue(is_string($result));
            $this->assertStringStartsWith('404', $result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Bootstrap::shutdown
         * @covers \Phork\Core\Bootstrap::__isset 
         */
        public function testShutdown($bootstrap)
        {
            $bootstrap->shutdown();
            $this->assertTrue(empty($bootstrap->controller));
            $this->assertTrue(empty($bootstrap->output));
            $this->assertTrue(empty($bootstrap->loader));
            $this->assertTrue(empty($bootstrap->router));
            $this->assertTrue(empty($bootstrap->event));
            $this->assertTrue(empty($bootstrap->config));
            $this->assertTrue(empty($bootstrap->language));
        }
    }
