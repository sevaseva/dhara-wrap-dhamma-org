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

/**
 * Fetch remote content with caching and proper error handling.
 *
 * @since 4.0.0
 * @param string $url URL to fetch.
 * @return string|WP_Error Content on success, WP_Error on failure.
 */
function fetch_url( $url ) {
	$cache_time = WRAP_DHAMMA_CACHE_EXPIRATION;

	// Create cache key from URL.
	$cache_key = 'wrap_dhamma_' . md5( $url );

	// Check cache first.
	$cached_content = get_transient( $cache_key );
	if ( false !== $cached_content ) {
		return $cached_content;
	}

	// Fetch from remote server.
	$response = wp_remote_get(
		$url,
		array(
			'timeout'    => 15,
			'sslverify'  => true,
			'user-agent' => 'WordPress/Wrap-Dhamma-Plugin/' . WRAP_DHAMMA_VERSION,
		)
	);

	// Handle errors.
	if ( is_wp_error( $response ) ) {
		error_log( 'Wrap Dhamma.org fetch error: ' . $response->get_error_message() );
		return $response;
	}

	$response_code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $response_code ) {
		$error_msg = sprintf(
			/* translators: 1: URL, 2: HTTP status code */
			__( 'Failed to fetch %1$s: HTTP %2$d', 'wrap-dhamma-org' ),
			$url,
			$response_code
		);
		error_log( 'Wrap Dhamma.org: ' . $error_msg );
		return new WP_Error( 'http_error', $error_msg );
	}

	$content = wp_remote_retrieve_body( $response );

	// Cache successful response.
	if ( ! empty( $content ) ) {
		set_transient( $cache_key, $content, $cache_time );
	}

	return $content;
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

/**
 * Fix internal URLs to point to local WordPress site.
 *
 * @since 1.0.0
 * @param string $raw  HTML content.
 * @param string $lang Language code.
 * @return string Modified content.
 */
function fixURLs ( $raw, $lang ) {
	$local_urls = array(
		'art'       => '/about/art-of-living/',
		'goenka'    => '/about/goenka/',
		'vipassana' => '/about/vipassana/',
		'/'         => '',
	);

	$home_url = home_url();

	foreach ( $local_urls as $from => $to ) {
		$raw = str_replace( '<a href="' . $from . '">', '<a href="' . esc_url( $home_url . $to ) . '">', $raw );
		$raw = str_replace( "<a href='" . $from . "'>", '<a href="' . esc_url( $home_url . $to ) . '">', $raw );
	}

	$raw = preg_replace( '#<a href=["\']/?code/?["\']>#', '<a href="' . esc_url( $home_url . '/courses/code-of-discipline/' ) . '">', $raw );
	$raw = str_replace( "<a href='/bycountry/'>", '<a target="_blank" rel="noopener" href="https://courses.dhamma.org/en-US/schedules/schdhara">', $raw );
	$raw = str_replace( "<a href='/docs/core/code-" . $lang . ".pdf'>here</a>",
		'<a href="https://www.dhamma.org/' . $lang . '/docs/core/code-' . $lang . '.pdf">here</a>', $raw );
	$raw = str_replace(
		'"/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"',
		'"https://www.dhamma.org/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"',
		$raw
	);

	return $raw;
}

/**
 * Strip H1 tags from content.
 *
 * @since 1.0.0
 * @param string $raw HTML content.
 * @return string Modified content.
 */
function stripH1( $raw ) {
	return preg_replace( '@<h1[^>]*?>.*?</h1>@si', '', $raw );
}

/**
 * Strip HR tags from content.
 *
 * @since 1.0.0
 * @param string $raw HTML content.
 * @return string Modified content.
 */
function stripHR ( $raw ) {
	return preg_replace( '@<hr.*?>@si', '', $raw );
}

/**
 * Change HTML tag type.
 *
 * @since 1.0.0
 * @param string $source  HTML content.
 * @param string $oldTag  Old tag name.
 * @param string $newTag  New tag name.
 * @return string Modified content.
 */
function changeTag ( $source, $oldTag, $newTag ) {
	$source = preg_replace( "@<{$oldTag}>@si", "<{$newTag}>", $source );
	$source = preg_replace( "@</{$oldTag}>@si", "</{$newTag}>", $source );
	return $source;
}

/**
 * Fix Goenka image URLs and styling.
 *
 * @since 1.0.0
 * @param string $raw HTML content.
 * @return string Modified content.
 */
function fixGoenkaImages ( $raw ) {
	// Fix image URLs.
	$raw = preg_replace( '#/images/sng/#si', 'https://www.dhamma.org/images/sng/', $raw );

	// Fix image alignment.
	$raw = str_replace( 'class="www-float-right-bottom"', "align='right'", $raw );
	$raw = str_replace( '<img alt="S. N. Goenka at U.N."', '<img alt="S. N. Goenka at U.N." style="display: block; margin-left: auto; margin-right: auto;"', $raw );
	$raw = str_replace( 'Photo courtesy Beliefnet, Inc.', '<p style="text-align:center">Photo courtesy Beliefnet, Inc.</p>', $raw );

	// Replace main Goenka image with local copy.
	$plugin_url = WRAP_DHAMMA_PLUGIN_URL;
	$raw        = str_replace(
		'src="https://www.dhamma.org/assets/sng/sng-f01f4d6595afa4ab14edced074a7e45c.gif"',
		'id="goenka-image" src="' . esc_url( $plugin_url . 'goenka.png' ) . '"',
		$raw
	);

	return $raw;
}

