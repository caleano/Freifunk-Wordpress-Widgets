<?php defined('ABSPATH') or die('NOPE');

/*
Plugin Name: Freifunk Wordpress Widgets
Plugin URI: https://caleano.com
Description: Ein Wordpress Plugin für Freifunk Frankfurt
Version: 1.0.1
Author: Igor Scheller <igor.scheller@igorshp.de>
Author URI: https://igorshp.de
License: MIT
*/

require_once __DIR__ . '/src/ContentParser.php';
require_once __DIR__ . '/src/InfoWidget.php';

add_action('widgets_init', function () {
    register_widget('Caleano\Freifunk\Widget\Info');
});
