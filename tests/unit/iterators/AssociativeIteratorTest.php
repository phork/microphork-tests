<?php
    use \Phork\Core\Iterators\Associative;
    use \Phork\Test\Iterators\AssociativeInvalid;
    
    class AssociativeIteratorTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public function testInitialize() 
        {
            $iterator = new Associative();
            $this->assertInstanceOf('Phork\Core\Iterators\Associative', $iterator);
            return $iterator;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Iterators\Associative::append
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::first
         * @covers \Phork\Core\Iterators\Associative::keyGet
         * @covers \Phork\Core\Iterators\Associative::offsetGet
         * @covers \Phork\Core\Iterators\Associative::last
         */
        public function testAppend($iterator)
        {
            $red = $iterator->append(array('r', 'red'));
            $orange = $iterator->append(array('o', 'orange'));
            $yellow = $iterator->append(array('y', 'yellow'));
            
            $this->assertEquals(3, $iterator->count());
            $this->assertEquals($red, $iterator->key());
            $this->assertEquals('red', $iterator->first());
            $this->assertEquals('orange', $iterator->keyGet($orange));
            $this->assertEquals('orange', $iterator->offsetGet(1));
            $this->assertEquals('yellow', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testAppend
         * @covers \Phork\Core\Iterators\Associative::insert
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::first
         * @covers \Phork\Core\Iterators\Associative::keyGet
         * @covers \Phork\Core\Iterators\Associative::offsetGet
         * @covers \Phork\Core\Iterators\Associative::last
         */
        public function testInsert($iterator)
        {
            $black = $iterator->insert(0, array('b', 'black'));
            $white = $iterator->insert(1, array('w', 'white'));
            
            $this->assertEquals(5, $iterator->count());
            $this->assertEquals('black', $iterator->first());
            $this->assertEquals('white', $iterator->keyGet($white));
            $this->assertEquals('white', $iterator->offsetGet(1));
            $this->assertEquals('red', $iterator->offsetGet(2));
            $this->assertEquals('orange', $iterator->offsetGet(3));
            $this->assertEquals('yellow', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testInsert
         * @covers \Phork\Core\Iterators\Associative::before
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::first
         * @covers \Phork\Core\Iterators\Associative::keyGet
         * @covers \Phork\Core\Iterators\Associative::offsetGet
         */
        public function testBefore($iterator)
        {
            $dark = $iterator->before('w', 'dark grey');
            $light = $iterator->before($dark, 'light grey');
            
            $this->assertEquals(7, $iterator->count());
            $this->assertEquals('black', $iterator->first());
            $this->assertEquals('light grey', $iterator->keyGet($light));
            $this->assertEquals('light grey', $iterator->offsetGet(1));
            $this->assertEquals('dark grey', $iterator->offsetGet(2));
            $this->assertEquals('white', $iterator->offsetGet(3));
            
            $result = $iterator->before('xxx', 'fail');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testBefore
         * @covers \Phork\Core\Iterators\Associative::after
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::offsetGet
         * @covers \Phork\Core\Iterators\Associative::last
         */
        public function testAfter($iterator)
        {
            $green = $iterator->after('y', 'green');
            $blue = $iterator->after($green, 'blue');
            
            $this->assertEquals(9, $iterator->count());
            $this->assertEquals('green', $iterator->offsetGet(7));
            $this->assertEquals('blue', $iterator->last());
            
            $result = $iterator->after('xxx', 'fail');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testBefore
         * @covers \Phork\Core\Iterators\Associative::modify
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::first
         */
        public function testModify($iterator)
        {
            $success = $iterator->modify('blackish');
            
            $this->assertEquals(9, $iterator->count());
            $this->assertTrue($success);
            $this->assertEquals('blackish', $iterator->first());
            
            return $iterator;
        }
        
        /**
         * @depends testModify
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::keySet
         * @covers \Phork\Core\Iterators\Associative::keyExists
         * @covers \Phork\Core\Iterators\Associative::keyGet
         */
        public function testKeys($iterator)
        {
            $iterator->keySet($iterator->key(), 'black');
            
            $this->assertTrue($iterator->keyExists('r'));
            $this->assertEquals('orange', $iterator->keyGet('o'));
            $this->assertEquals('black', $iterator->keyGet('b'));
            
            $iterator->keyUnset('b');
            $this->assertFalse($iterator->keyExists('b'));
            
            return $iterator;
        }
        
        /**
         * @depends testKeys
         * @covers \Phork\Core\Iterators\Associative::remove
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::first
         * @covers \Phork\Core\Iterators\Associative::last
         */
        public function testRemove($iterator)
        {
            $iterator->remove();
            $iterator->remove();
            $iterator->remove();
            
            $this->assertEquals(5, $iterator->count());
            $this->assertEquals('red', $iterator->first());
            $this->assertEquals('blue', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testRemove
         * @covers \Phork\Core\Iterators\Associative::rewind
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::current
         */
        public function testRewind($iterator)
        {
            $iterator->rewind(); 
            $this->assertEquals(0, $iterator->keyOffset($iterator->key()));
            $this->assertEquals('red', $iterator->current());
            
            return $iterator;
        }
        
        /**
         * @depends testRewind
         * @covers \Phork\Core\Iterators\Associative::next
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::current
         */
        public function testNext($iterator)
        { 
            $next = $iterator->next(); 
            $this->assertEquals(1, $iterator->keyOffset($iterator->key()));
            $this->assertEquals('orange', $next);
            $this->assertEquals('orange', $iterator->current());
            
            while ($next = $iterator->next()) {
                //do nothing; just need to run a full loop
            }
            
            $this->assertNull($iterator->next());
            $this->assertFalse($iterator->keyOffset($iterator->key()));
            return $iterator;
        }
        
        /**
         * @depends testNext
         * @covers \Phork\Core\Iterators\Associative::end
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::current
         */
        public function testEnd($iterator)
        {
            $iterator->end(); 
            $this->assertEquals(4, $iterator->keyOffset($iterator->key()));
            $this->assertEquals('blue', $iterator->current());
            
            return $iterator;
        }
        
        /**
         * @depends testEnd
         * @covers \Phork\Core\Iterators\Associative::prev
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::keyOffset
         * @covers \Phork\Core\Iterators\Associative::current
         */
        public function testPrev($iterator)
        {
            $prev = $iterator->prev(); 
            $this->assertEquals(3, $iterator->keyOffset($iterator->key()));
            $this->assertEquals('green', $prev);
            $this->assertEquals('green', $iterator->current());
            
            while ($prev = $iterator->prev()) {
                //do nothing; just need to run a full loop
            }
            
            $this->assertNull($iterator->prev());
            $this->assertFalse($iterator->keyOffset($iterator->key()));
            
            return $iterator;
        }
        
        /**
         * @depends testPrev
         * @covers \Phork\Core\Iterators\Associative::seek
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::keyOffset
         * @covers \Phork\Core\Iterators\Associative::current
         */
        public function testSeek($iterator)
        {
            $result = $iterator->seek('y');
            $this->assertTrue($result);
            $this->assertEquals(2, $iterator->keyOffset($iterator->key()));
            $this->assertEquals('yellow', $iterator->current());
            
            $result = $iterator->seek('xxx');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testSeek
         * @covers \Phork\Core\Iterators\Associative::rewind
         * @covers \Phork\Core\Iterators\Associative::each
         * @covers \Phork\Core\Iterators\Associative::key
         * @covers \Phork\Core\Iterators\Associative::keyOffset
         * @covers \Phork\Core\Iterators\Associative::current
         * @covers \Phork\Core\Iterators\Associative::valid
         */
        public function testEach($iterator)
        {
            $iterator->rewind(); 
            
            while (list($key, $item) = $iterator->each()) {
                //do nothing; just need to run a full loop
            }   
            
            $this->assertFalse($iterator->keyOffset($iterator->key()));
            $this->assertNull($iterator->current());
            $this->assertFalse($iterator->valid());
            
            return $iterator;
        }
        
        /**
         * @depends testEach
         * @covers \Phork\Core\Iterators\Associative::clear
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::key
         */
        public function testClear($iterator)
        {
            $iterator->clear(); 
            
            $this->assertEquals(0, $iterator->count());
            $this->assertNull($iterator->key());
            
            return $iterator;
        }
        
        /**
         * @depends testClear
         * @covers \Phork\Core\Iterators\Associative::append
         * @covers \Phork\Core\Iterators\Associative::items
         */
        public function testItems($iterator)
        {
            $red = $iterator->append('red');
            $orange = $iterator->append('orange');
            $yellow = $iterator->append('yellow');
        
            $items = $iterator->items();
            $this->assertCount(3, $items);
            return $iterator;
        }
        
        /**
         * @depends testItems
         * @covers \Phork\Core\Iterators\Associative::offsetGet
         */
        public function testOffsetGet($iterator)
        {
            $red = $iterator->offsetGet(0);
            $this->assertEquals('red', $red);
            
            $orange = $iterator->offsetGet(1);
            $this->assertEquals('orange', $orange);
            
            $fail = $iterator->offsetGet(99);
            $this->assertNull($fail);
        }
        
        /**
         * @depends testItems
         * @covers \Phork\Core\Iterators\Associative::offsetSet
         */
        public function testOffsetSet($iterator)
        {
            $result = $iterator->offsetSet(0, 'redish');
            $this->assertTrue($result);
            
            $result = $iterator->offsetSet(1, 'orangish');
            $this->assertTrue($result);
            
            $result = $iterator->offsetSet(99, 'appended');
            $this->assertTrue($result);
        }
        
        /**
         * @depends testItems
         * @covers \Phork\Core\Iterators\Associative::offsetKey
         * @covers \Phork\Core\Iterators\Associative::count
         */
        public function testOffsetKey($iterator)
        {
            $result = $iterator->append(array('foo', 'bar'));
            $this->assertEquals('foo', $result);
            
            $result = $iterator->offsetKey($iterator->count() - 1);
            $this->assertEquals('foo', $result);
            
            $result = $iterator->offsetKey(99);
            $this->assertNull($result);
        }
        
        /**
         * @depends testItems
         * @covers \Phork\Core\Iterators\Associative::offsetUnset
         */
        public function testOffsetUnset($iterator)
        {
            $iterator->end();
            
            $result = $iterator->offsetUnset(0);
            $this->assertTrue($result);
            
            $result = $iterator->offsetUnset(1);
            $this->assertTrue($result);
            
            $result = $iterator->offsetUnset(99);
            $this->assertNull($result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Iterators\Associative::count
         * @covers \Phork\Core\Iterators\Associative::append
         * @covers \Phork\Core\Iterators\Associative::keyGet
         */
        public function testKeyGet($iterator)
        {
            $iterator->clear();
            $this->assertEquals(0, $iterator->count());
            
            $red = $iterator->append('red');
            $orange = $iterator->append(array('o', 'orange'));
            
            $result = $iterator->keyGet($red);
            $this->assertEquals('red', $result);
            
            $result = $iterator->keyGet('o');
            $this->assertEquals('orange', $result);
            
            $result = $iterator->keyGet('xxx');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testKeyGet
         * @covers \Phork\Core\Iterators\Associative::keySet
         * @covers \Phork\Core\Iterators\Associative::keyGet
         */
        public function testKeySet($iterator)
        {
            $iterator->keySet('o', 'orangish');
            $result = $iterator->keyGet('o');
            $this->assertEquals('orangish', $result);
            
            $result = $iterator->keySet('xxx', 'fail');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testKeySet
         * @covers \Phork\Core\Iterators\Associative::keyUnset
         * @covers \Phork\Core\Iterators\Associative::offsetKey
         */
        public function testKeyUnset($iterator)
        {
            $iterator->keyUnset('o');
            $this->assertEquals(1, $iterator->count());
            
            $iterator->keyUnset($iterator->offsetKey(0));
            $this->assertEquals(0, $iterator->count());
            
            $result = $iterator->keyUnset('xxx');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @covers \Phork\Core\Iterators\Associative::append
         * @covers \Phork\Core\Iterators\Associative::insert
         * @covers \Phork\Core\Iterators\Associative::offsetSet
         */
        public function testInvalid()
        {
            $iterator = new AssociativeInvalid();
            
            $result = $iterator->append('foo');
            $this->assertNull($result);
            
            $result = $iterator->insert(1, 'foo');
            $this->assertNull($result);
            
            $result = $iterator->offsetSet(2, 'foo');
            $this->assertNull($result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Iterators\Associative::genKey
         */
        public function testGenKey($iterator)
        {
            $key = $this->invokeMethod($iterator, 'genKey');
            $this->assertTrue(is_string($key));
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Iterators\Associative::extract
         */
        public function testExtract($iterator)
        {
            $result = $this->invokeMethod($iterator, 'extract', array('foo', false));
            $this->assertNull($result[0]);
            $this->assertEquals('foo', $result[1]);
            
            $result = $this->invokeMethod($iterator, 'extract', array('foo', true));
            $this->assertTrue(is_string($result[0]));
            $this->assertEquals('foo', $result[1]);
            
            $result = $this->invokeMethod($iterator, 'extract', array(array('foo', 'bar'), false));
            $this->assertEquals('foo', $result[0]);
            $this->assertEquals('bar', $result[1]);
            
            $result = $this->invokeMethod($iterator, 'extract', array(array('foo', 'bar'), true));
            $this->assertEquals('foo', $result[0]);
            $this->assertEquals('bar', $result[1]);
        }
        
        /**
         * @coversNothing
         */
        public function invokeMethod(&$object, $methodName, array $args = array())
        {
            $reflection = new \ReflectionClass(get_class($object));
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);
            
            return $method->invokeArgs($object, $args);
        }
    }
