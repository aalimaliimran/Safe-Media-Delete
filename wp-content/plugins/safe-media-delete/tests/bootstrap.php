<?php
$_tests_dir = getenv('WP_TESTS_DIR') ?: '/path/to/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
    require dirname(__FILE__) . '/../safe-media-delete.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';
