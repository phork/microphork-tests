<?php
    namespace Phork\Test\Api;

    /**
     * A simple API class.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class Test extends \Phork\Core\Api
    {
        protected $restful = true;
        
        /**
         * Handles a GET request to /api/test/success.*
         *
         * @access protected
         * @return void
         */
        protected function getSuccess()
        {
            $this->success = true;
            $this->result = array(
                'hello' => 'world'
            );
        }
        
        /**
         * Handles a POST request to /api/test/error.*
         *
         * @access protected
         * @return void
         */
        protected function postError()
        {
            $this->success = false;
            $this->statusCode = 400;
            $this->result = array();
        }
        
        /**
         * Handles a GET request to /api/test/success.*
         *
         * @access protected
         * @return void
         */
        protected function getAuth()
        {
            $this->success = true;
            $this->result = array(
                'auth' => $this->authenticated
            );
        }
        
        /**
         * Throws an exception.
         *
         * @access protected
         * @return void
         */
        protected function getException()
        {
            throw new \PhorkException('Exception');
        }
    }
