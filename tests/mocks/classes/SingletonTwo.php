<?php
    namespace Phork\Test;
    
    /**
     * This has special methods to set and get data.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Core
     */
    class SingletonTwo extends \Phork\Core\Singleton 
    {
        protected $data = array(); 
        
        /**
         * A simple getter the data values.
         *
         * @access public
         * @param string $var The name of the data to get
         * @return mixed The data value
         */
        public function __get($var)
        {
            if (array_key_exists($var, $this->data)) {
                return $this->data[$var];
            }
        }
        
        /**
         * A simple setter the data values.
         *
         * @access public
         * @param string $var The name of the data to set
         * @return mixed The data value
         */
        public function __set($var, $val)
        {
            return $this->data[$var] = $val;
        }
    }
