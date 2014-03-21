<?php
    namespace Phork\Test\Dispatcher\Handlers;

    /**
     * Displays the debugging data on the screen. This should be used
     * as a handler for the Debug class.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test\Dispatcher
     */
    class Handler implements HandlerInterface
    {
        protected $action;

        
        /**
         * Sets up the handler's params including whether the debugging
         * display should use an HTML delimiter.
         *
         * @access public
         * @param array $params An array of params to set for each property
         * @return void
         */
        public function __construct($params = array())
        {
            foreach ($params as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }


        /**
         * Concatenates and prints or returns the arguments passed.
         *
         * @access public
         * @return mixed The output or nothing
         */
        public function passthru()
        {
            $args = func_get_args();
            $output = implode(': ', $args);
            
            switch ($this->action) {
                case 'print':
                    print $output;
                    break;
                    
                case 'return':
                    return $output;
            }
        }
    }
