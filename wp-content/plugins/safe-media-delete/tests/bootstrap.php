<?php

require_once 'D:/xampp/htdocs/Safe-Media-Delete/wp-load.php';

//require dirname(__FILE__) . '/../vendor/autoload.php';
define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname(__FILE__) . '/../vendor/yoast/phpunit-polyfills/src/');
define('WP_RUN_CORE_TESTS', 1);


require dirname(__FILE__) . '/../vendor/wp-phpunit/wp-phpunit/includes/functions.php';

function _manually_load_plugin() {
    require dirname(__FILE__) . '/../safe-media-delete.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require dirname(__FILE__) . '/../vendor/wp-phpunit/wp-phpunit/includes/bootstrap.php';
