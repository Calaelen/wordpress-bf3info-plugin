<?php
/*
Plugin Name: Battlefield 3 Info
Plugin URI: https://github.com/Calaelen/wordpress-bf3info-plugin
Description: 2013 Update! Display your Battlefield 3 Player Statistics from bf3stats.com in a sidebar widget. <strong>Show/hide values via the settings</strong> (e.g. hide Origin username).
Author: Calaelen
Author URI: http://www.calaelen.com/about/
Version: 0.2
*/
require_once(dirname( __FILE__ ) . '/inc/bf3stats-api.php');
require_once(dirname( __FILE__ ) . '/inc/bf3infobox-widget.php');
require_once(dirname( __FILE__ ) . '/inc/bf3infobox-options.php');


//--------------------------- Widget --------------------------//
add_action('widgets_init', 'bf3infobox_register_widget');
function bf3infobox_register_widget() {
    register_widget('bf3infobox');
}

add_action( 'wp_enqueue_scripts', 'bf3infobox_stylesheet' );
function bf3infobox_stylesheet() {
    wp_register_style( 'bf3infobox-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'bf3infobox-style' );
}

//--------------------------- Options Page --------------------------//
add_action('admin_menu', 'bf3infobox_register_optionspage');
function bf3infobox_register_optionspage() {
    bf3infobox_options::add_options_menu();
}

add_action('admin_init', 'bf3infobox_init_optionspage');
function bf3infobox_init_optionspage() {
    new bf3infobox_options();
}

register_activation_hook(__FILE__, 'bf3infobox_add_defaults');
function bf3infobox_add_defaults() {
    bf3infobox_options::add_default_options_on_activation();
}

//Settings Link - Info: http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
add_filter( 'plugin_row_meta', 'set_plugin_meta', 10, 2 );
function set_plugin_meta($links, $file) {
    $plugin = plugin_basename(__FILE__);
    if ($file == $plugin) {
        return array_merge(
            $links,
            array( sprintf( '<a href="options-general.php?page=bf3infobox-options">%s</a>', __('Settings') ) )
        );
    }
    return $links;
}

