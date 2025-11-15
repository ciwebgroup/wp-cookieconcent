<?php
/**
 * Uninstall script for Cookie Consent Manager
 *
 * Fired when the plugin is uninstalled.
 *
 * @package WP_CookieConsent
 */

declare(strict_types=1);

// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove plugin options from the database.
 *
 * @return void
 */
function wp_cookieconsent_uninstall(): void {
    // Delete plugin options.
    delete_option('wp_cookieconsent_options');

    // For multisite installations, delete options for all sites.
    if (is_multisite()) {
        global $wpdb;

        $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

        foreach ($blog_ids as $blog_id) {
            switch_to_blog((int) $blog_id);
            delete_option('wp_cookieconsent_options');
            restore_current_blog();
        }
    }

    // Clear any cached data.
    wp_cache_flush();
}

// Execute uninstall.
wp_cookieconsent_uninstall();

