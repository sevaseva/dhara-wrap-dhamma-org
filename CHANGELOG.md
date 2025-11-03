# Changelog

All notable changes to the Wrap Dhamma.org WordPress plugin.

## [4.0.0] - 2024-11-03

### Added
- Complete plugin headers with WordPress standards
- ABSPATH security check
- Plugin constants for version, paths, and cache duration
- Internationalization (i18n) support with text domain
- Helper function for allowed pages list
- Replace die() with WP_Error for proper error handling
- Return content instead of echoing (better control flow)
- Use gmdate() instead of date() for timezone safety
- URL escaping with esc_url()
- WordPress Transients API for intelligent caching (6-hour default)
- Proper HTTP error handling with status code checks
- 15-second timeout to prevent site hangs
- SSL verification enabled
- Replace deprecated get_option('home') with home_url()
- Add esc_url() for URL escaping
- Add rel="noopener" to external links for security
- Add PHPDoc comments to all HTML processing functions
- Use WRAP_DHAMMA_PLUGIN_URL constant instead of hardcoded path
- Improve code formatting and consistency
- Shortcode support: [dhamma_content]
- Admin settings page at Settings → Wrap Dhamma.org
- Cache management with clear cache functionality
- Plugin activation and deactivation hooks
- Complete uninstall.php with proper cleanup
- Comprehensive README documentation
- Usage examples and technical documentation

### Changed
- Refactored main wrap_dhamma() function with better error handling
- Improved fetch_url() with caching and proper HTTP handling
- Updated fixURLs() to use modern WordPress functions
- Enhanced HTML processing functions with documentation
- Improved code organization and formatting throughout

### Fixed
- Security issues with input validation and output escaping
- Performance issues by adding caching
- Deprecated function usage
- Error handling that could break sites
- Missing documentation

### Security
- Added nonce verification for admin actions
- Implemented capability checks
- Proper input sanitization
- Output escaping throughout
- SSL verification on remote requests

## [3.01] - Previous Version

Legacy version with basic functionality.
