# Branch Comparison: claude1 vs claude2

## Summary

**Recommendation: Use claude1**

Claude1 contains the complete, working refactored code with consistent naming.
Claude2 was an attempt to build the same changes incrementally through individual commits, but has issues.

## Comparison

### Claude1 (Recommended)
- **Commits**: 18 (1 large refactor commit)
- **Lines**: 666
- **Status**: ✅ Complete and working
- **Naming**: ✅ Consistent `wrap_dhamma_` prefix throughout
- **Functions**: All 26 functions properly implemented
- **File Structure**: ✅ Clean, no syntax errors
- **Documentation**: ✅ Complete README, CHANGELOG, IMPROVEMENTS

### Claude2 (Experimental)
- **Commits**: 38 (incremental commits)
- **Lines**: 440 
- **Status**: ⚠️  Incomplete
- **Naming**: ❌ Mixed - some old names (fixURLs, stripH1, etc.)
- **Functions**: Only 17 functions, missing 9 critical ones
- **File Structure**: ❌ Had stray `?>` tags (now fixed)
- **Documentation**: Partial

## Missing Functions in Claude2

1. `wrap_dhamma_fetch_remote_content()` - replaced by incomplete `fetch_url()`
2. `wrap_dhamma_pull_video_page()` - video handling incomplete
3. `wrap_dhamma_fix_urls()` - exists as `fixURLs()` (inconsistent naming)
4. `wrap_dhamma_fix_video_urls()` - missing
5. `wrap_dhamma_strip_h1()` - exists as `stripH1()` (inconsistent naming)
6. `wrap_dhamma_strip_hr()` - exists as `stripHR()` (inconsistent naming)
7. `wrap_dhamma_change_tag()` - exists as `changeTag()` (inconsistent naming)
8. `wrap_dhamma_fix_goenka_images()` - exists as `fixGoenkaImages()` (inconsistent naming)
9. `wrap_dhamma_strip_table_tags()` - missing (needed for video pages)
10. `wrap_dhamma_strip_excess_video_line_breaks()` - missing
11. `wrap_dhamma_get_body_content()` - missing
12. `wrap_dhamma_fix_blue_ball_images()` - missing
13. `wrap_dhamma_strip_home_link()` - missing
14. `wrap_dhamma_settings_init()` - missing (admin settings incomplete)
15. `wrap_dhamma_settings_section_callback()` - missing
16. `wrap_dhamma_cache_duration_render()` - missing
17. `wrap_dhamma_enabled_pages_render()` - missing

## Issues with Incremental Approach (Claude2)

1. **Inconsistent Naming**: Didn't rename all functions with proper prefix
2. **Incomplete Features**: Missing video page processing functions
3. **No Settings Page**: Admin interface incomplete (no configuration fields)
4. **Heredoc Issues**: Adding code via heredoc introduced stray `?>` tags
5. **Missing Refactorings**: Many helper functions not renamed/refactored

## Why Claude1 is Better

1. **Complete Implementation**: All features working
2. **Consistent Code**: All functions use `wrap_dhamma_` prefix
3. **Better Documentation**: Full README with examples
4. **Proper Structure**: Clean PHP file with no syntax issues
5. **Full Admin Interface**: Complete settings page with cache duration config
6. **Video Support**: Complete video page processing
7. **Tested Approach**: Single refactor commit easier to verify

## Recommendation

**Use the claude1 branch** for the production plugin.

Claude2 was an interesting experiment in incremental commits but the
complexity of refactoring all functions while maintaining consistency
proved difficult through the heredoc approach.

## Git Commands

To use claude1:
```bash
git checkout claude1
```

To see the final state:
```bash
git show claude1:wrap-dhamma-org.php
```
