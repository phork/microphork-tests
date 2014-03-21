<?php
    namespace Phork\Test\Iterators;

    /**
     * This has special handling to disallow any object added.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class AssociativeInvalid extends \Phork\Core\Iterators\Associative
    {
        /**
         * Validates the item being added to the list. Always will
         * return false.
         *
         * @access protected
         * @param mixed $item The record to validate
         * @return boolean True if allowed
         */
        protected function allowed($item)
        {
            return false;
        }
    }
