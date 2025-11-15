# Cookie Consent Manager for WordPress

A lightweight, GDPR and -compliant cookie consent WordPress plugin powered by [vanilla-cookieconsent](https://github.com/orestbida/cookieconsent) v3.1.0.


## Motivation

A blue ocean attempt to help other website developers and marketing agencies maintain regulatory compliance.

It is unfortunate that the individuals who _SHOULD_ hold the responsibility to protect an a person's privacy should be:

- The Producer of the pixel-delivering script AND recipient of the data!
- The Browser: That can offer default-safety (which all browser do except Chrome)
- The User: Who goes out of their way to allow it in their browser settings but is a victim if they didn't click it on each website.

Instead, website developers are responsible to (somehow) adhere to regional laws, regardless of the region the website lives.

California Penal Code § 637.2 (part of the California Invasion of Privacy Act (CIPA)) is well intended, but if people are to intentionally
strip off all of their close and run naked down the street, is it REALLY invading their privacy because you have cameras in public domain?

When asked to compare this philosophy of the "responsibility of data processors" to Payemnt Processors, etc, ChatGPT Characterizes this 
saying:

> "The problem is that privacy law in the U.S. is incoherent, inconsistent, and technologically illiterate in ways that other regulated 
> industries (finance, payments, medical devices, physical products) simply are not."

See more insights and in-depth discussion in the [GPT-LOL](./GPT-LOL/) folder.

## Features

- 🚀 **Ultra Lightweight** - Minimal performance impact
- 🔒 **GDPR Compliant** - Meets all GDPR requirements
- ⚡ **Zero Configuration Required** - Works immediately out of the box
- 🎨 **Fully Customizable** - Extensive admin options panel
- 📱 **Responsive Design** - Works perfectly on all devices
- 🌐 **Multi-language Ready** - Internationalization support
- 🔌 **MU Plugin Compatible** - Can be deployed as Must-Use plugin
- ♿ **Accessible** - WCAG compliant
- 🎯 **Modern PHP** - Built with PHP 8.1+ and WordPress 6.4+ best practices

## Requirements

- **WordPress**: 6.4 or higher
- **PHP**: 8.1 or higher

## Installation

### Standard Plugin Installation

1. Download the plugin files
2. Upload the `wp-cookieconsent` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. (Optional) Configure settings at Settings → Cookie Consent

### Must-Use (MU) Plugin Installation

1. Download the plugin files
2. Upload the `wp-cookieconsent` folder to `/wp-content/mu-plugins/`
3. Create a loader file `/wp-content/mu-plugins/wp-cookieconsent-loader.php`:

```php
<?php
/**
 * Plugin Name: Cookie Consent Manager (MU)
 * Description: MU Plugin loader for Cookie Consent Manager
 * Version: 1.0.0
 */

require_once WPMU_PLUGIN_DIR . '/wp-cookieconsent/wp-cookieconsent.php';
```

4. The plugin will activate automatically
5. (Optional) Configure settings at Settings → Cookie Consent

## Default Behavior

The plugin works immediately with sensible defaults:

- **Consent Mode**: Opt-in (GDPR compliant)
- **Modal Position**: Bottom right
- **Cookie Categories**: 
  - Necessary (always enabled)
  - Analytics (user choice)
  - Marketing (user choice)
- **Privacy Policy**: Automatically linked if configured in WordPress

## Configuration

Navigate to **Settings → Cookie Consent** in your WordPress admin panel to customize:

### General Settings
- Consent mode (opt-in/opt-out)
- Auto-show behavior
- Page interaction blocking
- Bot detection

### Appearance Settings
- Modal layout (box, cloud, bar)
- Modal position (top, middle, bottom + left, center, right)
- Button arrangement
- Preferences modal styling

### Text Content
- Modal titles and descriptions
- Button text customization
- Privacy policy links
- Multi-language support

### Cookie Categories
- Enable/disable analytics cookies by default
- Enable/disable marketing cookies by default
- Necessary cookies (always enabled, non-configurable)

## Usage for Developers

### Check if Category is Accepted

```javascript
if (CookieConsent.acceptedCategory('analytics')) {
    // Load analytics scripts
    console.log('Analytics cookies accepted');
}
```

### Accept Category Programmatically

```javascript
CookieConsent.acceptCategory('analytics');
```

### Show Preferences Modal

```javascript
CookieConsent.showPreferences();
```

### Get User Preferences

```javascript
const preferences = CookieConsent.getUserPreferences();
console.log(preferences);
```

### Managing Script Tags

Add `data-category` attribute to script tags that should only load with consent:

```html
<!-- This script will only load if analytics cookies are accepted -->
<script type="text/plain" data-category="analytics">
    // Your analytics code here
    gtag('config', 'GA_MEASUREMENT_ID');
</script>

<!-- This script will only load if marketing cookies are accepted -->
<script type="text/plain" data-category="marketing">
    // Your marketing/tracking code here
</script>
```

### WordPress Hooks

#### Filter Configuration

```php
add_filter('wp_cookieconsent_config', function($config) {
    // Modify configuration
    $config['mode'] = 'opt-out';
    return $config;
});
```

#### Check Accepted Categories in PHP

```php
// JavaScript bridge required - use inline script to communicate
add_action('wp_footer', function() {
    ?>
    <script>
    if (typeof CookieConsent !== 'undefined') {
        // Send consent status to server via AJAX if needed
        const preferences = CookieConsent.getUserPreferences();
        console.log('User preferences:', preferences);
    }
    </script>
    <?php
});
```

## Cookie Categories Explained

### Necessary Cookies
- Required for basic website functionality
- Cannot be disabled by users
- Examples: Session cookies, security cookies, load balancing

### Analytics Cookies
- Used to understand how visitors use the website
- Help improve website performance
- Examples: Google Analytics, Matomo, custom analytics

### Marketing Cookies
- Used for advertising and retargeting
- Track visitors across websites
- Examples: Facebook Pixel, Google Ads, retargeting pixels

## Styling Customization

The plugin uses the default cookieconsent CSS. To customize:

1. Create custom CSS in your theme:

```css
/* Override consent modal colors */
.cm {
    --cc-bg: #ffffff;
    --cc-text: #000000;
    --cc-btn-primary-bg: #2271b1;
    --cc-btn-primary-text: #ffffff;
}
```

2. Enqueue your custom styles:

```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('custom-cookieconsent', 
        get_stylesheet_directory_uri() . '/custom-cookieconsent.css',
        ['cookieconsent'],
        '1.0.0'
    );
}, 20);
```

## Troubleshooting

### Modal Not Showing

1. Check if JavaScript is loaded: View page source and look for `cookieconsent.umd.js`
2. Check browser console for JavaScript errors
3. Clear browser cache and WordPress cache
4. Ensure no JavaScript conflicts with other plugins

### Settings Not Saving

1. Check file permissions on WordPress uploads directory
2. Verify user has `manage_options` capability
3. Check for PHP errors in debug.log
4. Disable other plugins to check for conflicts

### Modal Appears Multiple Times

1. Check if plugin is activated both as regular and MU plugin
2. Ensure plugin is only included once in theme/child theme
3. Check for JavaScript conflicts with other consent plugins

## Performance

- **CSS Size**: ~25KB (minified)
- **JS Size**: ~35KB (minified)
- **No External Dependencies**: All assets loaded locally
- **Lazy Loading**: Modals generated only when needed
- **Caching Compatible**: Works with all major caching plugins

## Compliance

This plugin helps with GDPR compliance but doesn't guarantee it. You are responsible for:

- Having a valid privacy policy
- Properly configuring cookie categories
- Managing third-party scripts based on consent
- Handling data subject requests
- Consulting with legal counsel

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Credits

- **CookieConsent Library**: [orestbida/cookieconsent](https://github.com/orestbida/cookieconsent)
- **Plugin Development**: Custom WordPress integration

## License

This plugin is licensed under the GNU General Public License v3.0 or later (GPLv3+). See LICENSE file for details.

The vanilla-cookieconsent library is MIT licensed.

## Support

For issues, questions, or contributions:

1. Check the [official documentation](https://cookieconsent.orestbida.com)
2. Review closed issues in the repository
3. Open a new issue with detailed information

## Changelog

### 1.0.0 - 2025-11-15
- Initial release
- Full vanilla-cookieconsent v3.1.0 integration
- Admin settings panel with comprehensive options
- MU plugin compatibility
- PHP 8.1+ and WordPress 6.4+ support
- Zero-configuration default setup
- GDPR-compliant defaults

## Roadmap

- [ ] Multi-language translations (ES, FR, DE, IT, etc.)
- [ ] Color picker for theme customization
- [ ] Import/Export settings
- [ ] Consent log recording
- [ ] Integration with popular analytics plugins
- [ ] Visual theme presets
- [ ] Advanced script management UI
- [ ] Consent statistics dashboard

