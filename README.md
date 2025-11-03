# Wrap Dhamma.org WordPress Plugin

A WordPress plugin that retrieves, reformats, and displays content from dhamma.org (Vipassana Meditation Centers) with intelligent caching, security improvements, and modern WordPress API integration.

## Description

This plugin allows WordPress sites to dynamically display content from dhamma.org, the official website of Vipassana meditation centers. It fetches pages in real-time (or from cache), reformats them to match your site's styling, and displays them seamlessly within your WordPress pages or posts.

### Features

- **Modern WordPress APIs**: Uses `wp_remote_get()` for HTTP requests
- **Intelligent Caching**: Transients API caching (6-hour default)
- **Security Hardened**: Proper sanitization, escaping, and error handling
- **Multi-language Support**: Detects site language or allows manual override
- **Admin Interface**: Settings page for cache management
- **Shortcode Support**: Easy integration using `[dhamma_content]`
- **Error Handling**: Graceful error messages
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

### Available Pages

- `vipassana` - Introduction to Vipassana meditation
- `code` - Code of discipline for courses
- `goenka` - About S.N. Goenka
- `art` - Art of Living
- `qanda` - Questions and Answers
- `dscode` - Dhamma Server code
- `osguide` - Old Student guide
- `privacy` - Privacy policy

### Shortcode Parameters

**page** - Which page to display
```
[dhamma_content page="goenka"]
```

**lang** - Language code (optional, defaults to site language)
```
[dhamma_content page="code" lang="es"]
```

## Configuration

Access settings at **Settings → Wrap Dhamma.org**:

- **Clear Cache**: Force fresh content retrieval
- **Usage Examples**: Shortcode documentation

## Technical Details

### Caching Mechanism

- Uses WordPress Transients API
- Default 6-hour cache duration
- Per-page/per-language cache keys
- Manual cache clearing available

### Security Features

- `wp_remote_get()` with SSL verification
- Nonce verification for admin actions
- URL escaping with `esc_url()`
- Error logging instead of exposing errors
- Capability checks for admin functions

### Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **License**: GPL v3 or later

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## Credits

### Authors
- Joshua Hartwell (Original Author)
- Jeremy Dunn (Original Author)

## License

GPL v3 or later. See [gpl-3.0.txt](gpl-3.0.txt) for details.
