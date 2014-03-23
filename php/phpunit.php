<?php
    (!defined('PHK_ENV')     && define('PHK_ENV',     'prod'));
    (!defined('VENDOR_PATH') && define('VENDOR_PATH', realpath(__DIR__.'/../../../').'/'));
    (!defined('PHORK_PATH')  && define('PHORK_PATH',  realpath(__DIR__.'/../../../../').'/'));
    (!defined('CORE_PATH')   && define('CORE_PATH',   realpath(PHORK_PATH.'php/core').'/'));
    (!defined('APP_PATH')    && define('APP_PATH',    realpath(PHORK_PATH.'php/app').'/'));
    (!defined('VIEW_PATH')   && define('VIEW_PATH',   realpath(PHORK_PATH.'php/app/views').'/'));
    (!defined('PKG_PATH')    && define('PKG_PATH',    realpath(VENDOR_PATH.'microphork/packages').'/'));
    (!defined('TEST_PATH')   && define('TEST_PATH',   realpath(__DIR__.'/mocks').'/'));
    (!defined('LOG_PATH')    && define('LOG_PATH',    realpath(__DIR__.'/../logs').'/'));
    
    require_once VENDOR_PATH.'autoload.php';
    date_default_timezone_set('UTC');
    
    class_alias('Phork\\Core\\Exception', 'PhorkException');
    class_alias('Phork\\Core\\Bootstrap', 'Phork');
    
    
    //helper function to initialize the bootstrap where it's needed
    function init_bootstrap() {
        \Phork::instance()
            ->register('loader', \Phork\Core\Loader::instance()
                ->mapPath('Core', CORE_PATH)
                ->mapPath('App',  APP_PATH)
                ->mapPath('View', VIEW_PATH)
                ->mapPath('Pkg',  PKG_PATH)
                ->mapPath('Test', TEST_PATH)
                ->addStack(\Phork::LOAD_STACK, array('Core', 'App', 'Test'))
            )
        ;
    }
    
    //helper function to destroy the bootstrap (HHVM needs explicit __destruct)
    function destroy_bootstrap() {
        if (!empty(\Phork::instance()->loader)) {
            \Phork::loader()->autoload(false);
        }
    
        !empty(\Phork::instance()->loader) && \Phork::instance()->deregister('loader')->__destruct();
        !empty(\Phork::instance()->event)  && \Phork::instance()->deregister('event')->__destruct();
        !empty(\Phork::instance()->output) && \Phork::instance()->deregister('output')->__destruct();
        \Phork::instance()->__destruct();
    }
