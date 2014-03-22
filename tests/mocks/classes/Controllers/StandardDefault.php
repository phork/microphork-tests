<?php
    namespace Phork\Test\Controllers;

    /**
     * The controller tests the non-restful handlers.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class StandardDefault extends \Phork\Core\Controllers\Standard
    {
        protected $restful = false;
    }
