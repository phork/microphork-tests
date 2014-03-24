<?php
    namespace Controllers;
    
    use \Phork\Core\Controllers\Api as CoreApi;
    use \Phork\App\Controllers\Api as Api;
    
    /**
     * @covers \Phork\Core\Controllers\Api
     * @covers \Phork\Core\Encoder\Handlers\Xml
     * @covers \Phork\Core\Encoder\Handlers\Json
     * @covers \Phork\Core\Encoder\Handlers\Jsonp
     */
    class ApiTest extends \PHPUnit_Framework_TestCase 
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
         * @coversNothing
         */
        public function testInitialize() 
        {
            $api = new Api();
            $this->assertInstanceOf('Phork\Core\Controllers\Api', $api);
            return $api;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunXml($api) 
        {
            ob_start();
            
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/encoders.xml', array());
            
            $api->run();
            $output = ob_get_clean();
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><encoders><ext>xml</ext><ext>json</ext><ext>jsonp</ext></encoders></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunXml404($api) 
        {
            ob_start();
            
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/xxx.xml', array());
            
            $api->run();
            $output = ob_get_clean();
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><errors><error>Invalid API method</error></errors></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunJson($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/encoders.json', array());
            
            $api->run();
            $this->expectOutputString('{"encoders":["xml","json","jsonp"]}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunJson404($api) 
        {
            ob_start();
            
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/xxx.json', array());
            
            $api->run();
            $result = json_decode(ob_get_clean(), true);
            
            $this->assertTrue(is_array($result));
            $this->assertArrayHasKey('errors', $result);
            $this->assertEquals(1, count($result['errors']));
            $this->assertEquals('Invalid API method', $result['errors'][0]);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunJsonp($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/encoders.jsonp', array('callback' => 'phork'));
            
            $api->run();
            $this->expectOutputString('phork({"encoders":["xml","json","jsonp"]})');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunJsonp404($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/xxx.jsonp', array('callback' => 'phork'));
            
            $api->run();
            $this->expectOutputString('phork({"errors":["Invalid API method"]})');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunDefaultWithMeta($api) 
        {
            \Phork::error()->clear();
            \Phork::config()->interfaces->api->defaults->set('meta', true);
            \Phork::router()->init('get', 'api/encoders', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":true},"encoders":["xml","json","jsonp"]}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunFailureWithMeta($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/batch.json', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":false},"errors":["Missing batch definitions"]}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunApiException($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/test/exception.json', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":false},"error":"Exception"}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunException($api) 
        {
            \Phork::error()->clear();
            \Phork::router()->init('get', 'api/encoders.xxx', array());
            
            $api->run();
            $this->expectOutputString('400: Invalid encoder: xxx');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::authenticate
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunAuthTokenYes($api) 
        {
            $auth = $this->getMock('\Phork\Test\AuthMock');
            $auth->expects($this->any())
                ->method('isAuthenticated')
                ->will($this->returnValue(true))
            ;
            $auth->expects($this->any())
                ->method('isTokenValid')
                ->will($this->returnValue(true))
            ;
            $auth->expects($this->any())
                ->method('standardAuth')
                ->will($this->returnValue(true))
            ;
            
            \Phork::error()->clear();
            \Phork::instance()->register('auth', $auth, true);
            \Phork::router()->init('get', 'api/test/auth.json', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":true},"auth":true}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::authenticate
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunAuthTokenNo($api) 
        {
            $auth = $this->getMock('\Phork\Test\AuthMock');
            $auth->expects($this->any())
                ->method('isAuthenticated')
                ->will($this->returnValue(false))
            ;
            $auth->expects($this->any())
                ->method('isTokenValid')
                ->will($this->returnValue(false))
            ;
            $auth->expects($this->any())
                ->method('standardAuth')
                ->will($this->returnValue(false))
            ;
            
            \Phork::error()->clear();
            \Phork::instance()->register('auth', $auth, true);
            \Phork::router()->init('get', 'api/test/auth.json', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":true},"auth":null}');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::authenticate
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunAuthServerYes($api) 
        {
            $backup = $_SERVER;
            $_SERVER['PHP_AUTH_USER'] = 'user';
            $_SERVER['PHP_AUTH_PW'] = 'password';
        
            $auth = $this->getMock('\Phork\Test\AuthMock');
            $auth->expects($this->any())
                ->method('isAuthenticated')
                ->will($this->returnValue(false))
            ;
            $auth->expects($this->any())
                ->method('isTokenValid')
                ->will($this->returnValue(false))
            ;
            $auth->expects($this->any())
                ->method('standardAuth')
                ->will($this->returnValue(true))
            ;
        
            \Phork::error()->clear();
            \Phork::instance()->register('auth', $auth, true);
            \Phork::router()->init('get', 'api/test/auth.json', array());
            
            $api->run();
            $this->expectOutputString('{"meta":{"success":true},"auth":true}');
            
            $_SERVER = $backup;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Controllers\Api::run
         * @covers \Phork\Core\Controllers\Api::output
         * @covers \Phork\Core\Controllers\Api::encode
         */
        public function testRunNoEncoders($api) 
        {
            \Phork::error()->clear();
            \Phork::config()->encoder->delete('handlers');
            \Phork::config()->encoder->set('handlers', array());
            \Phork::router()->init('get', 'api/encoders.json', array());
            
            $api->run();
            $this->expectOutputString('500: Missing API encoders');
        }
    }
