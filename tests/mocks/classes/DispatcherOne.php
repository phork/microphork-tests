<?php
    namespace Phork\Test;

    /**
     * A simple dispatcher that allows only one handler.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class DispatcherOne extends \Phork\Core\Dispatcher
    {
        protected $instanceOf = '\\Phork\\Test\\Dispatcher\\Handlers\\HandlerInterface';
        protected $minimum = 1;
        protected $maximum = 1;
    }
