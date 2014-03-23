<?php
    use \Phork\Core\Api;
    
    /**
     * @covers \Phork\Core\Api
     * @covers \Phork\Core\Api\Internal
     */
    class ApiTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            init_bootstrap();
            \Phork::instance()->init(PHK_ENV);
        }
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            destroy_bootstrap();
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         */
        public function testInitializeException()
        {
            $loader = \Phork::loader();
            $stack = $loader->removeStack(\Phork::LOAD_STACK);
            $loader->addStack(\Phork::LOAD_STACK, array());
            
            try {
                //this should always throw an exception but clean up is still needed so only assertTrue should be called
                $api = new Api(\Phork::router());
                $this->assertTrue(false);
            } catch (\PhorkException $exception) {
                $loader->addStack(\Phork::LOAD_STACK, $stack, true);
                $this->assertTrue(true);
            }
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         */
        public function testInitialize()
        {
            $api = new Api(\Phork::router());
            $this->assertInstanceOf('Phork\Core\Api', $api);
            return $api;
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         * @covers \Phork\Core\Api::handleEncoders
         */
        public function testRunEncoders()
        {
            $router = \Phork::router();
            $router->init('get', 'api/encoders.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(200, $statusCode);
            $this->assertTrue($success);
            $this->assertArrayHasKey('encoders', $result);
            return $api;
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         * @covers \Phork\Core\Api::handleBatch
         * @covers \Phork\Core\Api\Internal::get
         * @covers \Phork\Core\Api\Internal::post
         * @covers \Phork\Core\Api\Internal::put
         * @covers \Phork\Core\Api\Internal::delete
         * @covers \Phork\Core\Api\Internal::request
         */
        public function testRunBatch()
        {
            $router = \Phork::router();
            $router->init('get', 'api/batch.json', array(
                'requests' => json_encode(array(
                    'encoders-get' => array(
                        'method' => 'get',
                        'url' => '/api/encoders.json?foo=bar'
                    ),
                    'encoders-post' => array(
                        'method' => 'post',
                        'url' => '/api/encoders.json'
                    ),
                    'encoders-put' => array(
                        'method' => 'put',
                        'url' => '/api/encoders.json'
                    ),
                    'encoders-delete' => array(
                        'method' => 'delete',
                        'url' => '/api/encoders.json'
                    )
                ))
            ));
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(200, $statusCode);
            $this->assertTrue($success);
            $this->assertArrayHasKey('batched', $result);
            
            $this->assertArrayHasKey('encoders-get', $result['batched']);
            $this->assertArrayHasKey('data', $result['batched']['encoders-get']);
            $this->assertEquals(200, $result['batched']['encoders-get']['status']);
            $this->assertTrue($result['batched']['encoders-get']['success']);
            
            $this->assertArrayHasKey('encoders-post', $result['batched']);
            $this->assertArrayHasKey('data', $result['batched']['encoders-post']);
            $this->assertArrayHasKey('errors', $result['batched']['encoders-post']['data']);
            $this->assertEquals(400, $result['batched']['encoders-post']['status']);
            $this->assertFalse($result['batched']['encoders-post']['success']);
            
            $this->assertArrayHasKey('encoders-put', $result['batched']);
            $this->assertArrayHasKey('data', $result['batched']['encoders-put']);
            $this->assertArrayHasKey('errors', $result['batched']['encoders-put']['data']);
            $this->assertEquals(400, $result['batched']['encoders-put']['status']);
            $this->assertFalse($result['batched']['encoders-put']['success']);
            
            $this->assertArrayHasKey('encoders-delete', $result['batched']);
            $this->assertArrayHasKey('data', $result['batched']['encoders-delete']);
            $this->assertArrayHasKey('errors', $result['batched']['encoders-delete']['data']);
            $this->assertEquals(400, $result['batched']['encoders-delete']['status']);
            $this->assertFalse($result['batched']['encoders-delete']['success']);
            
            return $api;
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         * @covers \Phork\Core\Api::handleBatch
         */
        public function testRunBatchMissingException()
        {
            $router = \Phork::router();
            $router->init('get', 'api/batch.json', array());
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Missing batch definitions', $result['errors']);
        }
            
        /**
         * @covers \Phork\Core\Api::__construct
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         * @covers \Phork\Core\Api::handleBatch
         */
        public function testRunBatchInvalidException()
        {
            $router = \Phork::router();    
            $router->init('get', 'api/batch.json', array(
                'requests' => array(
                    'foo'
                )
            ));
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Invalid batch definitions', $result['errors']);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         * @covers \Phork\Core\Api::handleBatch
         */
        public function testRunBatchMissingRequestException()
        {
            $router = \Phork::router();
            $router->init('get', 'api/batch.json', array(
                'requests' => json_encode(array(
                    'xxx' => array(
                        'method' => 'get'
                    )
                ))
            ));
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Missing request type and/or URL', $result['errors']);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct 
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         */
        public function testRunInvalidException()
        {
            $router = \Phork::router();
            $router->init('get', 'api/xxx.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Invalid API method', $result['errors']);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct 
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         */
        public function testRunExtension()
        {
            $router = \Phork::router();
            $router->init('get', 'api/test/success.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(200, $statusCode);
            $this->assertTrue($success);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct 
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         */
        public function testRunExtensionError()
        {
            $router = \Phork::router();
            $router->init('post', 'api/test/error.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct 
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         */
        public function testRunInvalidExtensionMethod()
        {
            $router = \Phork::router();
            $router->init('get', 'api/test/xxx.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Invalid API method', $result['errors']);
        }
        
        /**
         * @covers \Phork\Core\Api::__construct 
         * @covers \Phork\Core\Api::run
         * @covers \Phork\Core\Api::handle
         */
        public function testRunInvalidExtension()
        {
            $router = \Phork::router();
            $router->init('get', 'api/xxx/foo.json');
            $api = new Api(\Phork::router());
            
            list($statusCode, $success, $result) = $api->run();
            $this->assertEquals(400, $statusCode);
            $this->assertFalse($success);
            $this->assertArrayHasKey('errors', $result);
            $this->assertContains('Invalid API class', $result['errors']);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Api::mapXmlNode
         */
        public function testMapXmlNode($api)
        {
            $result = $api->mapXmlNode(1, 'errors');
            $this->assertEquals('error', $result);
            
            $result = $api->mapXmlNode(1, 'batched');
            $this->assertEquals('result', $result);
            
            $result = $api->mapXmlNode(1, 'encoders');
            $this->assertEquals('ext', $result);
        }
    }
