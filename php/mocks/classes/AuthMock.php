<?php
    namespace Phork\Test;

    /**
     * A spoofed auth class that can be mocked.
     *
     * @author Elenor Collings <elenor@phork.org>
     * @package \Phork\Test
     */
    class AuthMock
    {
        /**
         * Authenticates the user by username and password.
         *
         * @access public
         * @param string $username The username to authenticate
         * @param string $password The associated password
         * @return boolean True if authenticated successfully
         */
        public function standardAuth($username, $password) {}
        
        /**
         * Returns whether or not the user is authenticated.
         *
         * @access public
         * @return boolean True if authenticated
         */
        public function isAuthenticated() {}
        
        /**
         * Returns whether or not the token is valid.
         *
         * @access public
         * @param string $token The token to validate
         * @return boolean True if valid
         */
        public function isTokenValid($token) {}
    }
