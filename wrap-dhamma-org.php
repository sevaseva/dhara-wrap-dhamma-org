<?php
/**
 * Plugin Name: Wrap Dhamma.org
 * Plugin URI: https://github.com/sevaseva/dhara-wrap-dhamma-org
 * Description: Retrieves, re-formats, and displays content from dhamma.org with caching and security improvements
 * Version: 4.0.0
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Author: Joshua Hartwell, Jeremy Dunn
 * Author URI: https://github.com/sevaseva
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wrap-dhamma-org
 * Domain Path: /languages
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 or later.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>
 *
 * @package WrapDhammaOrg
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'WRAP_DHAMMA_VERSION', '4.0.0' );
define( 'WRAP_DHAMMA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WRAP_DHAMMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WRAP_DHAMMA_CACHE_EXPIRATION', HOUR_IN_SECONDS * 6 ); // 6 hours default

// Initialize plugin.
add_action( 'plugins_loaded', 'wrap_dhamma_init' );

/**
 * Initialize plugin (load text domain for translations).
 *
 * @since 4.0.0
 */
function wrap_dhamma_init() {
	load_plugin_textdomain( 'wrap-dhamma-org', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Get list of allowed pages.
 *
 * @since 4.0.0
 * @return array Array of allowed page slugs.
 */
function wrap_dhamma_get_allowed_pages() {
	return array( 'vipassana', 'code', 'goenka', 'art', 'qanda', 'dscode', 'osguide', 'privacy', 'video' );
}

function fetch_url( $url ) {
	$r = wp_remote_get( $url );
	if ( is_wp_error( $r ) ) {
		// TODO(vlotoshnikov@gmail.com): Retry once and then
		// return '<script>window.location.replace(substr($url, 0, -4))</script>';
		// or even
		// echo '<script>window.location.replace(substr($url, 0, -4))</script>'; return '';
		// if second attempt also fails?
	}
	return wp_remote_retrieve_body( $r );
}

/**
 * Main function to wrap and display dhamma.org content.
 *
 * @since 1.0.0
 * @param string      $page Page slug to retrieve.
 * @param string|null $lang Language code (defaults to site language).
 * @return string|WP_Error Formatted content or error.
 */
function wrap_dhamma( $page, $lang = null ) {
	$lang = isset( $lang ) ? $lang : substr( get_bloginfo( 'language' ), 0, 2 );
	
	// Validate page.
	$allowed_pages = wrap_dhamma_get_allowed_pages();
	if ( ! in_array( $page, $allowed_pages, true ) ) {
		$error_msg = sprintf(
			/* translators: %s: page slug */
			__( 'Invalid page requested: %s', 'wrap-dhamma-org' ),
			esc_html( $page )
		);
		error_log( 'Wrap Dhamma.org: ' . $error_msg );
		return new WP_Error( 'invalid_page', $error_msg );
	}

	// Build URL based on page type.
	if ( 'video' === $page ) {
		$url = 'https://video.server.dhamma.org/video/';
		$text_to_output = pull_video_page( $url );
	} else {
		$url = 'https://www.dhamma.org/' . $lang . '/' . $page . '?raw';
		$text_to_output = pull_page( $url, $lang );
	}

	// Handle errors.
	if ( is_wp_error( $text_to_output ) ) {
		return $text_to_output;
	}

	if ( false === $text_to_output || empty( $text_to_output ) ) {
		return new WP_Error( 'empty_content', __( 'No content retrieved from dhamma.org', 'wrap-dhamma-org' ) );
	}

	// Build output with comments.
	$output = sprintf(
		"<!-- %s dynamically reformatted on %s -->\n",
		esc_url( $url ),
		gmdate( 'D M j G:i:s Y T' )
	);
	$output .= $text_to_output;
	$output .= "\n<!-- end dynamically generated content -->";

	return $output;
}

function prepare_html( $html, $lang ) {
	$raw = fixURLs ( $html, $lang );
	$raw = stripH1 ( $raw );
	$raw = stripHR ( $raw );
	$raw = changeTag ( $raw, "h3", "h2" );
	$raw = changeTag ( $raw, "h4", "h3" );
	$raw = fixGoenkaImages ( $raw );
	return $raw;
}

function pull_page ( $url, $lang ) {
	$raw = fetch_url ( $url );
	return prepare_html($raw, $lang);
}

const LOCAL_URLS = array(
	'art' => '/vipassana/art-of-living/',
	'goenka' => '/vipassana/teacher-goenka/',
	'vipassana' => '/vipassana/about/',
	'/' => '',
);

function fixURLs ( $raw, $lang ) {
	foreach ( LOCAL_URLS as $from => $to ) {
		$raw = str_replace('<a href="' . $from . '">', '<a href="' . get_option('home') . $to . '">', $raw);
		$raw = str_replace("<a href='" . $from . "'>", '<a href="' . get_option('home') . $to . '">', $raw);
	}

	$raw = preg_replace("#<a href=[\"']/?code/?[\"']>#", '<a href="' . get_option('home') . '/courses/code-of-discipline/">', $raw);
	$raw = str_replace("<a href='/bycountry/'>", '<a target="_blank" href="' . get_theme_mod( 'dhamma_schedule_link' ) . '">', $raw);
	$raw = str_replace("<a href='/docs/core/code-" . $lang . ".pdf'>here</a>",
		"<a href='https://www.dhamma.org/" . $lang . "/docs/core/code-" . $lang . ".pdf'>here</a>", $raw);
	$raw = str_replace('"/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"',
		'"https://www.dhamma.org/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"', $raw);
	return $raw;
}

function stripH1( $raw ) {
	return preg_replace('@<h1[^>]*?>.*?<\/h1>@si', '', $raw); //This isn't a great solution, not very dynamic, but it gets the job done.
}

function stripHR ( $raw ) {
	return preg_replace("@<hr.*?>@si", '', $raw);
}

function changeTag ( $source, $oldTag, $newTag ) {
	$source = preg_replace( "@<{$oldTag}>@si", "<{$newTag}>", $source );
	$source = preg_replace( "@</{$oldTag}>@si", "</{$newTag}>", $source );
	return $source;
}

function fixGoenkaImages ( $raw ) {
	//Make the Goenkaji images work - JDH 10/12/2014
	$raw = preg_replace( '#/images/sng/#si', 'https://www.dhamma.org/images/sng/', $raw );

	//Make the goenka images inline - JDH 10/12/2014
	$raw = str_replace('class="www-float-right-bottom"', "align='right'", $raw);
	$raw = str_replace('<img alt="S. N. Goenka at U.N."', '<img alt="S. N. Goenka at U.N." style="display: block; margin-left: auto; margin-right: auto;"', $raw);
	$raw = str_replace('Photo courtesy Beliefnet, Inc.', '<p style="text-align:center">Photo courtesy Beliefnet, Inc.</p>', $raw);

	return $raw;
}

?>
