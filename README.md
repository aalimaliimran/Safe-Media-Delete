# Safe-Media-Delete

Assignment Description
======================
https://drive.google.com/file/d/1J_nAoemkEAJYvlms-mnRmBUg5D5Egr4L/view?usp=sharing

Plugin Zip Download
=================


Wordpress Plugin Installation
=============================

Download the Plugin: Obtain the plugin files for "Safe Media Delete" and "CMB2" to your computer. You can find these files on the https://github.com/aalimaliimran/Safe-Media-Delete repository. path: wp-content/plugins/

Wordpress Installation: Install wordpress latest version on your computer localhost or server.

Upload the Plugin Files: Within the root directory of wordpress, locate the wp-content/plugins folder. Extract the "Safe Media Delete" plugin files you downloaded in the previous step. Upload the entire plugin folders (e.g., safe-media-delete) to the wp-content/plugins directory. you can exclude tests/ and composer.json file as its only required for test cases.

Install the Dependency (CMB2): Similarly, extract the CMB2 plugin files from its ZIP archive. Upload the entire plugin folder (e.g., cmb2) to the wp-content/plugins directory as well.

Activate the Dependency (CMB2): Log in to your WordPress admin panel. Navigate to the "Plugins" section and locate the CMB2 plugin. Click on the "Activate" link below the CMB2 plugin to activate it.

Activate the Plugin (Safe Media Delete): In the WordPress admin panel, go to the "Plugins" section. Look for the "Safe Media Delete" plugin in the list. Click on the "Activate" link below the plugin to activate it.

API:
=====
For media detail API test link: /wp-json/assignment/v1/media/{id}

For media delete API test link: /wp-json/assignment/v1/media/{id}/delete

Test Cases:
===========

Unit test cases for the "Safe Media Delete" plugin are located in the `tests/` folder. To ensure the proper execution of these test cases, it is necessary to install the required dependencies. 
To install the dependencies for running the unit test cases, you can use the following command:

```
composer update
```

This command will fetch and install the necessary libraries, including PHPUnit 9.5 or a higher version and the WordPress test framework.

Having PHPUnit 9.5 or a newer version is crucial for running the unit test cases effectively. PHPUnit is a widely used testing framework for PHP applications. It provides a range of features and assertions to facilitate comprehensive and automated testing.

The WordPress test framework is another important dependency for running the unit test cases. It provides specialized tools and utilities specifically designed for testing WordPress plugins and themes. By utilizing the WordPress test framework, you can easily create and execute tests that cover various aspects of the "Safe Media Delete" plugin's functionality.

By ensuring that these dependencies are properly installed, you can run the unit test cases for the "Safe Media Delete" plugin and verify the reliability and correctness of its features and functionalities.

Developer
=========

Ali Imran

aalim.ali.imran@gmail.com

