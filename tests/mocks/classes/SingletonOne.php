<?php
    namespace Phork\Test;
    
    /**
     * This has special methods to return some protected singleton data.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Core
     */
    class SingletonOne extends \Phork\Core\Singleton 
    {
        /**
         * Returns the instances of all singletons.
         *
         * @access public
         * @return array
         */
        public function instances()
        {
            return static::$instances;
        }
        
        /**
         * Returns the names of all the dereferenced singletons.
         *
         * @access public
         * @return array
         */
        public function dereferenced()
        {
            return static::$dereferenced;
        }
    }
