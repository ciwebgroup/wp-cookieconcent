<?php
/**
 * Plugin Name: Cookie Consent Manager
 * Plugin URI: https://github.com/orestbida/cookieconsent
 * Description: Ultra lightweight GDPR-compliant cookie consent plugin using vanilla-cookieconsent. Works immediately with no configuration required.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-cookieconsent
 * Domain Path: /languages
 *
 * @package WP_CookieConsent
 */

declare(strict_types=1);

namespace WP_CookieConsent;

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('WP_COOKIECONSENT_VERSION', '1.0.0');
define('WP_COOKIECONSENT_PLUGIN_FILE', __FILE__);
define('WP_COOKIECONSENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_COOKIECONSENT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_COOKIECONSENT_COOKIECONSENT_VERSION', '3.1.0');

/**
 * Main plugin class.
 */
class CookieConsent {
    /**
     * Singleton instance.
     *
     * @var CookieConsent|null
     */
    private static ?CookieConsent $instance = null;

    /**
     * Get singleton instance.
     *
     * @return CookieConsent
     */
    public static function get_instance(): CookieConsent {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks.
     *
     * @return void
     */
    private function init_hooks(): void {
        // Enqueue frontend assets.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);

        // Add inline script with configuration.
        add_action('wp_footer', [$this, 'add_inline_script'], 100);

        // Admin hooks.
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_action('admin_init', [$this, 'register_settings']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        }

        // Plugin action links.
        add_filter('plugin_action_links_' . plugin_basename(WP_COOKIECONSENT_PLUGIN_FILE), [$this, 'add_action_links']);
    }

    /**
     * Enqueue frontend assets.
     *
     * @return void
     */
    public function enqueue_frontend_assets(): void {
        // Enqueue cookieconsent CSS.
        wp_enqueue_style(
            'cookieconsent',
            WP_COOKIECONSENT_PLUGIN_URL . 'assets/cookieconsent.css',
            [],
            WP_COOKIECONSENT_COOKIECONSENT_VERSION
        );

        // Enqueue cookieconsent JavaScript.
        wp_enqueue_script(
            'cookieconsent',
            WP_COOKIECONSENT_PLUGIN_URL . 'assets/cookieconsent.umd.js',
            [],
            WP_COOKIECONSENT_COOKIECONSENT_VERSION,
            ['in_footer' => true, 'strategy' => 'defer']
        );
    }

    /**
     * Add inline script with configuration.
     *
     * @return void
     */
    public function add_inline_script(): void {
        $config = $this->get_config();
        $config_json = wp_json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        ?>
        <script id="wp-cookieconsent-config">
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof CookieConsent !== 'undefined' && CookieConsent.run) {
                    CookieConsent.run(<?php echo $config_json; ?>);
                }
            });
        </script>
        <?php
    }

    /**
     * Get plugin configuration.
     *
     * @return array<string, mixed>
     */
    private function get_config(): array {
        $options = get_option('wp_cookieconsent_options', []);

        // Get privacy policy URL.
        $privacy_url = get_privacy_policy_url();

        // Default configuration.
        $default_config = [
            'mode' => 'opt-in',
            'autoShow' => true,
            'revision' => 0,
            'manageScriptTags' => true,
            'autoClearCookies' => true,
            'hideFromBots' => true,
            'disablePageInteraction' => false,
            'guiOptions' => [
                'consentModal' => [
                    'layout' => 'box',
                    'position' => 'bottom right',
                    'flipButtons' => false,
                    'equalWeightButtons' => true,
                ],
                'preferencesModal' => [
                    'layout' => 'box',
                    'position' => 'right',
                    'flipButtons' => false,
                    'equalWeightButtons' => true,
                ],
            ],
            'categories' => [
                'necessary' => [
                    'enabled' => true,
                    'readOnly' => true,
                ],
                'analytics' => [
                    'enabled' => false,
                    'readOnly' => false,
                ],
                'marketing' => [
                    'enabled' => false,
                    'readOnly' => false,
                ],
            ],
            'language' => [
                'default' => 'en',
                'translations' => [
                    'en' => [
                        'consentModal' => [
                            'title' => 'We use cookies',
                            'description' => 'This website uses cookies to ensure you get the best experience on our website.',
                            'acceptAllBtn' => 'Accept all',
                            'acceptNecessaryBtn' => 'Reject all',
                            'showPreferencesBtn' => 'Manage preferences',
                            'footer' => $privacy_url ? '<a href="' . esc_url($privacy_url) . '">Privacy Policy</a>' : '',
                        ],
                        'preferencesModal' => [
                            'title' => 'Cookie preferences',
                            'acceptAllBtn' => 'Accept all',
                            'acceptNecessaryBtn' => 'Reject all',
                            'savePreferencesBtn' => 'Save preferences',
                            'closeIconLabel' => 'Close',
                            'serviceCounterLabel' => 'Service|Services',
                            'sections' => [
                                [
                                    'title' => 'Cookie Usage',
                                    'description' => 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.',
                                ],
                                [
                                    'title' => 'Strictly Necessary Cookies',
                                    'description' => 'These cookies are essential for the proper functioning of the website and cannot be disabled.',
                                    'linkedCategory' => 'necessary',
                                ],
                                [
                                    'title' => 'Analytics Cookies',
                                    'description' => 'These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.',
                                    'linkedCategory' => 'analytics',
                                ],
                                [
                                    'title' => 'Marketing Cookies',
                                    'description' => 'These cookies are used to track visitors across websites to display relevant advertisements.',
                                    'linkedCategory' => 'marketing',
                                ],
                                [
                                    'title' => 'More information',
                                    'description' => $privacy_url ? 'For any queries in relation to our policy on cookies and your choices, please <a href="' . esc_url($privacy_url) . '">contact us</a>.' : 'For any queries in relation to our policy on cookies and your choices, please contact us.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Merge with saved options.
        return $this->array_merge_recursive_distinct($default_config, $options);
    }

    /**
     * Recursively merge arrays without overwriting numeric keys.
     *
     * @param array<mixed> $array1 First array.
     * @param array<mixed> $array2 Second array.
     * @return array<mixed>
     */
    private function array_merge_recursive_distinct(array $array1, array $array2): array {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Add admin menu.
     *
     * @return void
     */
    public function add_admin_menu(): void {
        add_options_page(
            __('Cookie Consent Settings', 'wp-cookieconsent'),
            __('Cookie Consent', 'wp-cookieconsent'),
            'manage_options',
            'wp-cookieconsent',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register plugin settings.
     *
     * @return void
     */
    public function register_settings(): void {
        register_setting(
            'wp_cookieconsent_settings',
            'wp_cookieconsent_options',
            [
                'type' => 'array',
                'sanitize_callback' => [$this, 'sanitize_options'],
                'default' => [],
            ]
        );

        // General Settings Section.
        add_settings_section(
            'wp_cookieconsent_general',
            __('General Settings', 'wp-cookieconsent'),
            [$this, 'render_general_section'],
            'wp-cookieconsent'
        );

        // GUI Settings Section.
        add_settings_section(
            'wp_cookieconsent_gui',
            __('Appearance Settings', 'wp-cookieconsent'),
            [$this, 'render_gui_section'],
            'wp-cookieconsent'
        );

        // Text Content Section.
        add_settings_section(
            'wp_cookieconsent_content',
            __('Text Content', 'wp-cookieconsent'),
            [$this, 'render_content_section'],
            'wp-cookieconsent'
        );

        // Categories Section.
        add_settings_section(
            'wp_cookieconsent_categories',
            __('Cookie Categories', 'wp-cookieconsent'),
            [$this, 'render_categories_section'],
            'wp-cookieconsent'
        );

        // Register fields for General Settings.
        $this->register_general_fields();

        // Register fields for GUI Settings.
        $this->register_gui_fields();

        // Register fields for Content Settings.
        $this->register_content_fields();

        // Register fields for Categories.
        $this->register_category_fields();
    }

    /**
     * Register general settings fields.
     *
     * @return void
     */
    private function register_general_fields(): void {
        add_settings_field(
            'mode',
            __('Consent Mode', 'wp-cookieconsent'),
            [$this, 'render_select_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_general',
            [
                'label_for' => 'mode',
                'field_name' => 'mode',
                'options' => [
                    'opt-in' => __('Opt-in (default, GDPR compliant)', 'wp-cookieconsent'),
                    'opt-out' => __('Opt-out', 'wp-cookieconsent'),
                ],
                'description' => __('Choose between opt-in and opt-out mode.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'auto_show',
            __('Auto Show', 'wp-cookieconsent'),
            [$this, 'render_checkbox_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_general',
            [
                'label_for' => 'auto_show',
                'field_name' => 'autoShow',
                'description' => __('Automatically show the consent modal if consent is not valid.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'disable_page_interaction',
            __('Disable Page Interaction', 'wp-cookieconsent'),
            [$this, 'render_checkbox_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_general',
            [
                'label_for' => 'disable_page_interaction',
                'field_name' => 'disablePageInteraction',
                'description' => __('Create dark overlay and disable page scroll until consent.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'hide_from_bots',
            __('Hide from Bots', 'wp-cookieconsent'),
            [$this, 'render_checkbox_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_general',
            [
                'label_for' => 'hide_from_bots',
                'field_name' => 'hideFromBots',
                'description' => __('Stop the plugin execution if a bot/crawler is detected.', 'wp-cookieconsent'),
            ]
        );
    }

    /**
     * Register GUI settings fields.
     *
     * @return void
     */
    private function register_gui_fields(): void {
        add_settings_field(
            'consent_modal_layout',
            __('Consent Modal Layout', 'wp-cookieconsent'),
            [$this, 'render_select_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_gui',
            [
                'label_for' => 'consent_modal_layout',
                'field_name' => 'guiOptions[consentModal][layout]',
                'options' => [
                    'box' => __('Box', 'wp-cookieconsent'),
                    'box wide' => __('Box Wide', 'wp-cookieconsent'),
                    'box inline' => __('Box Inline', 'wp-cookieconsent'),
                    'cloud' => __('Cloud', 'wp-cookieconsent'),
                    'cloud inline' => __('Cloud Inline', 'wp-cookieconsent'),
                    'bar' => __('Bar', 'wp-cookieconsent'),
                    'bar inline' => __('Bar Inline', 'wp-cookieconsent'),
                ],
                'description' => __('Choose the layout for the consent modal.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'consent_modal_position',
            __('Consent Modal Position', 'wp-cookieconsent'),
            [$this, 'render_select_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_gui',
            [
                'label_for' => 'consent_modal_position',
                'field_name' => 'guiOptions[consentModal][position]',
                'options' => [
                    'top' => __('Top', 'wp-cookieconsent'),
                    'top left' => __('Top Left', 'wp-cookieconsent'),
                    'top center' => __('Top Center', 'wp-cookieconsent'),
                    'top right' => __('Top Right', 'wp-cookieconsent'),
                    'middle' => __('Middle', 'wp-cookieconsent'),
                    'middle left' => __('Middle Left', 'wp-cookieconsent'),
                    'middle center' => __('Middle Center', 'wp-cookieconsent'),
                    'middle right' => __('Middle Right', 'wp-cookieconsent'),
                    'bottom' => __('Bottom', 'wp-cookieconsent'),
                    'bottom left' => __('Bottom Left', 'wp-cookieconsent'),
                    'bottom center' => __('Bottom Center', 'wp-cookieconsent'),
                    'bottom right' => __('Bottom Right', 'wp-cookieconsent'),
                ],
                'description' => __('Choose the position for the consent modal.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'preferences_modal_layout',
            __('Preferences Modal Layout', 'wp-cookieconsent'),
            [$this, 'render_select_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_gui',
            [
                'label_for' => 'preferences_modal_layout',
                'field_name' => 'guiOptions[preferencesModal][layout]',
                'options' => [
                    'box' => __('Box', 'wp-cookieconsent'),
                    'bar' => __('Bar', 'wp-cookieconsent'),
                    'bar wide' => __('Bar Wide', 'wp-cookieconsent'),
                ],
                'description' => __('Choose the layout for the preferences modal.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'preferences_modal_position',
            __('Preferences Modal Position', 'wp-cookieconsent'),
            [$this, 'render_select_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_gui',
            [
                'label_for' => 'preferences_modal_position',
                'field_name' => 'guiOptions[preferencesModal][position]',
                'options' => [
                    'left' => __('Left', 'wp-cookieconsent'),
                    'right' => __('Right', 'wp-cookieconsent'),
                ],
                'description' => __('Choose the position for the preferences modal.', 'wp-cookieconsent'),
            ]
        );
    }

    /**
     * Register content settings fields.
     *
     * @return void
     */
    private function register_content_fields(): void {
        add_settings_field(
            'consent_modal_title',
            __('Consent Modal Title', 'wp-cookieconsent'),
            [$this, 'render_text_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'consent_modal_title',
                'field_name' => 'language[translations][en][consentModal][title]',
                'description' => __('Title displayed in the consent modal.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'consent_modal_description',
            __('Consent Modal Description', 'wp-cookieconsent'),
            [$this, 'render_textarea_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'consent_modal_description',
                'field_name' => 'language[translations][en][consentModal][description]',
                'description' => __('Description displayed in the consent modal.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'accept_all_btn',
            __('"Accept All" Button Text', 'wp-cookieconsent'),
            [$this, 'render_text_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'accept_all_btn',
                'field_name' => 'language[translations][en][consentModal][acceptAllBtn]',
                'description' => __('Text for the "Accept All" button.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'reject_all_btn',
            __('"Reject All" Button Text', 'wp-cookieconsent'),
            [$this, 'render_text_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'reject_all_btn',
                'field_name' => 'language[translations][en][consentModal][acceptNecessaryBtn]',
                'description' => __('Text for the "Reject All" button.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'manage_preferences_btn',
            __('"Manage Preferences" Button Text', 'wp-cookieconsent'),
            [$this, 'render_text_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'manage_preferences_btn',
                'field_name' => 'language[translations][en][consentModal][showPreferencesBtn]',
                'description' => __('Text for the "Manage Preferences" button.', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'preferences_modal_title',
            __('Preferences Modal Title', 'wp-cookieconsent'),
            [$this, 'render_text_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_content',
            [
                'label_for' => 'preferences_modal_title',
                'field_name' => 'language[translations][en][preferencesModal][title]',
                'description' => __('Title displayed in the preferences modal.', 'wp-cookieconsent'),
            ]
        );
    }

    /**
     * Register category fields.
     *
     * @return void
     */
    private function register_category_fields(): void {
        add_settings_field(
            'analytics_enabled',
            __('Enable Analytics by Default', 'wp-cookieconsent'),
            [$this, 'render_checkbox_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_categories',
            [
                'label_for' => 'analytics_enabled',
                'field_name' => 'categories[analytics][enabled]',
                'description' => __('Enable analytics cookies by default (only in opt-out mode).', 'wp-cookieconsent'),
            ]
        );

        add_settings_field(
            'marketing_enabled',
            __('Enable Marketing by Default', 'wp-cookieconsent'),
            [$this, 'render_checkbox_field'],
            'wp-cookieconsent',
            'wp_cookieconsent_categories',
            [
                'label_for' => 'marketing_enabled',
                'field_name' => 'categories[marketing][enabled]',
                'description' => __('Enable marketing cookies by default (only in opt-out mode).', 'wp-cookieconsent'),
            ]
        );
    }

    /**
     * Render general section description.
     *
     * @return void
     */
    public function render_general_section(): void {
        echo '<p>' . esc_html__('Configure general cookie consent behavior.', 'wp-cookieconsent') . '</p>';
    }

    /**
     * Render GUI section description.
     *
     * @return void
     */
    public function render_gui_section(): void {
        echo '<p>' . esc_html__('Customize the appearance of the cookie consent modals.', 'wp-cookieconsent') . '</p>';
    }

    /**
     * Render content section description.
     *
     * @return void
     */
    public function render_content_section(): void {
        echo '<p>' . esc_html__('Customize the text content displayed in the modals.', 'wp-cookieconsent') . '</p>';
    }

    /**
     * Render categories section description.
     *
     * @return void
     */
    public function render_categories_section(): void {
        echo '<p>' . esc_html__('Configure cookie categories. Necessary cookies are always enabled and cannot be disabled.', 'wp-cookieconsent') . '</p>';
    }

    /**
     * Render select field.
     *
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_select_field(array $args): void {
        $options = get_option('wp_cookieconsent_options', []);
        $field_name = $args['field_name'];
        $value = $this->get_nested_value($options, $field_name);
        $id = $args['label_for'];

        echo '<select id="' . esc_attr($id) . '" name="wp_cookieconsent_options[' . esc_attr($field_name) . ']">';

        foreach ($args['options'] as $option_value => $option_label) {
            $selected = selected($value, $option_value, false);
            echo '<option value="' . esc_attr($option_value) . '"' . $selected . '>' . esc_html($option_label) . '</option>';
        }

        echo '</select>';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render text field.
     *
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_text_field(array $args): void {
        $options = get_option('wp_cookieconsent_options', []);
        $field_name = $args['field_name'];
        $value = $this->get_nested_value($options, $field_name);
        $id = $args['label_for'];

        echo '<input type="text" id="' . esc_attr($id) . '" name="wp_cookieconsent_options[' . esc_attr($field_name) . ']" value="' . esc_attr($value) . '" class="regular-text">';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render textarea field.
     *
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_textarea_field(array $args): void {
        $options = get_option('wp_cookieconsent_options', []);
        $field_name = $args['field_name'];
        $value = $this->get_nested_value($options, $field_name);
        $id = $args['label_for'];

        echo '<textarea id="' . esc_attr($id) . '" name="wp_cookieconsent_options[' . esc_attr($field_name) . ']" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render checkbox field.
     *
     * @param array<string, mixed> $args Field arguments.
     * @return void
     */
    public function render_checkbox_field(array $args): void {
        $options = get_option('wp_cookieconsent_options', []);
        $field_name = $args['field_name'];
        $value = $this->get_nested_value($options, $field_name);
        $id = $args['label_for'];
        $checked = checked($value, true, false);

        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" id="' . esc_attr($id) . '" name="wp_cookieconsent_options[' . esc_attr($field_name) . ']" value="1"' . $checked . '>';
        echo ' ' . esc_html($args['description']);
        echo '</label>';
    }

    /**
     * Get nested value from array using dot notation.
     *
     * @param array<mixed> $array The array to search.
     * @param string $path The path using bracket notation.
     * @return mixed
     */
    private function get_nested_value(array $array, string $path) {
        // Convert bracket notation to array keys.
        preg_match_all('/\[([^\]]+)\]/', $path, $matches);

        if (empty($matches[1])) {
            // No brackets, just a simple key.
            return $array[$path] ?? '';
        }

        $keys = $matches[1];

        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return '';
            }
            $array = $array[$key];
        }

        return $array;
    }

    /**
     * Set nested value in array using bracket notation.
     *
     * @param array<mixed> $array The array to modify.
     * @param string $path The path using bracket notation.
     * @param mixed $value The value to set.
     * @return void
     */
    private function set_nested_value(array &$array, string $path, $value): void {
        preg_match_all('/\[([^\]]+)\]/', $path, $matches);

        if (empty($matches[1])) {
            $array[$path] = $value;
            return;
        }

        $keys = $matches[1];
        $current = &$array;

        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }

        $current = $value;
    }

    /**
     * Sanitize options.
     *
     * @param array<mixed> $input Input options.
     * @return array<mixed>
     */
    public function sanitize_options(array $input): array {
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_options($value);
            } elseif (is_bool($value) || $value === '1' || $value === '0') {
                $sanitized[$key] = (bool) $value;
            } elseif (is_numeric($value)) {
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }

    /**
     * Render settings page.
     *
     * @return void
     */
    public function render_settings_page(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if settings were saved.
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'wp_cookieconsent_messages',
                'wp_cookieconsent_message',
                __('Settings saved successfully.', 'wp-cookieconsent'),
                'success'
            );
        }

        settings_errors('wp_cookieconsent_messages');

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="wp-cookieconsent-settings-container">
                <form action="options.php" method="post">
                    <?php
                    settings_fields('wp_cookieconsent_settings');
                    do_settings_sections('wp-cookieconsent');
                    submit_button(__('Save Settings', 'wp-cookieconsent'));
                    ?>
                </form>

                <div class="wp-cookieconsent-info">
                    <h2><?php esc_html_e('About Cookie Consent', 'wp-cookieconsent'); ?></h2>
                    <p><?php esc_html_e('This plugin uses the vanilla-cookieconsent library to provide a lightweight, GDPR-compliant cookie consent solution.', 'wp-cookieconsent'); ?></p>
                    <p>
                        <strong><?php esc_html_e('Documentation:', 'wp-cookieconsent'); ?></strong>
                        <a href="https://cookieconsent.orestbida.com" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e('Visit Official Documentation', 'wp-cookieconsent'); ?>
                        </a>
                    </p>
                    <p>
                        <strong><?php esc_html_e('Version:', 'wp-cookieconsent'); ?></strong>
                        <?php echo esc_html(WP_COOKIECONSENT_VERSION); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook_suffix The current admin page.
     * @return void
     */
    public function enqueue_admin_assets(string $hook_suffix): void {
        if ($hook_suffix !== 'settings_page_wp-cookieconsent') {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_style(
            'wp-cookieconsent-admin',
            WP_COOKIECONSENT_PLUGIN_URL . 'assets/admin.css',
            [],
            WP_COOKIECONSENT_VERSION
        );

        wp_enqueue_script(
            'wp-cookieconsent-admin',
            WP_COOKIECONSENT_PLUGIN_URL . 'assets/admin.js',
            ['jquery', 'wp-color-picker'],
            WP_COOKIECONSENT_VERSION,
            true
        );
    }

    /**
     * Add action links to plugin page.
     *
     * @param array<string> $links Existing links.
     * @return array<string>
     */
    public function add_action_links(array $links): array {
        $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=wp-cookieconsent')) . '">' . __('Settings', 'wp-cookieconsent') . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }
}

// Initialize the plugin.
CookieConsent::get_instance();

