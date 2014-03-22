<?php
    namespace Phork\Test\Dispatcher\Handlers;

    /**
     * The dispatcher interface defines a single passthru method.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test\Dispatcher
     */
    interface HandlerInterface
    {
        public function __construct($params = array());
        public function passthru();
    }
