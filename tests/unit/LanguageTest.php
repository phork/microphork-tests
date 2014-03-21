<?php
    use \Phork\Core\Language;
    
    /**
     * @covers \Phork\Core\Language
     */
    class LanguageTest extends PHPUnit_Framework_TestCase 
    {
        /**
         * @coversNothing
         */
        public function testInitialize() 
        {
            $language = new Language();
            $this->assertInstanceOf('Phork\Core\Language', $language);
            return $language;
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Language::translate
         */
        public function testTranslateOff($language)
        {
            $result = $language->translate('hello');
            $this->assertEquals('hello', $result);
        }
        
        /**
         * @depends testInitialize
         * @covers \Phork\Core\Language::setFilePaths
         */
        public function testSetFilePaths($language)
        {
            $language->setFilePaths(array($this->getHelperPath().'foo/'));
            return $language;
        }
        
        /**
         * @depends testSetFilePaths
         * @covers \Phork\Core\Language::setLanguage
         * @covers \Phork\Core\Language::load
         * @covers \Phork\Core\Language::loadFilePath
         */
        public function testSetLanguage($language)
        {
            $language->setLanguage('english');
            return $language;
        }
        
        /**
         * @depends testSetLanguage
         * @covers \Phork\Core\Language::translate
         */
        public function testTranslateFoo($language)
        {
            $result = $language->translate('hello');
            $this->assertEquals('hello foo', $result);
            
            $result = $language->translate('hello %s', 'world');
            $this->assertEquals('hello world', $result);
            
            return $language;
        }
        
        /**
         * @depends testTranslateFoo
         * @covers \Phork\Core\Language::translate
         * @covers \Phork\Core\Language::addFilePath
         * @covers \Phork\Core\Language::loadFilePath
         * @covers \Phork\Core\Language::files
         */
        public function testAddFilePath($language)
        {
            $result = $language->translate('taco');
            $this->assertEquals('taco', $result);
            
            $language->addFilePath($this->getHelperPath().'bar/');
            
            $result = $language->translate('taco');
            $this->assertEquals('taco bar', $result);
            
            return $language;
        }
        
        /**
         * @depends testTranslateFoo
         * @covers \Phork\Core\Language::translate
         * @covers \Phork\Core\Language::addFilePath
         * @covers \Phork\Core\Language::loadFilePath
         * @covers \Phork\Core\Language::files
         */
        public function testAddFilePathNone($language)
        {
            $language->addFilePath('');
            return $language;
        }
        
        /**
         * @depends testAddFilePath
         * @covers \Phork\Core\Language::translate
         * @covers \Phork\Core\Language::addFilePath
         * @covers \Phork\Core\Language::loadFilePath
         * @covers \Phork\Core\Language::files
         * @expectedException PhorkException
         */
        public function testTranslateException($language)
        {
            $language->addFilePath($this->getHelperPath().'broken/');
            $language->translate('hello');
        }
        
        /**
         * @depends testTranslateFoo
         * @covers \Phork\Core\Language::setCachePath
         */
        public function testSetCachePath($language)
        {
            $language->setCachePath($this->getHelperPath().'cached/');
            
            $result = $language->translate('hello');
            $this->assertEquals('hello', $result);
            
            $result = $language->translate('dog');
            $this->assertEquals('wrigley', $result);
        }
        
        /**
         * @coversNothing
         */
        public function getHelperPath()
        {
            return __DIR__.'/../helpers/language/';
        }
    }
