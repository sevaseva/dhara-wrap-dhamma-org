# Changelog

All notable changes to the Wrap Dhamma.org WordPress plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] - 2024-11-03

### Added
- WordPress HTTP API integration using `wp_remote_get()` with proper timeout and SSL verification
- Transients-based caching system with configurable duration
- Admin settings page at Settings → Wrap Dhamma.org
- Shortcode support: `[dhamma_content page="vipassana" lang="en"]`
- Proper error handling using WP_Error instead of fatal errors
- Internationalization (i18n) support with text domain
- PHPDoc comments throughout the codebase
- Plugin activation/deactivation hooks
- Uninstall script for clean removal
- Cache management interface with clear cache functionality
- Enabled pages configuration in admin
- Proper plugin headers (Requires at least, Tested up to, Requires PHP, etc.)
- Comprehensive README.md with usage examples
- CHANGELOG.md following Keep a Changelog format
- Security improvements: nonces, capability checks, proper escaping
- Error logging for debugging

### Changed
- **BREAKING**: Refactored all functions with `wrap_dhamma_` prefix for better namespacing
- **BREAKING**: Main function now returns content instead of echoing directly
- Replaced deprecated `get_option('home')` with `home_url()`
- Replaced `file_get_contents()` with `wp_remote_get()` for HTTP requests
- Improved function naming consistency (snake_case throughout)
- Better error messages for end users
- Updated external URLs to use HTTPS
- Added `rel="noopener"` to external links for security
- Improved regex patterns for better performance
- Enhanced HTML content extraction and manipulation

### Fixed
- Security vulnerability: Remote content is now properly handled
- Performance issue: Added caching to prevent repeated remote requests
- XSS vulnerability: Added proper output escaping
- Error handling: Replaced `die()` with WP_Error
- Hardcoded plugin path: Now uses dynamic `WRAP_DHAMMA_PLUGIN_URL`
- Missing SSL verification on remote requests
- No timeout on remote requests could cause site hangs
- Missing error handling for network failures
- Video URL construction now uses HTTPS

### Removed
- None (backwards compatibility maintained where possible)

### Security
- Added nonce verification for admin actions
- Implemented capability checks for admin functions
- Added proper sanitization of user inputs
- URL escaping with `esc_url()`
- HTML attribute escaping with `esc_attr()`
- Text escaping with `esc_html()`
- SSL verification enabled for remote requests

## [3.01] - Previous Version

### Features
- Basic content retrieval from dhamma.org
- Support for multiple page types
- Language selection support
- Video page processing
- Image URL fixes
- HTML reformatting

### Known Issues
- Used deprecated WordPress functions
- No caching mechanism
- Used `file_get_contents()` for remote requests
- Fatal errors on invalid input
- No admin interface
- Security vulnerabilities
- Performance issues on high-traffic sites

## [Unreleased]

### Planned
- DOMDocument-based HTML parsing for better reliability
- WP-CLI commands for cache management
- REST API endpoint for programmatic access
- Multisite network support
- Performance monitoring and metrics
- Advanced cache strategies (object cache, CDN integration)
- Scheduled cache warming via WP-Cron
- Content modification filters for developers
- Improved error recovery mechanisms
