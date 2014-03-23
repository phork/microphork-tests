<?php
    use \Phork\Core\Config;
    
    /**
     * @covers \Phork\Core\Config
     */
    class ConfigTest extends PHPUnit_Framework_TestCase 
    {
        protected $data = array(
            'foo' => array(
                'bar' => array(
                    'baz' => 42
                )
            )
        );
        
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            init_bootstrap();
        }
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            destroy_bootstrap();
        }
        
        /**
         * @covers \Phork\Core\Config::__construct
         */
        public function testInitialize() 
        {
            $config = new Config();
            $this->assertInstanceOf('Phork\Core\Config', $config);
            return $config;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Config::import
         * @covers \Phork\Core\Config::get
         */
        public function testImport($config)
        {
            $config->import($this->data);
            
            $this->assertInstanceOf('Phork\Core\Config', $config->foo);
            $this->assertInstanceOf('Phork\Core\Config', $config->foo->bar);
            $this->assertEquals(42, $config->foo->bar->baz);
            
            return $config;
        }
        
        /**
         * @depends testImport
         * @covers \Phork\Core\Config::export
         */
        public function testExport($config)
        {
            $export = $config->export();
            $this->assertEquals($export, $this->data);
            return $config;
        }
        
        /**
         * @depends testImport
         * @covers \Phork\Core\Config::get
         */
        public function testGet($config)
        {
            $this->assertEquals(42, $config->foo->bar->get('baz'));
            $this->assertEquals(13, $config->foo->bar->get('qux', 13));
            return $config;
        }
        
        /**
         * @depends testGet
         * @covers \Phork\Core\Config::get
         */
        public function testGetIgnore($config)
        {
            $result = $config->get('xxx', null, false);
            $this->assertNull($result);
            
            return $config;
        }
        
        /**
         * @depends testGet
         * @covers \Phork\Core\Config::get
         * @expectedException PhorkException
         */
        public function testGetException($config)
        {
            $result = $config->xxx;
            return $config;
        }
        
        /**
         * @depends testGet
         * @covers \Phork\Core\Config::get
         */
        public function testGetError($config)
        {
            $result = $config->get('xxx', null, false);
            $this->assertNull($result);
            
            return $config;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Config::set
         * @covers \Phork\Core\Config::get
         */
        public function testSet($config)
        {
            $config->set('qux', 13);
            $this->assertEquals(13, $config->qux);
            
            $config->set('qux', array(1, 2, 3));
            $this->assertCount(3, $config->qux->export());
            $this->assertEquals(1, $config->qux->get(0));
            $this->assertEquals(2, $config->qux->get(1));
            $this->assertEquals(3, $config->qux->get(2));
            
            $config->set('qux', array('foo' => 1, 'bar' => 2, 'baz' => 3), false);
            $this->assertCount(3, $config->qux->export());
            $this->assertEquals(1, $config->qux->foo);
            $this->assertEquals(2, $config->qux->bar);
            $this->assertEquals(3, $config->qux->baz);
            
            $config->set('qux', array('uno' => 1, 'dos' => 2, 'tres' => 3), true);
            $this->assertCount(6, $config->qux->export());
            $this->assertEquals(1, $config->qux->uno);
            $this->assertEquals(2, $config->qux->dos);
            $this->assertEquals(3, $config->qux->tres);
            
            return $config;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Config::delete
         * @covers \Phork\Core\Config::__isset
         */
        public function testDelete($config)
        {
            $config->set('quux', 10);
            $this->assertEquals(10, $config->quux);
            
            $config->delete('quux');
            $this->assertTrue(empty($config->quux));
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Config::delete
         * @expectedException PhorkException
         */
        public function testDeleteException($config)
        {
            $config->delete('xxx', true);
            return $config;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Config::load
         */
        public function testLoad($config)
        {
            $config->load('global');
            $this->assertNull($config->env);
            $this->assertInstanceOf('Phork\Core\Config', $config->router);
        }
    }
