<?php
    use \Phork\Core\Event;
    
    /**
     * @covers \Phork\Core\Event
     */
    class EventTest extends PHPUnit_Framework_TestCase 
    {
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
         * @covers \Phork\Core\Event::__construct
         */
        public function testInitializeException() 
        {
            $loader = \Phork::loader();
            $stack = $loader->removeStack(\Phork::LOAD_STACK);
            $loader->addStack(\Phork::LOAD_STACK, array());
            
            try {
                //this should always throw an exception but clean up is still needed so only assertTrue should be called
                $event = Event::instance();
                $this->assertTrue(false);
            } catch (\PhorkException $exception) {
                unset($event);
                $loader->addStack(\Phork::LOAD_STACK, $stack, true);
                $this->assertTrue(true);
            }
        }
        
        /**
         * @covers \Phork\Core\Event::__construct
         */
        public function testInitialize() 
        {
            $event = Event::instance();
            $this->assertInstanceOf('Phork\Core\Event', $event);
            return $event;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Event::listen
         * @covers \Phork\Core\Event::exists
         */
        public function testListen($event)
        {
            $foo = $event->listen('test.one', function() {
                return 'foo';
            });
            
            $bar = $event->listen('test.one', function() {
                return 'bar';
            }, array(), 0);
            
            $this->assertTrue($event->exists('test.one'));
            
            return $event;
        }
        
        /**
         * @depends testListen
         * @covers \Phork\Core\Event::once
         * @covers \Phork\Core\Event::get
         */
        public function testOnce($event)
        {
            $baz = $event->once('test.one', function() {
                return 'baz';
            });
            
            $events = $event->get('test.one');
            $this->assertCount(3, $events);
            
            return $event;
        }
        
        /**
         * @depends testOnce
         * @covers \Phork\Core\Event::listen
         * @covers \Phork\Core\Event::get
         */
        public function testCallback($event)
        {
            $cr = $event->listen('test.one', function($calltime, $runtime) {
                return sprintf('%s-%s', $calltime, $runtime);
            }, array('calltime'));
            
            $events = $event->get('test.one');
            $this->assertCount(4, $events);
            
            return $event;
        }
        
        /**
         * @depends testCallback
         * @covers \Phork\Core\Event::trigger
         */
        public function testTriggerOne($event)
        {
            $results = $event->trigger('test.one', array('runtime'));
            $this->assertCount(4, $results);
            
            $bar = array_shift($results);
            $this->assertEquals('bar', $bar);
            
            $foo = array_shift($results);
            $this->assertEquals('foo', $foo);
            
            $baz = array_shift($results);
            $this->assertEquals('baz', $baz);
            
            $cr = array_shift($results);
            $this->assertEquals('calltime-runtime', $cr);
            
            return $event;
        }
        
        /**
         * @depends testTriggerOne
         * @covers \Phork\Core\Event::trigger
         */
        public function testTriggerTwo($event)
        {
            $results = $event->trigger('test.one', array('runtime'), true);
            $this->assertCount(3, $results);
            
            $bar = array_shift($results);
            $this->assertEquals('bar', $bar);
            
            $foo = array_shift($results);
            $this->assertEquals('foo', $foo);
            
            $cr = array_shift($results);
            $this->assertEquals('calltime-runtime', $cr);
            
            return $event;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Event::trigger
         */
        public function testTriggerThree($event)
        {
            $result = $event->trigger('foo', null, false, false);
            $this->assertNull($result);
            return $event;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Event::trigger
         * @expectedException PhorkException
         */
        public function testTriggerException($event)
        {
            $result = $event->trigger('foo', null, false, true);
            $this->assertNull($result);
            return $event;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Event::listen
         * @covers \Phork\Core\Event::get
         * @covers \Phork\Core\Event::trigger
         * @covers \Phork\Core\Event::remove
         */
        public function testRemove($event)
        {
            $foo = $event->listen('test.two', function() {
                return 'foo';
            });
            
            $bar = $event->listen('test.two', function() {
                return 'bar';
            });
            
            $result = $event->remove('test.two', $foo);
            $this->assertTrue(is_object($result));
            $this->assertInstanceOf('Closure', $result->callback());
            $this->assertTrue(is_array($result->args()));
            
            $events = $event->get('test.two');
            $this->assertCount(1, $events);
            
            $results = $event->trigger('test.two');
            $this->assertCount(1, $results);
            
            $bar = array_shift($results);
            $this->assertEquals('bar', $bar);
            
            $result = $event->remove('test.two', 'baz', false);
            $this->assertNull($result);
            
            $result = $event->remove('test.xxx', 'baz', false);
            $this->assertNull($result);
            
            return $event;
        }
        
        /**
         * @depends testRemove
         * @expectedException PhorkException
         */
        public function testRemoveFail($event)
        {
            $result = $event->remove('test.two', 'xxx', true);
        }
        
        /**
         * @depends testRemove
         * @covers \Phork\Core\Event::destroy
         * @covers \Phork\Core\Event::get
         * @expectedException PhorkException
         */
        public function testDestroy($event)
        {
            $result = $event->destroy('test.xxx', false);
            $this->assertNull($result);
            
            $result = $event->destroy('test.xxx', true);
            $this->assertNull($result);
            
            $result = $event->destroy('test.two');
            $this->assertInstanceOf('Phork\Core\Iterators\Associative', $result);
            
            $event->get('test.two');
            return $event;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Event::__destruct
         */
        public function testDestruct($event)
        {
            Event::instance()->__destruct();
        }
    }
