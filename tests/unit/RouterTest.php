<?php
    use \Phork\Core\Router;
    
    /**
     * @covers \Phork\Core\Router
     */
    class RouterTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @covers \Phork\Core\Router::__construct
         */
        public function testInitialize() 
        {
            $router = new Router('', false, false);
            $this->assertInstanceOf('Phork\Core\Router', $router);
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getBaseUrl
         */
        public function testGetIndex()
        {
            $router = new Router('/index.php', false, false);
            $router->init('GET', '');
            
            $this->assertEquals('get', $router->getMethod());
            $this->assertEquals('/index.php', $router->getBaseUrl());
            
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getRelativeUrl
         */
        public function testPostIndex()
        {
            $router = new Router('', true, false);
            $router->init('POST', '/foo/bar/baz=42', array(
                'foo' => 'bar'
            ));
            
            $this->assertEquals('post', $router->getMethod());
            $this->assertEquals('/foo/bar/baz=42/', $router->getRelativeUrl());
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectCli
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getVariables
         * @covers \Phork\Core\Router::getCli
         */
        public function testCli()
        {
            $backup = $GLOBALS;
            $GLOBALS['argc'] = 3;
            $GLOBALS['argv'] = array('index.php', 'get', 'api/foo.xml');
            
            $router = new Router('', false, false);
            $router->detectCli();
            $router->init();
            
            $this->assertEquals('get', $router->getMethod());
            $this->assertEquals(array(), $router->getVariables());
            $this->assertTrue($router->getCli());
            
            $GLOBALS = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectCli
         * @covers \Phork\Core\Router::getMethod
         */
        public function testCliComplex()
        {
            $backup = $GLOBALS;
            $GLOBALS['argc'] = 4;
            $GLOBALS['argv'] = array('index.php', 'get', 'api/foo.xml', 'bar=baz&qux=42');
            
            $router = new Router('', false, false);
            $router->detectCli();
            $router->init();
            
            $this->assertEquals('get', $router->getMethod());
            
            $GLOBALS = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getVariable
         * @covers \Phork\Core\Router::getSecure
         */
        public function testGet()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['HTTPS'] = true;
            
            $router = new Router('', false, false);
            $router->init('get', 'site/index', array('foo' => 'bar'));
            
            $this->assertEquals('get', $router->getMethod());
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            $this->assertEquals(null, $router->getSegment(2));
            $this->assertEquals('bar', $router->getVariable('foo'));
            $this->assertTrue($router->getSecure());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getVariable
         * @covers \Phork\Core\Router::getFilter
         */
        public function testGetFilters()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            
            $router = new Router('', false, false);
            $router->init('get', 'site/index/foo=bar/baz=qux/multi=one/multi=two/');
            
            $this->assertEquals('get', $router->getMethod());
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            $this->assertEquals('bar', $router->getFilter('foo'));
            $this->assertEquals('qux', $router->getFilter('baz'));
            $this->assertEquals(array('one', 'two'), $router->getFilter('multi'));
            $this->assertNull($router->getFilter('xxx'));
            $this->assertNull($router->getVariable('xxx'));
           
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getExtension
         */
        public function testGetExtension()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            
            $router = new Router('', false, false);
            $router->init('get', 'site/index.html');
            
            $this->assertEquals('get', $router->getMethod());
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index.html', $router->getSegment(1));
            $this->assertEquals('html', $router->getExtension());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getRelativeUrl
         * @covers \Phork\Core\Router::getFullUrl
         */
        public function testGetFullUrl()
        {
            $router = new Router('', false, false);
            $router->init('get', '/site/foo=bar/index', array('foo' => 'bar', 'baz' => 'qux'));
            
            $this->assertEquals('/site/foo=bar/index', $router->getRelativeUrl());
            $this->assertEquals('/site/foo=bar/index', $router->getFullUrl(false, false));
            $this->assertEquals('/site/foo=bar/index?foo=bar&baz=qux', $router->getFullUrl(true, false));
            $this->assertEquals('/site/foo=bar/index?foo=bar&amp;baz=qux', $router->getFullUrl(true, true));
            
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getVariable
         */
        public function testPost()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'POST';
            
            $router = new Router('', false, false);
            $router->init('post', 'site/index', array('foo' => 'bar'));
            
            $this->assertEquals('post', $router->getMethod());
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            $this->assertEquals('bar', $router->getVariable('foo'));
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         */
        public function testPathInfo()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['PATH_INFO'] = '/';
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEquals('get', $router->getMethod());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getMethod
         */
        public function testRequestUri()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/';
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEquals('get', $router->getMethod());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::getRelativeUrl
         */
        public function testRequestUriFallback()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = null;
            $_SERVER['PATH_INFO'] = null;
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEquals('/', $router->getRelativeUrl());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::useQueryString
         * @covers \Phork\Core\Router::getSegment
         */
        public function testQueryString()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['QUERY_STRING'] = '/site/index/';
            
            $router = new Router('', false, false);
            $router->useQueryString();
            $router->init();
            
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::useQueryString
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getFilter
         * @covers \Phork\Core\Router::getVariable
         */
        public function testQueryStringComplex()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['QUERY_STRING'] = '/site/index/filter=42/?foo=bar&baz=qux';
            
            $router = new Router('', false, false);
            $router->useQueryString();
            $router->init();
            
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            $this->assertEquals(42, $router->getFilter('filter'));
            $this->assertEquals('bar', $router->getVariable('foo'));
            $this->assertEquals('qux', $router->getVariable('baz'));
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::useQueryString
         * @covers \Phork\Core\Router::getSegment
         * @covers \Phork\Core\Router::getFilter
         * @covers \Phork\Core\Router::getVariable
         */
        public function testQueryStringVariable()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['QUERY_STRING'] = 'path=/site/index/filter=42/&foo=bar&baz=qux';
            
            $router = new Router('', false, false);
            $router->useQueryString('path');
            $router->init();
            
            $this->assertEquals('site', $router->getSegment(0));
            $this->assertEquals('index', $router->getSegment(1));
            $this->assertEquals(42, $router->getFilter('filter'));
            $this->assertEquals('bar', $router->getVariable('foo'));
            $this->assertEquals('qux', $router->getVariable('baz'));
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectVariables
         * @covers \Phork\Core\Router::getVariable
         */
        public function testDetectVariablesPost()
        {
            $backup = array(
                '_SERVER' => $_SERVER,
                '_POST' => $_POST
            );
            
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = array(
                'foo' => 'bar',
                'baz' => 'qux'
            );
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEquals('bar', $router->getVariable('foo'));
            $this->assertEquals('qux', $router->getVariable('baz'));
            
            foreach ($backup as $key => $value) {
                $$key = $value; 
            }
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectVariables
         * @covers \Phork\Core\Router::getVariable
         */
        public function testDetectVariablesPostMixed()
        {
            $backup = array(
                '_SERVER' => $_SERVER,
                '_POST' => $_POST,
                '_GET' => $_GET
            );
            
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = array(
                'foo' => 'bar'
            );
            $_GET = array(
                'baz' => 'qux'
            );
            
            $router = new Router('', false, true);
            $router->init();
            
            $this->assertEquals('bar', $router->getVariable('foo'));
            $this->assertEquals('qux', $router->getVariable('baz'));
            
            foreach ($backup as $key => $value) {
                $$key = $value; 
            }
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectVariables
         * @covers \Phork\Core\Router::getRawData
         */
        public function testDetectVariablesPostRaw()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $GLOBALS['HTTP_RAW_POST_DATA'] = 'foobar';
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEquals('foobar', $router->getRawData());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectVariables
         * @covers \Phork\Core\Router::getVariables
         */
        public function testQueryStringVariablePut()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            
            $router = new Router('', false, false);
            $router->init();
            
            $this->assertEmpty($router->getVariables());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::setRoutes
         * @covers \Phork\Core\Router::routeUrl
         * @covers \Phork\Core\Router::getRoutedUrl
         */
        public function testRoutes()
        {
            $backup = $_SERVER;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            
            $router = new Router('', false, false);
            $router->setRoutes(array(
                '^/error/([0-9]{3}/?)'  => '/home/fatal/$1'
            ));
            $router->init('get', '/error/404/');
            
            $this->assertEquals('home', $router->getSegment(0));
            $this->assertEquals('fatal', $router->getSegment(1));
            $this->assertEquals('404', $router->getSegment(2));
            $this->assertEquals('/home/fatal/404/', $router->getRoutedUrl());
            
            $_SERVER = $backup;
            return $router;
        }
        
        /**
         * @covers \Phork\Core\Router::init
         * @covers \Phork\Core\Router::detectCli
         * @covers \Phork\Core\Router::__clone
         */
        public function testClone()
        {
            $router = new Router('', false, false);
            $router->detectCli();
            $router->init();
            
            $this->assertTrue($router->getCli());
            $clone = clone $router;
            
            $this->assertInstanceOf('Phork\Core\Router', $clone);
            $this->assertFalse($clone->getCli());
        }
    }
