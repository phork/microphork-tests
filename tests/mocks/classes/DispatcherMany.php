<?php
    namespace Phork\Test;

    /**
     * A simple dispatcher that allows many handlers.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class DispatcherMany extends \Phork\Core\Dispatcher
    {
        protected $instanceOf = '\\Phork\\Test\\Dispatcher\\Handlers\\HandlerInterface';
    }
