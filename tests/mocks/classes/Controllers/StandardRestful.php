<?php
    namespace Phork\Test\Controllers;

    /**
     * The controller tests the restful handlers.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class StandardRestful extends \Phork\Core\Controllers\Standard
    {
        protected $restful = true;
        
        /**
         * Maps the get method to standard index page.
         *
         * @access public
         * @return void
         */
        public function getIndex()
        {
            $this->displayIndex();
        }
        
        
        /**
         * Maps the post method to standard index page.
         *
         * @access public
         * @return void
         */
        public function postIndex()
        {
            $this->displayIndex();
        }
    }
