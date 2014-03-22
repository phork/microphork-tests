<?php
    namespace Phork\Test;

    /**
     * This has special handling to print headers.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class OutputPrintHeaders extends \Phork\Core\Output
    {
        /**
         * Prints the headers.
         *
         * @access public
         * @param string $header The complete header to send
         * @param integer $position The order to display the header in
         * @param string $id The unique ID of the header in the event object if output is buffered
         * @return object The instance of the output object
         */
        public function addHeader($header, $position = null, &$id = null)
        {
            print $header;
            return $this;
        }
    }
