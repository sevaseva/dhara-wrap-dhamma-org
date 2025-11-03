<?php
/**
 * Uninstall script for Wrap Dhamma.org plugin.
 *
 * Fired when the plugin is uninstalled.
 *
 * @package WrapDhammaOrg
 * @since   4.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
exit;
}

// Clear all cached content.
global $wpdb;

// Delete all transients related to this plugin.
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
