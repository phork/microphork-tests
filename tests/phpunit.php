<?php
    require_once __DIR__.'/../vendor/autoload.php';
    date_default_timezone_set('UTC');
    
    (!defined('PHK_ENV')   && define('PHK_ENV',   'prod'));
    (!defined('TEST_PATH') && define('TEST_PATH', realpath(__DIR__.'/stubs').'/'));
    (!defined('CORE_PATH') && define('CORE_PATH', realpath(__DIR__.'/../vendor/microphork/framework/php/core').'/'));
    (!defined('APP_PATH')  && define('APP_PATH',  realpath(__DIR__.'/../vendor/microphork/framework/php/app').'/'));
    (!defined('VIEW_PATH') && define('VIEW_PATH', realpath(__DIR__.'/../vendor/microphork/framework/php/app/views').'/'));
    (!defined('PKG_PATH')  && define('PKG_PATH',  realpath(__DIR__.'/../vendor/microphork/packages').'/'));
    (!defined('LOG_PATH')  && define('LOG_PATH',  realpath(__DIR__.'/../logs/phpunit').'/'));
    
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
