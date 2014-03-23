<?php
    use \Phork\Core\Loader;
    use \Phork\Test\LoaderCleanPath;
    
    /**
     * @covers \Phork\Core\Loader
     */
    class LoaderTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            destroy_bootstrap();
        }
        
        /**
         * @covers \Phork\Core\Loader::__construct
         */
        public function testInitialize() 
        {
            $loader = Loader::instance();
            \Phork::instance()->register('loader', $loader, true);
            
            $this->assertInstanceOf('Phork\Core\Loader', $loader);
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::autoload
         */
        public function testAutoloader($loader)
        {
            $initial = count(spl_autoload_functions());
            
            $loader->autoload(true);
            $this->assertEquals($initial + 1, count(spl_autoload_functions()));
            
            $loader->autoload(true);
            $this->assertEquals($initial + 1, count(spl_autoload_functions()));
            
            $loader->autoload(false);
            $this->assertEquals($initial, count(spl_autoload_functions()));
            
            $loader->autoload(false);
            $this->assertEquals($initial, count(spl_autoload_functions()));
            
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::mapPath
         * @covers \Phork\Core\Loader::getPath
         */
        public function testMapPath($loader)
        {
            $loader
                ->mapPath('Core', CORE_PATH)
                ->mapPath('App',  APP_PATH)
                ->mapPath('View', VIEW_PATH)
                ->mapPath('Pkg',  PKG_PATH)
                ->mapPath('Test', TEST_PATH)
            ;
            
            $this->assertEquals($loader->getPath('Core'), CORE_PATH);
            $this->assertEquals($loader->getPath('App'), APP_PATH);
            $this->assertEquals($loader->getPath('View'), VIEW_PATH);
            $this->assertEquals($loader->getPath('Pkg'), PKG_PATH);
            $this->assertEquals($loader->getPath('Test'), TEST_PATH);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::getPath
         * @expectedException PhorkException
         */
        public function testgetPathException($loader)
        {
            $loader->getPath('Xxx');
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::mapClass
         */
        public function testMapClass($loader)
        {
            $loader->mapClass('Foo', $this->getHelperPath().'classes/Foo.php');
            $loader->mapClass('FooInterface', $this->getHelperPath().'classes/FooInterface.php');
            
            $loader->loadClass('Foo');
            $this->assertTrue(class_exists('Foo'));
            
            $loader->loadClass('FooInterface');
            $this->assertTrue(interface_exists('FooInterface'));
            
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::loadClass
         */
        public function testLoadClass($loader)
        {
            $this->assertTrue(class_exists('Phork\Core\Bootstrap', false));
            $result = $loader->loadClass('Phork\Core\Bootstrap');
            $this->assertTrue($result);
            
            $this->assertFalse(class_exists('Phork\App\Bootstrap', false));
            $result = $loader->loadClass('Phork\App\Bootstrap');
            $this->assertTrue($result);
            $this->assertTrue(class_exists('Phork\Core\Language', false));
            
            $this->assertFalse(class_exists('Phork\Pkg\Auth\Auth', false));
            $result = $loader->loadClass('Phork\Pkg\Auth\Auth');
            $this->assertTrue($result);
            $this->assertTrue(class_exists('Phork\Pkg\Auth\Auth', false));
            
            $result = $loader->loadClass('Phork\Core\Bootstrap');
            $this->assertTrue($result);
            
            $this->assertTrue(interface_exists('FooInterface'));
            $result = $loader->loadClass('FooInterface');
            $this->assertTrue($result);
            
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::loadClass
         * @expectedException PhorkException
         */
        public function testLoadPhorkClassException($loader)
        {
            $this->assertFalse(class_exists('Phork\Xxx\Bootstrap', false));
            $loader->loadClass('Phork\Xxx\Bootstrap');
            
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::loadClass
         * @expectedException PhorkException
         */
        public function testLoadClassException($loader)
        {
            $loader->loadClass('Xxx', true);
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::loadClass
         */
        public function testLoadClassFail($loader)
        {
            $result = $loader->loadClass('Xxx', false);
            $this->assertNull($result);
            return $loader;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::addStack
         */
        public function testAddStack($loader)
        {
            $loader->addStack('loader', array(
                'App',
                array('Bar', $this->getHelperPath().'classes/Bar/'),
                array('Baz', $this->getHelperPath().'classes/Baz/')
            ));
            
            return $loader;
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::addStack
         * @expectedException PhorkException
         */
        public function testAddStackException($loader)
        {
            $loader->addStack('loader', array(
                array('Bar', $this->getHelperPath().'classes/Bar/'),
                array('Baz', $this->getHelperPath().'classes/Baz/')
            ));
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::getStack
         */
        public function testGetStack($loader)
        {
            $result = $loader->getStack('loader');
            $this->assertTrue(is_array($result));
            $this->assertEquals(3, count($result));
            
            $result = $loader->getStack('xxx', false);
            $this->assertNull($result);
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::getStack
         * @expectedException PhorkException
         */
        public function testGetStackException($loader)
        {
            $loader->getStack('xxx', true);
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::listStack
         */
        public function testListStack($loader)
        {
            $result = $loader->listStack('loader', 'Bar', '');
            $this->assertTrue(is_array($result));
            $this->assertEquals(1, count($result));
            $this->assertEquals($result['Bar'], realpath($this->getHelperPath().'classes/Bar/Bar.php'));
            
            $result = $loader->listStack('loader', 'Baz', '');
            $this->assertTrue(is_array($result));
            $this->assertEquals(0, count($result));
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::loadStack
         */
        public function testLoadStack($loader)
        {
            $result = $loader->loadStack('loader', 'Bar', null, '', false);
            $this->assertEquals(array('Bar' => 1), $result);
            
            $result = $loader->loadStack('loader', 'Bar', function($result, $type) { return $type; }, '', false);
            $this->assertEquals('Bar', $result);
            
            $result = $loader->loadStack('loader', 'Bar', function($result, $type) { return $type; }, '', true);
            $this->assertEquals(array('Bar' => 'Bar'), $result);
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::removeStack
         */
        public function testRemoveStack($loader)
        {
            $result = $loader->removeStack('loader');
            $this->assertTrue(is_array($result));
            $this->assertEquals(3, count($result));
            
            $result = $loader->removeStack('xxx', false);
            $this->assertNull($result);
        }
        
        /**
         * @depends testAddStack
         * @covers \Phork\Core\Loader::removeStack
         * @expectedException PhorkException
         */
        public function testRemoveStackException($loader)
        {
            $loader->removeStack('xxx', true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::loadFile
         */
        public function testLoadFile($loader)
        {
            $result = $loader->loadFile($this->getHelperPath().'blankfile.php', false);
            $this->assertEquals(1, $result);
            
            $result = $loader->loadFile($this->getHelperPath().'blankfile.php', true);
            $this->assertEquals(1, $result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::validateFile
         */
        public function testValidateFile($loader)
        {
            $result = $loader->validateFile($file = $this->getHelperPath().'blankfile.php');
            $this->assertEquals(realpath($file), $result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::validateFile
         * @expectedException PhorkException
         */
        public function testValidateFileException($loader)
        {
            $result = $loader->validateFile($this->getHelperPath().'xxx.php', false);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::validateFile
         */
        public function testValidateFileRestrict($loader)
        {
            $result = $loader->validateFile($file = $this->getHelperPath().'blankfile.php', $this->getHelperPath());
            $this->assertEquals(realpath($file), $result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::validateFile
         * @expectedException PhorkException
         */
        public function testValidateFileRestrictException($loader)
        {
            $result = $loader->validateFile($file = $this->getHelperPath().'blankfile.php', CORE_PATH);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::validateFile
         * @expectedException PhorkException
         */
        public function testValidateFileInvalidRestrictException($loader)
        {
            $result = $loader->validateFile($file = $this->getHelperPath().'blankfile.php', $this->getHelperPath().'xxx');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::isFile
         */
        public function testIsFile($loader)
        {
            $result = $loader->isFile($file = $this->getHelperPath().'blankfile.php');
            $this->assertEquals($result, realpath($file));

            $result = $loader->isFile($file = $this->getHelperPath().'xxx.php');
            $this->assertFalse($result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::isTemplate
         */
        public function testIsTemplate($loader)
        {
            $result = $loader->isTemplate($file = $this->getHelperPath().'index.php');
            $this->assertEquals($result, realpath($file));
            
            $result = $loader->isTemplate($file = $this->getHelperPath().'xxx.php');
            $this->assertFalse($result);

        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Loader::__destruct
         */
        public function testDestruct($loader)
        {
            $loader->__destruct();
        }
        
        /**
         * @covers \Phork\Core\Loader::cleanPath
         * @covers \Phork\Core\Loader::__destruct
         */
        public function testCleanPath()
        {
            $loader = LoaderCleanPath::instance();
            $result = $loader->publicCleanPath('foo/bar', false);
            $this->assertEquals('foo'.DIRECTORY_SEPARATOR.'bar', $result);
            
            $loader->__destruct();
        }
        
        /**
         * @coversNothing
         */
        public function getHelperPath()
        {
            return __DIR__.'/../helpers/loader/';
        }
    }
