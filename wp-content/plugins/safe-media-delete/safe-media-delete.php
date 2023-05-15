<?php
/**
 * Plugin Name: Safe Media Delete
 * Plugin URI: [Plugin URI]
 * Description: Safe Media Delete
 * Version: 1.0.0
 * Author: Ali Imran
 * Author URI: https://github.com/aalimaliimran/Safe-Media-Delete
 * License: GPL2
 */
// Add CMB2 library
require_once plugin_dir_path(__FILE__) . '../cmb2/init.php';

// Include plugin functionality
require_once plugin_dir_path(__FILE__) . 'includes/add-image-taxonomy-terms.php';
require_once plugin_dir_path(__FILE__) . 'includes/attached-object.php';
require_once plugin_dir_path(__FILE__) . 'includes/prevent-image-deletion.php';
require_once plugin_dir_path(__FILE__) . 'includes/api.php';

