<?php
    use \Phork\Core\Singleton;
    
    /**
     * @covers \Phork\Core\Singleton
     */
    class SingletonTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @covers \Phork\Core\Singleton::__construct
         * @covers \Phork\Core\Singleton::instance
         */
        public function testInitialize() 
        {
            $singleton =  Singleton::instance(false, false);
            $this->assertNull($singleton);
            
            $singleton = SingletonOne::instance();
            $this->assertInstanceOf('Phork\Core\Singleton', $singleton);
            return $singleton;
        }
        
        /**
         * @covers \Phork\Core\Singleton::instance
         * @covers \Phork\Core\Singleton::__destruct
         */
        public function testDestroy()
        {
            $singleton = SingletonTwo::instance(true);
            $this->assertInstanceOf('Phork\Core\Singleton', $singleton);
            
            $singleton->foo = 'bar';
            $this->assertEquals('bar', $singleton->foo);
            
            unset($singleton);
            
            $singleton = SingletonTwo::instance(true);
            $this->assertNull($singleton->foo);
            
            unset($singleton);
            
            $singleton = SingletonThree::instance();
            $singleton->__destruct();
            unset($singleton);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Singleton::instance
         */
        public function testInstances($singleton)
        {
            $instances = $singleton->instances();
            $this->assertArrayHasKey(get_class($singleton), $singleton->instances());
            
            return $singleton;
        }
        
        /**
         * @covers \Phork\Core\Singleton::instance
         * @covers \Phork\Core\Singleton::dereference
         * @expectedException PhorkException
         */
        public function testDereference()
        {
            $singleton = SingletonOne::instance(true);
            
            $dereferenced = $singleton->dereferenced();
            $this->assertArrayHasKey(get_class($singleton), $dereferenced);
            
            $result = $singleton->dereference();
            $this->assertNull($result);
            
            $exception = SingletonOne::instance();
            
            return $singleton;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Singleton::__clone
         */
        public function testClone($singleton)
        {
            $singleton = $this->invokeMethod($singleton, '__clone');
            $this->assertNull($singleton);
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

    class SingletonOne extends Singleton 
    {
        public function instances()
        {
            return static::$instances;
        }
        
        public function dereferenced()
        {
            return static::$dereferenced;
        }
    }
    
    class SingletonTwo extends Singleton
    {
        protected $data = array(); 
        
        public function __get($var)
        {
            if (array_key_exists($var, $this->data)) {
                return $this->data[$var];
            }
        }
        
        public function __set($var, $val)
        {
            return $this->data[$var] = $val;
        }
    }
    
    class SingletonThree extends Singleton
    {
        public function __destruct()
        {
            parent::__destruct();
            
            if (array_key_exists($class = get_called_class(), static::$instances)) {
                throw new \PhorkException('Unable to destroy singleton');
            }
        }
    }
