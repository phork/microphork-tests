<?php
    namespace Encoder\Handlers;
    
    use \Phork\Core\Encoder\Handlers\Xml;
    
    /**
     * @covers \Phork\Core\Encoder\Handlers\Xml
     */
    class XmlTest extends \PHPUnit_Framework_TestCase 
    {
        protected $source = array(
            'foos' => array(
                'foo1',
                'foo2'
            ),
            'bars' => array(
                'bar1' => 'taco',
                'bar2' => 'beer'
            )
        );        
        
        /**
         * @coversNothing
         */
        public function testInitialize() 
        {
            $xml = new Xml();
            $this->assertInstanceOf('Phork\Core\Encoder\Handlers\Xml', $xml);
            return $xml;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Encoder\Handlers\Xml::encode
         * @covers \Phork\Core\Encoder\Handlers\Xml::build
         * @covers \Phork\Core\Encoder\Handlers\Xml::node
         */
        public function testEncode($xml)
        {
            $output = $xml->encode($this->source);
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><foos><node>foo1</node><node>foo2</node></foos><bars><bar1>taco</bar1><bar2>beer</bar2></bars></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Encoder\Handlers\Xml::encode
         * @covers \Phork\Core\Encoder\Handlers\Xml::build
         * @covers \Phork\Core\Encoder\Handlers\Xml::node
         */
        public function testEncodeNumericReplacements($xml)
        {
            $output = $xml->encode($this->source, array(
                'numericReplacements' => array(
                    'foos' => 'foo'
                )
            ));
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><foos><foo>foo1</foo><foo>foo2</foo></foos><bars><bar1>taco</bar1><bar2>beer</bar2></bars></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Encoder\Handlers\Xml::encode
         * @covers \Phork\Core\Encoder\Handlers\Xml::build
         * @covers \Phork\Core\Encoder\Handlers\Xml::node
         */
        public function testEncodeNumericPrefix($xml)
        {
            $output = $xml->encode($this->source, array(
                'numericPrefix' => 'foo'
            ));
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><foos><foo>foo1</foo><foo>foo2</foo></foos><bars><bar1>taco</bar1><bar2>beer</bar2></bars></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Encoder\Handlers\Xml::encode
         * @covers \Phork\Core\Encoder\Handlers\Xml::build
         * @covers \Phork\Core\Encoder\Handlers\Xml::node
         */
        public function testEncodeIncludeKeys($xml)
        {
            $output = $xml->encode($this->source, array(
                'includeKeys' => true
            ));
            
            $expected = new \DOMDocument;
            $result = $expected->loadXML('<root><foos><node key="0">foo1</node><node key="1">foo2</node></foos><bars><bar1>taco</bar1><bar2>beer</bar2></bars></root>');
            $this->assertTrue($result);
            
            $actual = new \DOMDocument;
            $result = $actual->loadXML($output);
            $this->assertTrue($result);
            
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
        }
    }