?>

/**
 * Shortcode handler for [dhamma_content].
 *
 * @since 4.0.0
 * @param array $atts Shortcode attributes.
 * @return string Formatted content or error message.
 */
function wrap_dhamma_shortcode( $atts ) {
$atts = shortcode_atts(
array(
'page' => 'vipassana',
'lang' => null,
),
$atts,
'dhamma_content'
);

$content = wrap_dhamma( sanitize_text_field( $atts['page'] ), $atts['lang'] );

if ( is_wp_error( $content ) ) {
return sprintf(
'<div class="dhamma-error">%s</div>',
esc_html( $content->get_error_message() )
);
}

return $content;
}
add_shortcode( 'dhamma_content', 'wrap_dhamma_shortcode' );

/**
 * Clear cached content for dhamma.org pages.
 *
 * @since 4.0.0
 * @param string|null $page Specific page to clear, or null for all.
 */
function wrap_dhamma_clear_cache( $page = null ) {
if ( null !== $page ) {
$allowed_pages = wrap_dhamma_get_allowed_pages();
if ( in_array( $page, $allowed_pages, true ) ) {
$lang = substr( get_bloginfo( 'language' ), 0, 2 );
if ( 'video' === $page ) {
$url = 'https://video.server.dhamma.org/video/';
} else {
$url = 'https://www.dhamma.org/' . $lang . '/' . $page . '?raw';
}
$cache_key = 'wrap_dhamma_' . md5( $url );
delete_transient( $cache_key );
}
} else {
// Clear all cached content.
global $wpdb;
$wpdb->query(
$wpdb->prepare(
"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
$wpdb->esc_like( '_transient_wrap_dhamma_' ) . '%'
)
);
$wpdb->query(
$wpdb->prepare(
"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
$wpdb->esc_like( '_transient_timeout_wrap_dhamma_' ) . '%'
)
);
}
}

/**
 * Add admin menu for plugin settings.
 *
 * @since 4.0.0
 */
function wrap_dhamma_add_admin_menu() {
add_options_page(
__( 'Wrap Dhamma.org Settings', 'wrap-dhamma-org' ),
__( 'Wrap Dhamma.org', 'wrap-dhamma-org' ),
'manage_options',
'wrap-dhamma-org',
'wrap_dhamma_options_page'
);
}
add_action( 'admin_menu', 'wrap_dhamma_add_admin_menu' );

/**
 * Render options page.
 *
 * @since 4.0.0
 */
function wrap_dhamma_options_page() {
// Handle cache clearing.
if ( isset( $_POST['wrap_dhamma_clear_cache'] ) && check_admin_referer( 'wrap_dhamma_clear_cache_action', 'wrap_dhamma_clear_cache_nonce' ) ) {
wrap_dhamma_clear_cache();
echo '<div class="notice notice-success"><p>' . esc_html__( 'Cache cleared successfully!', 'wrap-dhamma-org' ) . '</p></div>';
}

?>
<div class="wrap">
<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

<h2><?php esc_html_e( 'Cache Management', 'wrap-dhamma-org' ); ?></h2>
<form method="post">
<?php wp_nonce_field( 'wrap_dhamma_clear_cache_action', 'wrap_dhamma_clear_cache_nonce' ); ?>
<p><?php esc_html_e( 'Clear all cached content to force fresh retrieval from dhamma.org', 'wrap-dhamma-org' ); ?></p>
<button type="submit" name="wrap_dhamma_clear_cache" class="button button-secondary">
<?php esc_html_e( 'Clear Cache', 'wrap-dhamma-org' ); ?>
</button>
</form>

<hr>

<h2><?php esc_html_e( 'Usage', 'wrap-dhamma-org' ); ?></h2>
<p><?php esc_html_e( 'Use the shortcode in your posts or pages:', 'wrap-dhamma-org' ); ?></p>
<code>[dhamma_content page="vipassana"]</code>
<p><?php esc_html_e( 'Optional parameters:', 'wrap-dhamma-org' ); ?></p>
<ul>
<li><code>page</code> - <?php esc_html_e( 'Page to display (vipassana, code, goenka, art, qanda, dscode, osguide, privacy)', 'wrap-dhamma-org' ); ?></li>
<li><code>lang</code> - <?php esc_html_e( 'Language code (defaults to site language)', 'wrap-dhamma-org' ); ?></li>
</ul>
<p><?php esc_html_e( 'Example:', 'wrap-dhamma-org' ); ?> <code>[dhamma_content page="goenka" lang="en"]</code></p>
</div>
<?php
}
