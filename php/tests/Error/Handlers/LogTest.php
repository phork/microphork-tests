<?php
    namespace Error\Handlers;
    
    use \Phork\Core\Error\Handlers\Log;
    
    /**
     * @covers \Phork\Core\Error\Handlers\Log
     */
    class LogTest extends \PHPUnit_Framework_TestCase 
    {
        static protected $logfile;
        protected $type = 'error';
        protected $level = E_USER_ERROR;
        protected $error = 'Test error';
        protected $file = __FILE__;
        protected $line = __LINE__;
        
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            static::$logfile = tempnam(LOG_PATH, 'error_handlers_logtest');
            file_put_contents(static::$logfile, '');
        }
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            unlink(static::$logfile);
        }
        
        /**
         * @covers \Phork\Core\Error\Handlers\Log::__construct
         * @expectedException PhorkException
         */
        public function testInitializeException() 
        {
            $log = new Log();
        }
        
        /**
         * @covers \Phork\Core\Error\Handlers\Log::__construct
         */
        public function testInitialize() 
        {
            $log = new Log(array(
                'logfile' => static::$logfile,
                'verbose' => false
            ));
            $this->assertInstanceOf('\Phork\Core\Error\Handlers\Log', $log);
            return $log;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Error\Handlers\Log::handle
         * @covers \Phork\Core\Error\Handlers\Log::log
         */
        public function testHandle($log)
        {
            $log->handle($this->type, $this->level, $error = $this->error.time(), $this->file, $this->line);
            $result = file_get_contents(static::$logfile);
            $this->assertTrue(strpos($result, $error) !== false);
        }
        
        /**
         * @covers \Phork\Core\Error\Handlers\Log::handle
         * @covers \Phork\Core\Error\Handlers\Log::log
         */
        public function testHandleVerbose()
        {
            $log = new Log(array(
                'logfile' => static::$logfile,
                'verbose' => true
            ));
            
            $log->handle($this->type, $this->level, $error = $this->error.time(), $this->file, $this->line);
            $result = file_get_contents(static::$logfile);
            $this->assertTrue(strpos($result, 'error: '.$error.' in '.$this->file.' on line '.$this->line) !== false);
        }
    }
