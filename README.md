# Wrap Dhamma.org WordPress Plugin

A WordPress plugin that retrieves, reformats, and displays content from dhamma.org (Vipassana Meditation Centers) with intelligent caching, security improvements, and modern WordPress API integration.

## Description

This plugin allows WordPress sites to dynamically display content from dhamma.org, the official website of Vipassana meditation centers. It fetches pages in real-time (or from cache), reformats them to match your site's styling, and displays them seamlessly within your WordPress pages or posts.

### Features

- **Modern WordPress APIs**: Uses `wp_remote_get()` for HTTP requests instead of deprecated methods
- **Intelligent Caching**: Configurable caching system using WordPress Transients API (default: 6 hours)
- **Security Hardened**: Proper sanitization, escaping, and error handling
- **Multi-language Support**: Automatically detects site language or allows manual override
- **Admin Interface**: Settings page for cache management and configuration
- **Shortcode Support**: Easy integration using `[dhamma_content]` shortcode
- **Error Handling**: Graceful error messages instead of fatal errors
- **Performance Optimized**: Caching prevents repeated remote requests

## Installation

1. Download the plugin files
2. Upload the `wrap-dhamma-org` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure settings in Settings → Wrap Dhamma.org

## Usage

### Basic Shortcode

Display Vipassana introduction page:
```
[dhamma_content page="vipassana"]
```

### All Available Pages

- `vipassana` - Introduction to Vipassana meditation
- `code` - Code of discipline for courses
- `goenka` - About S.N. Goenka
- `art` - Art of Living
- `qanda` - Questions and Answers
- `dscode` - Dhamma Server code
- `osguide` - Old Student guide
- `privacy` - Privacy policy
- `video` - Video resources

### Shortcode Parameters

**page** (required) - Which page to display
```
[dhamma_content page="goenka"]
```

**lang** (optional) - Language code (defaults to site language)
```
[dhamma_content page="code" lang="es"]
```

### PHP Function (Advanced)

You can also call the function directly in your theme:
```php
<?php
if ( function_exists( 'wrap_dhamma' ) ) {
    $content = wrap_dhamma( 'vipassana', 'en' );
    if ( ! is_wp_error( $content ) ) {
        echo $content;
    }
}
?>
```

## Configuration

### Settings Page

Access the settings at **Settings → Wrap Dhamma.org**:

1. **Cache Duration**: How long to cache content (in seconds)
   - Default: 21,600 seconds (6 hours)
   - Minimum: 300 seconds (5 minutes)

2. **Enabled Pages**: Select which pages can be retrieved

3. **Clear Cache**: Force fresh content retrieval from dhamma.org

### Recommended Cache Duration

- **High-traffic sites**: 6-12 hours (reduces load on dhamma.org)
- **Low-traffic sites**: 1-3 hours (more current content)
- **Development**: 5-10 minutes (for testing)

## Technical Details

### Caching Mechanism

The plugin uses WordPress Transients API to cache remote content:
- Each page/language combination has a unique cache key
- Cache is automatically refreshed when expired
- Manual cache clearing available in admin panel
- Cache is cleared on plugin deactivation

### Security Features

- Uses `wp_remote_get()` with SSL verification
- Proper nonce verification for admin actions
- URL escaping with `esc_url()`
- HTML output sanitization options
- Capability checks for admin functions
- Error logging instead of fatal errors

### WordPress Compatibility

- **Requires at least**: WordPress 5.8
- **Tested up to**: WordPress 6.4
- **Requires PHP**: 7.4 or higher
- **License**: GPL v3 or later

## Frequently Asked Questions

### How often is content updated?

Content is cached for 6 hours by default. You can modify this in Settings → Wrap Dhamma.org.

### What happens if dhamma.org is down?

The plugin will serve cached content if available. If no cache exists, it displays a user-friendly error message without breaking your site.

### Can I use this for multiple pages?

Yes! Use multiple shortcodes on different pages, or even multiple shortcodes on the same page.

### Does this work with page builders?

Yes, the shortcode works with most page builders (Elementor, Beaver Builder, Divi, etc.).

### How do I clear the cache?

Go to Settings → Wrap Dhamma.org and click "Clear Cache".

### Can I style the content?

Yes, the content inherits your theme's styles. You can add custom CSS targeting the content wrapper.

## Changelog

### 4.0.0 (2024)
- **BREAKING**: Refactored to use modern WordPress APIs
- Added: WordPress HTTP API (`wp_remote_get`) instead of `file_get_contents`
- Added: Transients-based caching system
- Added: Admin settings page
- Added: Shortcode support `[dhamma_content]`
- Added: Proper error handling with WP_Error
- Added: Internationalization support (i18n)
- Fixed: Deprecated `get_option('home')` replaced with `home_url()`
- Fixed: Security improvements (sanitization, escaping, nonces)
- Fixed: Added proper plugin headers
- Fixed: PHPDoc comments throughout
- Fixed: Activation/deactivation hooks
- Improved: Performance with intelligent caching
- Improved: Error messages for better UX

### 3.01 (Previous)
- Legacy version
- Basic functionality without caching
- Used deprecated APIs

## Support

For issues, questions, or contributions:
- GitHub: https://github.com/sevaseva/dhara-wrap-dhamma-org
- Report bugs via GitHub Issues

## Credits

### Authors
- Joshua Hartwell (Original Author)
- Jeremy Dunn (Original Author)
- Contributors (Version 4.0 modernization)

### Related Resources
- [Dhamma.org](https://www.dhamma.org) - Official Vipassana website
- [Vipassana Meditation](https://www.dhamma.org/en/about/vipassana) - Learn about the technique

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, version 3 or later.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

## Acknowledgments

Special thanks to the Vipassana community and dhamma.org for making these teachings freely available.
