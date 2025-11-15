# Installation Guide - Cookie Consent Manager

This guide provides step-by-step instructions for installing the Cookie Consent Manager plugin.

## Table of Contents

1. [Requirements](#requirements)
2. [Standard Plugin Installation](#standard-plugin-installation)
3. [Must-Use (MU) Plugin Installation](#must-use-mu-plugin-installation)
4. [First-Time Configuration](#first-time-configuration)
5. [Verification](#verification)
6. [Troubleshooting](#troubleshooting)

## Requirements

Before installing, ensure your WordPress site meets these requirements:

- **WordPress Version**: 6.4 or higher
- **PHP Version**: 8.1 or higher
- **Memory Limit**: 64MB minimum (128MB recommended)
- **File Permissions**: Write access to wp-content directory

To check your PHP version:
1. Go to WordPress Admin → Tools → Site Health
2. Click "Info" tab
3. Check "Server" section for PHP version

## Standard Plugin Installation

### Method 1: Upload via WordPress Admin (Recommended)

1. **Download the Plugin**
   - Download the `wp-cookieconsent` folder as a ZIP file
   - Ensure the ZIP contains the folder structure correctly

2. **Upload to WordPress**
   - Log in to your WordPress admin dashboard
   - Navigate to **Plugins → Add New**
   - Click **Upload Plugin** button at the top
   - Click **Choose File** and select the ZIP file
   - Click **Install Now**

3. **Activate the Plugin**
   - After installation, click **Activate Plugin**
   - You should see a success message

4. **Verify Installation**
   - Visit your website's frontend
   - You should see the cookie consent modal appear

### Method 2: Manual FTP/SFTP Installation

1. **Prepare Files**
   - Extract the plugin ZIP file on your computer
   - You should have a folder named `wp-cookieconsent`

2. **Upload via FTP**
   - Connect to your server using FTP/SFTP client (FileZilla, etc.)
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `wp-cookieconsent` folder
   - Ensure all files and folders are uploaded completely

3. **Set Permissions**
   - Files: 644 (rw-r--r--)
   - Directories: 755 (rwxr-xr-x)

4. **Activate Plugin**
   - Log in to WordPress admin
   - Go to **Plugins → Installed Plugins**
   - Find "Cookie Consent Manager"
   - Click **Activate**

### Method 3: WP-CLI Installation

If you have WP-CLI installed:

```bash
# Navigate to your WordPress directory
cd /path/to/wordpress

# Copy plugin folder
cp -r /path/to/wp-cookieconsent wp-content/plugins/

# Activate plugin
wp plugin activate wp-cookieconsent

# Verify activation
wp plugin list
```

## Must-Use (MU) Plugin Installation

MU (Must-Use) plugins are automatically loaded by WordPress without needing activation. This is ideal for sites where you want cookie consent always active.

### Step 1: Prepare the Plugin

1. Extract the `wp-cookieconsent` folder
2. Ensure you also have the `wp-cookieconsent-mu-loader.php` file

### Step 2: Upload Files

Using FTP/SFTP:

1. **Upload Plugin Folder**
   - Navigate to `/wp-content/mu-plugins/`
   - If `mu-plugins` folder doesn't exist, create it
   - Upload the entire `wp-cookieconsent` folder
   - Final path: `/wp-content/mu-plugins/wp-cookieconsent/`

2. **Upload Loader File**
   - Upload `wp-cookieconsent-mu-loader.php` to `/wp-content/mu-plugins/`
   - Final path: `/wp-content/mu-plugins/wp-cookieconsent-mu-loader.php`

### Step 3: Verify Directory Structure

Your directory structure should look like this:

```
wp-content/
├── mu-plugins/
│   ├── wp-cookieconsent-mu-loader.php
│   └── wp-cookieconsent/
│       ├── assets/
│       │   ├── admin.css
│       │   ├── admin.js
│       │   ├── cookieconsent.css
│       │   └── cookieconsent.umd.js
│       ├── languages/
│       │   └── wp-cookieconsent.pot
│       ├── LICENSE
│       ├── README.md
│       ├── uninstall.php
│       └── wp-cookieconsent.php
└── plugins/
    └── (other plugins)
```

### Step 4: Verify MU Plugin is Active

1. Log in to WordPress admin
2. Go to **Plugins → Must-Use**
3. You should see "Cookie Consent Manager (MU Loader)" listed
4. MU plugins are automatically active (no activation button)

### Using WP-CLI for MU Installation

```bash
# Create mu-plugins directory if it doesn't exist
mkdir -p wp-content/mu-plugins

# Copy plugin folder
cp -r /path/to/wp-cookieconsent wp-content/mu-plugins/

# Copy loader file
cp /path/to/wp-cookieconsent-mu-loader.php wp-content/mu-plugins/

# Verify
wp plugin list --status=must-use
```

## First-Time Configuration

The plugin works immediately with default settings, but you can customize it:

### Access Settings

1. Log in to WordPress admin
2. Navigate to **Settings → Cookie Consent**
3. Configure options as needed

### Recommended Initial Settings

1. **General Settings**
   - Keep "Consent Mode" as "Opt-in" (GDPR compliant)
   - Enable "Auto Show"
   - Enable "Hide from Bots"

2. **Appearance Settings**
   - Choose layout and position that suits your design
   - Default: "Box" layout at "Bottom Right"

3. **Text Content**
   - Review and customize text to match your brand voice
   - Ensure Privacy Policy link is correct

4. **Save Settings**
   - Click "Save Settings" button
   - You'll see a success message

## Verification

### Frontend Verification

1. **Open Your Website in Incognito/Private Window**
   - This ensures you're not seeing cached content
   - Cookie consent should appear immediately

2. **Test Consent Modal**
   - Modal should appear on page load
   - Try clicking "Accept All"
   - Modal should close and not reappear

3. **Test Preferences**
   - Clear cookies or open another private window
   - Click "Manage Preferences"
   - Preferences modal should open
   - Toggle categories on/off
   - Click "Save Preferences"

4. **Verify Persistent Consent**
   - After accepting, refresh the page
   - Modal should NOT appear again
   - Your choice is remembered

### Developer Verification

Open browser console (F12) and check:

```javascript
// Check if CookieConsent is loaded
console.log(typeof CookieConsent);
// Should output: "object"

// Check consent status
console.log(CookieConsent.validConsent());
// Should output: true (after accepting) or false (before accepting)

// Get user preferences
console.log(CookieConsent.getUserPreferences());
// Shows accepted categories
```

### Technical Verification

1. **Check Assets Loading**
   - View page source (Ctrl+U or Cmd+U)
   - Search for "cookieconsent.css" - should be in `<head>`
   - Search for "cookieconsent.umd.js" - should be before `</body>`
   - Search for "wp-cookieconsent-config" - should be present

2. **Check Network Tab**
   - Open DevTools → Network tab
   - Refresh page
   - Look for:
     - `cookieconsent.css` (Status: 200)
     - `cookieconsent.umd.js` (Status: 200)

3. **Check Console for Errors**
   - Open DevTools → Console tab
   - Should not see any errors related to cookieconsent
   - If errors exist, see Troubleshooting section

## Troubleshooting

### Issue: Modal Not Appearing

**Possible Causes:**

1. **JavaScript Conflict**
   - **Solution**: Disable other plugins one by one to find conflict
   - Check browser console for JavaScript errors

2. **Caching Issue**
   - **Solution**: Clear all caches:
     - Browser cache (Ctrl+Shift+Delete)
     - WordPress cache (if using caching plugin)
     - CDN cache (if using CDN)

3. **Theme Conflict**
   - **Solution**: Temporarily switch to a default WordPress theme (Twenty Twenty-Four)
   - If modal appears, contact theme developer

4. **Assets Not Loading**
   - **Solution**: Check file permissions
   - Verify files exist in `/wp-content/plugins/wp-cookieconsent/assets/`
   - Check for 404 errors in Network tab

### Issue: Settings Not Saving

**Possible Causes:**

1. **Permissions Issue**
   - **Solution**: Check WordPress database permissions
   - Verify user has `manage_options` capability

2. **Memory Limit**
   - **Solution**: Increase PHP memory limit in `wp-config.php`:
     ```php
     define('WP_MEMORY_LIMIT', '256M');
     ```

3. **Nonce Verification Failed**
   - **Solution**: Clear cookies and cache
   - Log out and log back in

### Issue: Modal Appears Twice

**Possible Causes:**

1. **Plugin Activated Twice**
   - **Solution**: Check if activated as both regular AND MU plugin
   - Deactivate regular plugin if using MU version

2. **Theme Includes Plugin**
   - **Solution**: Check theme files for cookieconsent code
   - Remove duplicate implementations

### Issue: Styles Look Broken

**Possible Causes:**

1. **CSS Not Loading**
   - **Solution**: Check if `cookieconsent.css` loads (Network tab)
   - Clear CSS cache

2. **Theme CSS Conflicts**
   - **Solution**: Increase CSS specificity or use `!important`
   - Add custom CSS to override

3. **CSS Minification Issue**
   - **Solution**: Exclude cookieconsent.css from minification
   - Regenerate minified files

### Issue: PHP Errors

**Common Errors:**

1. **"Parse error: syntax error"**
   - **Cause**: PHP version too old
   - **Solution**: Upgrade to PHP 8.1 or higher

2. **"Call to undefined function"**
   - **Cause**: WordPress version too old
   - **Solution**: Upgrade to WordPress 6.4 or higher

3. **"Headers already sent"**
   - **Cause**: Output before headers
   - **Solution**: Check for whitespace before `<?php` in plugin files

### Getting Help

If you still have issues:

1. **Enable WordPress Debug Mode**
   ```php
   // Add to wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

2. **Check Debug Log**
   - Location: `/wp-content/debug.log`
   - Look for cookie-consent related errors

3. **System Information**
   - Go to WordPress Admin → Tools → Site Health
   - Copy system information
   - Include when asking for help

4. **Contact Support**
   - Include WordPress version, PHP version, and active plugins
   - Describe the issue in detail
   - Include any error messages
   - Provide debug.log contents (if relevant)

## Post-Installation Steps

### 1. Configure Privacy Policy

1. Go to **Settings → Privacy**
2. Create or select a privacy policy page
3. Add information about cookies you use
4. Save changes

### 2. Set Up Cookie Categories

For each cookie/script you use on your site:

1. Identify its category (necessary, analytics, marketing)
2. Add `data-category` attribute to script tags
3. Test consent flow

Example:
```html
<script type="text/plain" data-category="analytics" src="analytics.js"></script>
```

### 3. Test Across Devices

- Test on desktop browsers (Chrome, Firefox, Safari, Edge)
- Test on mobile devices (iOS Safari, Chrome Mobile)
- Test on tablets
- Verify responsive behavior

### 4. Monitor Consent Rates

- Check user preferences over time
- Adjust modal text if acceptance rate is too low
- Ensure GDPR compliance

## Uninstallation

To completely remove the plugin:

### Standard Plugin

1. **Deactivate**
   - Go to **Plugins → Installed Plugins**
   - Click "Deactivate" under Cookie Consent Manager

2. **Delete**
   - Click "Delete" under the plugin
   - Confirm deletion
   - All settings will be removed from database

### MU Plugin

1. **Remove via FTP**
   - Delete `/wp-content/mu-plugins/wp-cookieconsent-mu-loader.php`
   - Delete `/wp-content/mu-plugins/wp-cookieconsent/` folder

2. **Clean Database** (Optional)
   ```sql
   DELETE FROM wp_options WHERE option_name = 'wp_cookieconsent_options';
   ```

## Additional Resources

- [Official Documentation](https://cookieconsent.orestbida.com)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [GDPR Compliance Guide](https://gdpr.eu/)
- [Cookie Policy Generator](https://www.cookiepolicygenerator.com/)

## Support

For additional help:
- Check README.md for usage examples
- Review official cookieconsent documentation
- Contact your hosting provider for server-related issues
- Consult WordPress support forums

