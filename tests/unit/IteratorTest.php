<?php
    use \Phork\Core\Iterator;
    use \Phork\Test\IteratorInvalid;
    
    /**
     * @covers \Phork\Core\Iterator
     */
    class IteratorTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public function testInitialize() 
        {
            $iterator = new Iterator();
            $this->assertInstanceOf('Phork\Core\Iterator', $iterator);
            return $iterator;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Iterator::append
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         * @covers \Phork\Core\Iterator::offsetGet
         * @covers \Phork\Core\Iterator::last
         */
        public function testAppend($iterator)
        {
            $red = $iterator->append('red');
            $orange = $iterator->append('orange');
            $yellow = $iterator->append('yellow');
            
            $this->assertEquals(3, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals(0, $red);
            $this->assertEquals(1, $orange);
            $this->assertEquals(2, $yellow);
            $this->assertEquals('red', $iterator->first());
            $this->assertEquals('orange', $iterator->offsetGet(1));
            $this->assertEquals('yellow', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testAppend
         * @covers \Phork\Core\Iterator::insert
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         * @covers \Phork\Core\Iterator::offsetGet
         * @covers \Phork\Core\Iterator::last
         */
        public function testInsert($iterator)
        {
            $black = $iterator->insert(0, 'black');
            $white = $iterator->insert(1, 'white');
            
            $this->assertEquals(5, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals(0, $black);
            $this->assertEquals(1, $white);
            $this->assertEquals('black', $iterator->first());
            $this->assertEquals('white', $iterator->offsetGet(1));
            $this->assertEquals('red', $iterator->offsetGet(2));
            $this->assertEquals('orange', $iterator->offsetGet(3));
            $this->assertEquals('yellow', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testInsert
         * @covers \Phork\Core\Iterator::before
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         * @covers \Phork\Core\Iterator::offsetGet
         */
        public function testBefore($iterator)
        {
            $dark = $iterator->before(1, 'dark grey');
            $light = $iterator->before(1, 'light grey');
            
            $this->assertEquals(7, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals(1, $dark);
            $this->assertEquals(1, $light);
            $this->assertEquals('black', $iterator->first());
            $this->assertEquals('light grey', $iterator->offsetGet(1));
            $this->assertEquals('dark grey', $iterator->offsetGet(2));
            $this->assertEquals('white', $iterator->offsetGet(3));
            
            $result = $iterator->before(99, 'fail');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testBefore
         * @covers \Phork\Core\Iterator::after
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         * @covers \Phork\Core\Iterator::offsetGet
         * @covers \Phork\Core\Iterator::last
         */
        public function testAfter($iterator)
        {
            $green = $iterator->after(6, 'green');
            $blue = $iterator->after(7, 'blue');
            
            $this->assertEquals(9, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals(7, $green);
            $this->assertEquals(8, $blue);
            $this->assertEquals('green', $iterator->offsetGet(7));
            $this->assertEquals('blue', $iterator->last());
            
            $result = $iterator->after(99, 'fail');
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testBefore
         * @covers \Phork\Core\Iterator::modify
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         */
        public function testModify($iterator)
        {
            $success = $iterator->modify('blackish');
            
            $this->assertEquals(9, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertTrue($success);
            $this->assertEquals('blackish', $iterator->first());
            
            return $iterator;
        }
        
        /**
         * @depends testModify
         * @covers \Phork\Core\Iterator::remove
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::first
         * @covers \Phork\Core\Iterator::last
         */
        public function testRemove($iterator)
        {
            $iterator->remove();
            $iterator->remove();
            $iterator->remove();
            $iterator->remove();
            
            $this->assertEquals(5, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals('red', $iterator->first());
            $this->assertEquals('blue', $iterator->last());
            
            return $iterator;
        }
        
        /**
         * @depends testRemove
         * @covers \Phork\Core\Iterator::rewind
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         */
        public function testRewind($iterator)
        {
            $iterator->rewind(); 
            $this->assertEquals(0, $iterator->key());
            $this->assertEquals('red', $iterator->current());
            
            return $iterator;
        }
        
        /**
         * @depends testRewind
         * @covers \Phork\Core\Iterator::next
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         */
        public function testNext($iterator)
        { 
            $next = $iterator->next(); 
            $this->assertEquals(1, $iterator->key());
            $this->assertEquals('orange', $next);
            $this->assertEquals('orange', $iterator->current());
            
            while ($next = $iterator->next()) {
                //do nothing; just need to run a full loop
            }
            
            $this->assertNull($iterator->next());
            $this->assertEquals($iterator->count(), $iterator->key());
            
            return $iterator;
        }
        
        /**
         * @depends testNext
         * @covers \Phork\Core\Iterator::end
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         */
        public function testEnd($iterator)
        {
            $iterator->end(); 
            $this->assertEquals(4, $iterator->key());
            $this->assertEquals('blue', $iterator->current());
            
            return $iterator;
        }
        
        /**
         * @depends testEnd
         * @covers \Phork\Core\Iterator::prev
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         */
        public function testPrev($iterator)
        {
            $prev = $iterator->prev(); 
            $this->assertEquals(3, $iterator->key());
            $this->assertEquals('green', $prev);
            $this->assertEquals('green', $iterator->current());
            
            while ($prev = $iterator->prev()) {
                //do nothing; just need to run a full loop
            }
            
            $this->assertNull($iterator->prev());
            $this->assertEquals(-1, $iterator->key());
            
            return $iterator;
        }
        
        /**
         * @depends testPrev
         * @covers \Phork\Core\Iterator::seek
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         */
        public function testSeek($iterator)
        {
            $result = $iterator->seek(2);
            $this->assertTrue($result);
            $this->assertEquals(2, $iterator->key());
            $this->assertEquals('yellow', $iterator->current());
            
            $result = $iterator->seek(99);
            $this->assertNull($result);
            
            return $iterator;
        }
        
        /**
         * @depends testSeek
         * @covers \Phork\Core\Iterator::rewind
         * @covers \Phork\Core\Iterator::each
         * @covers \Phork\Core\Iterator::key
         * @covers \Phork\Core\Iterator::current
         * @covers \Phork\Core\Iterator::valid
         */
        public function testEach($iterator)
        {
            $iterator->rewind();
            
            while (list($position, $item) = $iterator->each()) {
                //do nothing; just need to run a full loop
            }   
            
            $this->assertEquals($iterator->key(), $iterator->count());
            $this->assertNull($iterator->current());
            $this->assertFalse($iterator->valid());
            
            return $iterator;
        }
        
        /**
         * @depends testEach
         * @covers \Phork\Core\Iterator::clear
         * @covers \Phork\Core\Iterator::count
         * @covers \Phork\Core\Iterator::key
         */
        public function testClear($iterator)
        {
            $iterator->clear(); 
            
            $this->assertEquals(0, $iterator->count());
            $this->assertEquals(0, $iterator->key());
            
            return $iterator;
        }
        
        /**
         * @depends testClear
         * @covers \Phork\Core\Iterator::append
         * @covers \Phork\Core\Iterator::items
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
         * @covers \Phork\Core\Iterator::offsetGet
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
         * @covers \Phork\Core\Iterator::offsetSet
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
         * @covers \Phork\Core\Iterator::offsetUnset
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
         * @covers \Phork\Core\Iterator::append
         * @covers \Phork\Core\Iterator::insert
         * @covers \Phork\Core\Iterator::offsetSet
         */
        public function testInvalid()
        {
            $iterator = new IteratorInvalid();
            
            $result = $iterator->append('foo');
            $this->assertNull($result);
            
            $result = $iterator->insert(1, 'foo');
            $this->assertNull($result);
            
            $result = $iterator->offsetSet(2, 'foo');
            $this->assertNull($result);
        }
    }
