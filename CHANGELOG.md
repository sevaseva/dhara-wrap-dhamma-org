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
