# Changelog

## [4.0.0] - In Progress

Major refactor in progress.

## [3.01] - Previous Version

Legacy version.

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
