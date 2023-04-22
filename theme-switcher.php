<?php
/**
 * Plugin Name: Theme Switcher
 * Plugin URI: https://crispydiv.com
 * Description: Without preview, switch WordPress themes from the front-end Admin Bar. Best used in development.
 * Version: 1.0.0
 * Author: Sean Davis
 * Author URI: https://crispydiv.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: theme-switcher
 */

// Set the plugin version
const PLUGIN_VERSION = '1.0.0';

/**
 * Switch the theme
 *
 * @return void
 */
function theme_switcher_switch_theme() {

	if ( isset( $_POST['stylesheet'] ) ) {
		$stylesheet = $_POST['stylesheet'];
		switch_theme( $stylesheet );
	}

	wp_die();
}
add_action( 'wp_ajax_theme_switcher_switch_theme', 'theme_switcher_switch_theme' );
add_action( 'wp_ajax_nopriv_theme_switcher_switch_theme', 'theme_switcher_switch_theme' );

/**
 * Add the theme switcher menu to the Admin Bar
 *
 * @param $wp_admin_bar
 * @return void
 */
function theme_switcher_admin_bar_menu( $wp_admin_bar ) {

	if ( is_admin() ) {
		return;
	}

	$themes = wp_get_themes();
	$current_theme = wp_get_theme();
	$theme_menu = array();

	foreach ( $themes as $theme ) {
		$item = array(
			'id'    => 'theme_switcher_theme_' . $theme->get_stylesheet(),
			'title' => $theme->get('Name'),
			'href'  => '#',
			'meta'  => array(
				'onclick' => 'theme_switcher_switch_theme("' . $theme->get_stylesheet() . '")'
			)
		);
		if ( $theme->get_stylesheet() === $current_theme->get_stylesheet() ) {
			$item['meta']['class'] = 'theme-switcher-active';
		}
		$theme_menu[] = $item;
	}

	$wp_admin_bar->add_menu( array(
		'id'     => 'theme_switcher_theme_switcher',
		'title'  => 'Switch Theme',
		'href'   => false,
		'parent' => 'top-secondary',
	) );

	foreach ( $theme_menu as $item ) {
		$wp_admin_bar->add_menu( array(
			'id'     => $item['id'],
			'title'  => $item['title'],
			'href'   => $item['href'],
			'parent' => 'theme_switcher_theme_switcher',
			'meta'   => $item['meta']
		) );
	}
}
add_action( 'admin_bar_menu', 'theme_switcher_admin_bar_menu', 100 );

/**
 * Enqueue the theme switcher scripts and styles
 *
 * @return void
 */
function theme_switcher_enqueue_scripts() {

	wp_enqueue_style( 'theme-switcher', plugin_dir_url( __FILE__ ) . 'theme-switcher.css', array(), PLUGIN_VERSION );
	wp_enqueue_script( 'theme-switcher', plugin_dir_url( __FILE__ ) . 'theme-switcher.js', array( 'jquery' ), PLUGIN_VERSION, true );
	wp_localize_script( 'theme-switcher', 'theme_switcher_data', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' )
	) );
}
add_action( 'wp_enqueue_scripts', 'theme_switcher_enqueue_scripts' );
