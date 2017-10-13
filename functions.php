<?php
/**
 * Genesis Sample.
 *
 * This file adds functions to the Genesis Sample Theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://www.studiopress.com/
 */

// Start the engine.
include_once( get_template_directory() . '/lib/init.php' );

// Setup Theme.
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

add_action( 'after_setup_theme', 'genesis_sample_localization_setup' );
/**
 * Set Localization (do not remove).
 * @return void
 */
function genesis_sample_localization_setup(){
	load_child_theme_textdomain( 'genesis-sample', get_stylesheet_directory() . '/languages' );
}

// Add the helper functions.
include_once( get_stylesheet_directory() . '/lib/helper-functions.php' );

// Add Image upload and Color select to WordPress Theme Customizer.
require_once( get_stylesheet_directory() . '/lib/customize.php' );

// Include Customizer CSS.
include_once( get_stylesheet_directory() . '/lib/output.php' );

// Child theme (do not remove).
define( 'CHILD_THEME_NAME', 'CGWP_Dev' );
define( 'CHILD_THEME_URL', 'https://github.com/myersg86/cgwpdev' );
define( 'CHILD_THEME_VERSION', '2.3.1' );

add_action( 'wp_enqueue_scripts', 'genesis_sample_enqueue_scripts_styles' );
/**
 * Enqueue Scripts and Styles.
 * @return scripts and styles
 */

function genesis_sample_enqueue_scripts_styles() {
	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Magra:400,700|Oswald:300,400,600,700', array(), 
	wp_enqueue_style( 'dashicons' );

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'genesis-sample-responsive-menu', get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js", array( 'jquery' ), CHILD_THEME_VERSION, true );
	wp_localize_script(
		'genesis-sample-responsive-menu',
		'genesis_responsive_menu',
		genesis_sample_responsive_menu_settings()
	);

}

// Define our responsive menu settings.
function genesis_sample_responsive_menu_settings() {

	$settings = array(
		'mainMenu'          => __( 'Menu', 'genesis-sample' ),
		'menuIconClass'     => 'dashicons-before dashicons-menu',
		'subMenu'           => __( 'Submenu', 'genesis-sample' ),
		'subMenuIconsClass' => 'dashicons-before dashicons-arrow-down-alt2',
		'menuClasses'       => array(
			'combine' => array(
				'.nav-primary',
				'.nav-secondary',
			),
			'others'  => array(),
		),
	);

	return $settings;

}

// Add HTML5 markup structure.
add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
// Add Accessibility support.
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );
// Add viewport meta tag for mobile browsers.
add_theme_support( 'genesis-responsive-viewport' );


add_filter( 'wp_nav_menu_items', 'theme_menu_extras', 10, 2 );
	function theme_menu_extras( $menu, $args ) {
	//* Change 'primary' to 'secondary' to add extras to the secondary navigation menu
	if ( 'primary' !== $args->theme_location )
		return $menu;
	//* Uncomment this block to add a search form to the navigation menu
	ob_start();
	get_search_form();
	$search = ob_get_clean();
	$menu  .= '<li class="right search">' . $search . '</li>';
	return $menu;
}

// Add support for custom header.
add_theme_support( 'custom-header', array(
	'width'           => 198,
	'height'          => 132,
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'flex-height'     => true,
) );
					 
// Add support for custom background.
add_theme_support( 'custom-background' );

// Add support for after entry widget.
add_theme_support( 'genesis-after-entry-widget-area' );

// Add support for 3-column footer widgets.
add_theme_support( 'genesis-footer-widgets', 3 );

// Add Image Sizes.
add_image_size( 'featured-image', 720, 400, true );

// Rename primary and secondary navigation menus.
add_theme_support( 'genesis-menus', array(
	'primary'   => __( 'Primary Navigation Menu', 'genesis-sample' ),
	'secondary' => __( 'Secondary Navigation Menu', 'genesis-sample' ),
	'footer'    => __( 'Footer Navigation Menu', 'genesis-sample' ),
) );

                     
// Reposition the primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav' );

// Reduce the secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'genesis_sample_secondary_menu_args' );
function genesis_sample_secondary_menu_args( $args ) {
	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}
	$args['depth'] = 1;
	return $args;
}
                    
// Modify size of the Gravatar in the author box.
add_filter( 'genesis_author_box_gravatar_size', 'genesis_sample_author_box_gravatar' );
function genesis_sample_author_box_gravatar( $size ) {
	return 90;
}

// Modify size of the Gravatar in the entry comments.
add_filter( 'genesis_comment_list_args', 'genesis_sample_comments_gravatar' );
function genesis_sample_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;

	return $args;

}

remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
// Remove Header Right widget area.
unregister_sidebar( 'header-right' );

// Remove Primary Navigation's structural wrap.
add_theme_support( 'genesis-structural-wraps', array( 
	'header', 
	'menu-secondary', 
	'footer-widgets', 
	'footer' 
) );

add_filter( 'theme_page_templates', 'genesis_sample_remove_genesis_page_templates' );
/**
 * Remove Genesis Page Templates.
 *
 *
 * @param array $page_templates
 * @return array
 */
function genesis_sample_remove_genesis_page_templates( $page_templates ) {
	unset( $page_templates['page_archive.php'] );
	unset( $page_templates['page_blog.php'] );
	return $page_templates;
}

// Add single post navigation.
add_action( 'genesis_after_entry', 'genesis_prev_next_post_nav' );
add_action( 'genesis_after_loop', 'genesis_adjacent_entry_nav' );

add_action( 'genesis_theme_settings_metaboxes', 'genesis_sample_remove_metaboxes' );
/**
 * Remove Metaboxes
 * This removes unused or unneeded metaboxes from Genesis > Theme Settings.
 * See /genesis/lib/admin/theme-settings for all metaboxes.
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/code/remove-metaboxes-from-genesis-theme-settings/
 */
function genesis_sample_remove_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );
}

// Unregister content/sidebar/sidebar layout setting.
genesis_unregister_layout( 'content-sidebar-sidebar' );

// Unregister sidebar/sidebar/content layout setting.
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Unregister sidebar/content/sidebar layout setting.
genesis_unregister_layout( 'sidebar-content-sidebar' );

// Unregister secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Add typical attributes for footer navigation elements.
add_filter( 'genesis_attr_nav-footer', 'genesis_attributes_nav' );

// Display Footer Navigation Menu above footer content
add_action( 'genesis_footer', 'genesis_sample_do_footernav', 5 );
/**
 * Echo the "Footer Navigation" menu.
 *
 * @uses genesis_nav_menu() Display a navigation menu.
 * @uses genesis_nav_menu_supported() Checks for support of specific nav menu.
 */
function genesis_sample_do_footernav() {

	// Do nothing if menu not supported.
	if ( ! genesis_nav_menu_supported( 'footer' ) ) {
		return;
	}

	$class = 'menu genesis-nav-menu menu-footer';
	if ( genesis_superfish_enabled() ) {
		$class .= ' js-superfish';
	}

	genesis_nav_menu( array(
		'theme_location' => 'footer',
		'menu_class'     => $class,
	) );

}

add_filter( 'genesis_footer_creds_text', 'genesis_sample_footer_creds_filter' );
/**
 * Change Footer text.
 *
 * @link  https://my.studiopress.com/documentation/customization/shortcodes-reference/footer-shortcode-reference/
 */
function genesis_sample_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright before="Copyright "]';
	return $creds;
}
