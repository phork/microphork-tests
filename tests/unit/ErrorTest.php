<?php
    use \Phork\Core\Error;
    
    /**
     * @covers \Phork\Core\Error
     */
    class ErrorTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @covers \Phork\Core\Error::__construct
         * @covers \Phork\Core\Error::getVerbose
         */
        public function testInitialize() 
        {
            $error = new Error(true, true);
            $this->assertInstanceOf('Phork\Core\Error', $error);
            $this->assertTrue($error->getVerbose());
            return $error;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error::handle
         * @covers \Phork\Core\Error::getErrors
         * @covers \Phork\Core\Error::getDetails
         * @covers \Phork\Core\Error::getBacktraces
         */
        public function testTriggerNotice($error)
        {
            trigger_error('Notice', E_USER_NOTICE);
            
            $errors = $error->getErrors();
            $this->assertInstanceOf('\Phork\Core\Iterators\Associative', $errors);
            $this->assertEquals(1, $errors->count());
            $this->assertStringStartsWith('Notice:', $errors->last());
            
            $details = $error->getDetails();
            $this->assertInstanceOf('\Phork\Core\Iterators\Associative', $details);
            $this->assertEquals(1, $details->count());
            
            $backtraces = $error->getBacktraces();
            $this->assertInstanceOf('\Phork\Core\Iterators\Associative', $backtraces);
            $this->assertEquals(1, $backtraces->count());
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error::handle
         * @covers \Phork\Core\Error::getErrors
         */
        public function testTriggerWarning($error)
        {
            trigger_error('Warning', E_USER_WARNING);
            
            $errors = $error->getErrors();
            $this->assertInstanceOf('\Phork\Core\Iterators\Associative', $errors);
            $this->assertEquals(2, $errors->count());
            $this->assertStringStartsWith('Warning:', $errors->last());
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error::handle
         */
        public function testTriggerError($error)
        {
            trigger_error('Error', E_USER_ERROR);
            
            $errors = $error->getErrors();
            $this->assertInstanceOf('\Phork\Core\Iterators\Associative', $errors);
            $this->assertEquals(3, $errors->count());
            $this->assertStringStartsWith('Error:', $errors->last());
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error::clear
         */
        public function testClear($error)
        {
            $error->clear();
            
            $errors = $error->getErrors();
            $this->assertEquals(0, $errors->count());
            
            $details = $error->getDetails();
            $this->assertEquals(0, $details->count());
            
            $backtraces = $error->getBacktraces();
            $this->assertEquals(0, $backtraces->count());
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error::__destruct
         */
        public function testDestruct($error)
        {
            $error->__destruct();
        }
    }
