# Quick Start Guide - Cookie Consent Manager

Get up and running with Cookie Consent Manager in under 5 minutes!

## 🚀 Installation (Choose One Method)

### Method A: Standard WordPress Plugin (Easiest)

1. **Upload** the `wp-cookieconsent` folder to `/wp-content/plugins/`
2. **Activate** via WordPress Admin → Plugins
3. **Done!** Visit your site - the consent modal appears automatically

### Method B: Must-Use (MU) Plugin (Always Active)

1. **Upload** `wp-cookieconsent` folder to `/wp-content/mu-plugins/`
2. **Upload** `wp-cookieconsent-mu-loader.php` to `/wp-content/mu-plugins/`
3. **Done!** The plugin loads automatically (no activation needed)

## ✅ Verify Installation

1. Open your website in a **private/incognito window**
2. You should see a cookie consent modal appear
3. Test clicking "Accept All" or "Manage Preferences"
4. Refresh - modal should not appear again (consent remembered)

## 🎨 Customize (Optional)

### Access Settings
Go to: **WordPress Admin → Settings → Cookie Consent**

### Quick Customizations

**Change Position:**
```
Appearance Settings → Consent Modal Position
Choose: Top Right, Bottom Left, Middle Center, etc.
```

**Change Text:**
```
Text Content → Consent Modal Title
Change: "We use cookies" → "Your custom title"
```

**Change Layout:**
```
Appearance Settings → Consent Modal Layout
Choose: Box, Cloud, Bar, etc.
```

## 🔧 Basic Configuration

### Default Settings (Already Configured)
✓ Opt-in mode (GDPR compliant)  
✓ Auto-show enabled  
✓ Bot detection enabled  
✓ Three cookie categories (necessary, analytics, marketing)  
✓ Privacy policy auto-linked

### Recommended: Review Privacy Policy

1. Go to: **Settings → Privacy**
2. Select or create your privacy policy page
3. Add cookie information
4. Save changes

The plugin automatically links to this page in the consent modal.

## 📝 Managing Scripts

### Block Scripts Until Consent

Wrap analytics/tracking scripts with `data-category` attribute:

**Before:**
```html
<script src="https://www.google-analytics.com/analytics.js"></script>
<script>
    gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

**After:**
```html
<script type="text/plain" data-category="analytics" src="https://www.google-analytics.com/analytics.js"></script>
<script type="text/plain" data-category="analytics">
    gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Common Categories

- `data-category="necessary"` - Essential scripts (always load)
- `data-category="analytics"` - Google Analytics, Matomo, etc.
- `data-category="marketing"` - Facebook Pixel, Google Ads, etc.

## 🧪 Testing

### Test Consent Flow

1. **Open in Private Window**
   - Ensures clean state (no existing cookies)

2. **Test Accept All**
   - Click "Accept All"
   - Modal should close
   - Refresh page - modal should NOT reappear

3. **Test Reject All**
   - Clear cookies or new private window
   - Click "Reject All"
   - Only necessary cookies should be set

4. **Test Manage Preferences**
   - Click "Manage Preferences"
   - Toggle individual categories
   - Click "Save Preferences"
   - Verify only selected categories are active

### Check Console

Open browser DevTools (F12) and run:

```javascript
// Check if loaded
console.log(typeof CookieConsent);
// Should output: "object"

// Check consent status
console.log(CookieConsent.validConsent());
// true if consent given, false if not

// Check accepted categories
console.log(CookieConsent.getUserPreferences());
// Shows user's consent choices
```

## 🎯 Common Use Cases

### Use Case 1: Google Analytics

**In your theme or plugin:**

```html
<!-- Add to footer.php or use wp_footer hook -->
<script type="text/plain" data-category="analytics">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    
    ga('create', 'UA-XXXXX-Y', 'auto');
    ga('send', 'pageview');
</script>
```

### Use Case 2: Facebook Pixel

```html
<script type="text/plain" data-category="marketing">
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'YOUR_PIXEL_ID');
    fbq('track', 'PageView');
</script>
```

### Use Case 3: Custom Cookie

```javascript
// Check if analytics accepted before setting cookie
if (CookieConsent.acceptedCategory('analytics')) {
    document.cookie = "my_analytics_cookie=value; path=/; max-age=31536000";
    console.log('Analytics cookie set');
}
```

### Use Case 4: Show Preferences Button

Add a button anywhere on your site to let users change preferences:

```html
<button onclick="CookieConsent.showPreferences()">
    Cookie Settings
</button>
```

Common placement:
- Footer
- Privacy Policy page
- Account Settings

## 🐛 Troubleshooting

### Modal Not Showing?

**Check 1:** Clear all caches
- Browser cache (Ctrl+Shift+Delete)
- WordPress cache plugin
- CDN cache

**Check 2:** View page source
- Look for `cookieconsent.umd.js` in the HTML
- If missing, plugin may not be activated

**Check 3:** Check browser console (F12)
- Look for JavaScript errors
- Red errors indicate conflicts

### Settings Not Saving?

**Check 1:** User permissions
- Must be admin or have `manage_options` capability

**Check 2:** PHP errors
- Enable debug mode in `wp-config.php`:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  ```
- Check `/wp-content/debug.log`

### Still Having Issues?

1. **Disable other plugins** - test for conflicts
2. **Switch theme** - test with default WordPress theme
3. **Check PHP version** - must be 8.1 or higher
4. **Check WordPress version** - must be 6.4 or higher

## 🎓 Next Steps

### Learn More

- **Full Documentation**: Read [README.md](README.md)
- **Installation Guide**: See [INSTALL.md](INSTALL.md)
- **Official Docs**: Visit [cookieconsent.orestbida.com](https://cookieconsent.orestbida.com)

### Advanced Customization

1. **Custom Styles**: Override CSS variables
2. **Translations**: Create .po files for your language
3. **Hooks**: Use `wp_cookieconsent_config` filter
4. **Integration**: Connect with your analytics tools

### Best Practices

✓ Always use opt-in mode (GDPR compliant)  
✓ Keep privacy policy updated  
✓ Test after any site changes  
✓ Monitor consent rates  
✓ Respect user choices

## 📚 Resources

- [Official CookieConsent Documentation](https://cookieconsent.orestbida.com)
- [GDPR Requirements](https://gdpr.eu/)
- [WordPress Privacy Guide](https://wordpress.org/about/privacy/)
- [Cookie Policy Generator](https://www.cookiepolicygenerator.com/)

## 💡 Tips

1. **Test in Incognito** - Always test in private/incognito window
2. **Clear Cookies** - Use DevTools Application tab to clear cookies
3. **Check Network Tab** - Verify scripts load only after consent
4. **Mobile Testing** - Test on actual mobile devices
5. **Performance** - Plugin is lightweight (~60KB total)

## ✨ That's It!

You're all set! The cookie consent modal is now protecting your visitors' privacy and keeping your site GDPR compliant.

**Need help?** Check the troubleshooting section above or refer to the full documentation.

**Working great?** Consider leaving a review and sharing with others!

---

**Version**: 1.0.0  
**Last Updated**: 2025-11-15  
**Plugin Homepage**: [Cookie Consent Manager](https://github.com/orestbida/cookieconsent)

