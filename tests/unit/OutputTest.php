<?php
    use \Phork\Core\Output;
    use \Phork\Core\Event;
    
    /**
     * @covers \Phork\Core\Output
     */
    class OutputTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public static function setUpBeforeClass()
        {
            init_bootstrap();
            \Phork::instance()->register('event', Event::instance(true));
        }
        
        /**
         * @coversNothing
         */
        public static function tearDownAfterClass()
        {
            destroy_bootstrap();
        }
        
        /**
         * @covers \Phork\Core\Output::__construct
         */
        public function testInitialize() 
        {
            $output = Output::instance();
            $this->assertInstanceOf('Phork\Core\Output', $output);
            return $output;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::addContent
         */
        public function testAddContent($output)
        {
            $output->addContent('foobar');
            $this->expectOutputString('foobar');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::buffer
         * @covers \Phork\Core\Output::addContent
         * @covers \Phork\Core\Output::flush
         */
        public function testAddBufferedContent($output)
        {
            $output->buffer();
            $output->addContent('world', null);
            $output->addContent('hello', 0);
            $output->addContent(' ', 1);
            $output->flush();
            
            $this->expectOutputString('hello world');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::buffer
         * @covers \Phork\Core\Output::addContent
         * @covers \Phork\Core\Output::replaceContent
         * @covers \Phork\Core\Output::flush
         */
        public function testReplaceBufferedContent($output)
        {
            $output->buffer();
            
            $greeting = $question = null;
            $output->addContent('hello world', null, $greeting);
            $output->addContent('how are you', null, $question);
            
            $output->replaceContent($greeting, function($content) {
                return $content.'! ';
            });
            
            $output->replaceContent($question, function($content) {
                return $content.'?';
            });
            
            $output->flush();
            
            $this->expectOutputString('hello world! how are you?');
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::buffer
         * @covers \Phork\Core\Output::replaceContent
         * @covers \Phork\Core\Output::flush
         */
        public function testReplaceBufferedContentException($output)
        {
            $output->buffer();
            
            try {
                //this should always throw an exception but $output->flush still needs calling so only assertTrue should be called
                $output->replaceContent(1, function($content) {
                    return $content.$content;
                });
                $this->assertTrue(false);
            } catch (\PhorkException $exception) {
                $output->flush();
                $this->assertTrue(true);
            }
            
            return $output;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::replaceContent
         * @covers \Phork\Core\Output::flush
         * @expectedException PhorkException
         */
        public function testReplaceContentException($output)
        {
            $output->replaceContent(1, function($content) {
                return $content.$content;
            });
            $output->flush();
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::addTemplate
         */
        public function testAddTemplate($output)
        {
            \Phork::loader()->mapPath('View', $this->getHelperPath());
            $output->addTemplate('template', array(
                'greeting' => 'hello',
                'recipient' => 'world'
            ));
            
            $this->expectOutputString('hello world!');
            return $output;
        }
        
        /**
         * @depends testAddTemplate
         * @covers \Phork\Core\Output::addTemplate
         * @expectedException PhorkException
         */
        public function testAddTemplateException($output)
        {
            $output->addTemplate('xxx');
        }
        
        /**
         * @depends testAddTemplate
         * @covers \Phork\Core\Output::addTemplate
         */
        public function testAddBufferedTemplate($output)
        {
            $output->buffer();
            $output->addTemplate('template', array(
                'greeting' => 'hello',
                'recipient' => 'world'
            ));
            $output->flush();
            
            $this->expectOutputString('hello world!');
            return $output;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::getStatusCode
         */
        public function testGetStatusCode($output)
        {
            $result = $output->getStatusCode(404);
            $this->assertEquals('Not Found', $result);
            
            $result = $output->getStatusCode('xxx');
            $this->assertNull($result);
        }
                
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::setStatusCode
         * @covers \Phork\Core\Output::addHeader
         */
        public function testSetStatusCodeBuffered($output)
        {
            $output->buffer();
            $output->setStatusCode(404);
            $output->clear();
            
            return $output;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::setStatusCode
         * @expectedException PhorkException
         * @expectedExceptionMessage Invalid status code: 1
         */
        public function testSetStatusCodeNull($output)
        {
            $output->setStatusCode(1);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::setStatusCode
         * @covers \Phork\Core\Output::addHeader
         */
        public function testSetStatusCode($output)
        {
            $output->setStatusCode(404);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Output::__destruct
         */
        public function testDestruct($output)
        {
            $output->buffer();
            $output->addContent('foo');
            $output->__destruct();
            
            $this->expectOutputString('foo');
        }
        
        /**
         * @coversNothing
         */
        public function getHelperPath()
        {
            return __DIR__.'/../helpers/output/';
        }
    }
