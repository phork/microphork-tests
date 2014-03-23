<?php
    namespace Phork\Test;
    
    /**
     * This has additional destruct checking.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Core
     */
    class SingletonThree extends \Phork\Core\Singleton 
    {
        /**
         * Throws an exception if destruct failed.
         *
         * @access public
         */
        public function __destruct()
        {
            parent::__destruct();
            
            if (array_key_exists($class = get_called_class(), static::$instances)) {
                throw new \PhorkException('Unable to destroy singleton');
            }
        }
    }
