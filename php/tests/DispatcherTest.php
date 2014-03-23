<?php
    use \Phork\Core\Dispatcher;
    use \Phork\Test\DispatcherOne;
    use \Phork\Test\DispatcherMany;
    use \Phork\Test\Dispatcher\Handlers\HandlerInterface;
    use \Phork\Test\Dispatcher\Handlers\Handler;
     
    /**
     * @covers \Phork\Core\Iterator
     */
    class DispatcherTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public function testInitializeOne() 
        {
            $dispatcher = new DispatcherOne();
            $this->assertInstanceOf('Phork\Core\Dispatcher', $dispatcher);
            return $dispatcher;
        }
        
        /**
         * @coversNothing
         */
        public function testInitializeMany() 
        {
            $dispatcher = new DispatcherMany();
            $this->assertInstanceOf('Phork\Core\Dispatcher', $dispatcher);
            return $dispatcher;
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::init
         * @covers \Phork\Core\Dispatcher::initHandler
         * @covers \Phork\Core\Dispatcher::count
         */
        public function testInit($one, $many)
        {
            list($handlers, $active) = $one->init(array(
                'print' => array(
                    'active' => true,
                    'class' => '\\Phork\\Test\\Dispatcher\\Handlers\\Handler',
                    'params' => array(
                        'action' => 'print'
                    )
                ),
                'ignore' => array(
                    'init' => false,
                    'active' => false,
                    'class' => '\\Phork\\Test\\Dispatcher\\Handlers\\Handler',
                    'params' => array(
                        'action' => 'ignore'
                    )
                )
            ));
            
            $this->assertEquals(1, $handlers);
            $this->assertEquals(1, $active);
            
            list($handlers, $active) = $many->init(array(
                'print' => array(
                    'init' => true,
                    'active' => true,
                    'class' => '\\Phork\\Test\\Dispatcher\\Handlers\\Handler',
                    'params' => array(
                        'action' => 'print'
                    )
                ),
                'return' => array(
                    'init' => true,
                    'active' => false,
                    'class' => '\\Phork\\Test\\Dispatcher\\Handlers\\Handler',
                    'params' => array(
                        'action' => 'return'
                    )
                ),
                'ignore' => array(
                    'init' => false,
                    'active' => false,
                    'class' => '\\Phork\\Test\\Dispatcher\\Handlers\\Handler',
                    'params' => array(
                        'action' => 'ignore'
                    )
                )
            ));
            
            $this->assertEquals(2, $handlers);
            $this->assertEquals(1, $active);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::initHandler
         * @covers \Phork\Core\Dispatcher::addHandler
         * @covers \Phork\Core\Dispatcher::count
         */
        public function testInitHandler($one, $many)
        {
            list($handlers, $active) = $many->initHandler('ignore');
            $this->assertEquals(3, $handlers);
            $this->assertEquals(1, $active);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::initHandler
         * @expectedException PhorkException
         */
        public function testInitHandlerException($one, $many)
        {
            $many->initHandler('xxx');
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::addHandler
         * @expectedException PhorkException
         */
        public function testAddHandlerException($one, $many)
        {
            $many->addHandler('xxx', new StdClass());
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::activateHandler
         * @covers \Phork\Core\Dispatcher::count
         */
        public function testActivateHandler($one, $many)
        {
            list($handlers, $active) = $one->activateHandler('ignore');
            $this->assertEquals(2, $handlers);
            $this->assertEquals(2, $active);
            
            list($handlers, $active) = $many->activateHandler('ignore');
            $this->assertEquals(3, $handlers);
            $this->assertEquals(2, $active);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::getHandler
         */
        public function testGetHandler($one, $many)
        {
            $result = $many->getHandler('print');
            $this->assertInstanceOf('Phork\Test\Dispatcher\Handlers\Handler', $result);
            
            $result = $many->getHandler('xxx', false);
            $this->assertNull($result);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::getHandler
         * @expectedException PhorkException
         */
        public function testGetHandlerException($one, $many)
        {
            $result = $many->getHandler('xxx');
            $this->assertNull($result);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::deactivateHandler
         * @covers \Phork\Core\Dispatcher::count
         */
        public function testDeactivateHandler($one, $many)
        {
            list($handlers, $active) = $many->deactivateHandler('ignore');
            $this->assertEquals(3, $handlers);
            $this->assertEquals(1, $active);
            
            list($handlers, $active) = $many->deactivateHandler('xxx', false);
            $this->assertEquals(3, $handlers);
            $this->assertEquals(1, $active);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::deactivateHandler
         * @expectedException PhorkException
         */
        public function testDeactivateHandlerException($one, $many)
        {
            $many->deactivateHandler('xxx');
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::removeHandler
         * @covers \Phork\Core\Dispatcher::count
         */
        public function testRemoveHandler($one, $many)
        {
            list($handlers, $active) = $many->removeHandler('ignore');
            $this->assertEquals(2, $handlers);
            $this->assertEquals(1, $active);
            
            list($handlers, $active) = $many->removeHandler('xxx', false);
            $this->assertEquals(2, $handlers);
            $this->assertEquals(1, $active);
        }
        
        /**
         * @depends testInitializeOne
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::removeHandler
         * @expectedException PhorkException
         */
        public function testRemoveHandlerException($one, $many)
        {
            $many->removeHandler('xxx');
        }
        
        /**
         * @depends testInitializeMany
         * @covers \Phork\Core\Dispatcher::activateHandler
         * @covers \Phork\Core\Dispatcher::__call
         */
        public function testCallHandlerMany($many)
        {
            $many->activateHandler('return');
            
            $results = $many->passthru('foobar');
            $this->expectOutputString('foobar');
            $this->assertArrayHasKey('print', $results);
            $this->assertArrayHasKey('return', $results);
            $this->assertNull($results['print']);
            $this->assertEquals($results['return'], 'foobar');
        }
        
        /**
         * @depends testInitializeOne
         * @covers \Phork\Core\Dispatcher::__call
         * @expectedException PhorkException
         */
        public function testCallHandlerOneException($one)
        {
            $results = $one->passthru('foobar');
        }
        
        /**
         * @depends testInitializeOne
         * @covers \Phork\Core\Dispatcher::deactivateHandler
         * @covers \Phork\Core\Dispatcher::__call
         */
        public function testCallHandlerOne($one)
        {
            $one->deactivateHandler('ignore');

            $results = $one->passthru('foobar');
            $this->expectOutputString('foobar');
        }
    }
