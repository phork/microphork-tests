<?php
    namespace Phork\Test;

    /**
     * This has special handling to not send headers because headers will
     * break during testing.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class OutputNoHeaders extends \Phork\Core\Output
    {
        /**
         * Gracefully handles header_sent exceptions.
         *
         * @access public
         * @param string $header The complete header to send
         * @param integer $position The order to display the header in
         * @param string $id The unique ID of the header in the event object if output is buffered
         * @return object The instance of the output object
         */
        public function addHeader($header, $position = null, &$id = null)
        {
            try {
                parent::addHeader($header, $position, $id);
            } catch (\PhorkException $exception) {
                //do nothing; it's expected to break
            }

            return $this;
        }
    }
