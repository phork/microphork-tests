<?php
    namespace Phork\Test;

    /**
     * This has special handling to be able to call the cleanPath method.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class LoaderCleanPath extends \Phork\Core\Loader
    {
        /**
         * Replaces the slashed path with the appropriate directory
         * separator and optionally returns the realpath of the file.
         * If the file doesn't exist then realpath will return false.
         *
         * @access public
         * @param string $path The path name to clean
         * @param boolean $realpath Whether to return the realpath to the file
         * @return string The cleaned path or false on failure
         */
        public function publicCleanPath($path, $realpath = false)
        {
            return parent::cleanPath($path, $realpath);
        }
    }
