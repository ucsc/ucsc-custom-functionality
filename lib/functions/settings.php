<?php

/**
 * Add Plugin settings and info page
 *
 * This file contains functions to add a settings/info page below WordPress Settings menu
 *
 * @package      ucsc
 * @since        1.7.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/** Register new menu and page */

if (! function_exists('ucsc_add_settings_page')) {
    function ucsc_add_settings_page()
    {
        add_options_page('UCSC Custom Functionality plugin page', 'UCSC Custom Functionality', 'manage_options', 'ucsc-custom-functionality-settings', 'ucsc_render_plugin_settings_page');
    }
}
add_action('admin_menu', 'ucsc_add_settings_page');


/** Register plugin settings via the Settings API */

if (! function_exists('ucsc_register_plugin_settings')) {
    function ucsc_register_plugin_settings()
    {
        register_setting(
            'ucsc_custom_functionality',
            'ucsc_enable_xmlrpc',
            array(
                'type'              => 'boolean',
                'description'       => 'Whether XML-RPC is enabled on this site.',
                'sanitize_callback' => static function ($value) {
                    return ! empty($value);
                },
                'default'           => false,
                'show_in_rest'      => false,
            )
        );

        add_settings_section(
            'ucsc_security_section',
            'Security',
            static function () {
                echo '<p>Security features provided by this plugin.</p>';
            },
            'ucsc-custom-functionality-settings'
        );

        add_settings_field(
            'ucsc_enable_xmlrpc',
            'Enable XML-RPC',
            'ucsc_render_enable_xmlrpc_field',
            'ucsc-custom-functionality-settings',
            'ucsc_security_section',
            array('label_for' => 'ucsc_enable_xmlrpc')
        );
    }
}
add_action('admin_init', 'ucsc_register_plugin_settings');


if (! function_exists('ucsc_render_enable_xmlrpc_field')) {
    function ucsc_render_enable_xmlrpc_field()
    {
        $enabled = (bool) get_option('ucsc_enable_xmlrpc', false);
?>
        <label for="ucsc_enable_xmlrpc">
            <input
                type="checkbox"
                id="ucsc_enable_xmlrpc"
                name="ucsc_enable_xmlrpc"
                value="1"
                <?php checked($enabled); ?> />
            Allow requests to <code>/xmlrpc.php</code> on this site.
        </label>
        <p class="description">
            XML-RPC is disabled by default because <code>/xmlrpc.php</code> can be used as an attack vector.
            Only enable this if a specific integration (for example, the Jetpack mobile app or a remote publishing tool) requires it.
            See <a href="https://docs.pantheon.io/guides/wordpress-developer/xml-rpc-attacks" rel="noopener noreferrer">Pantheon&rsquo;s guidance on XML-RPC attacks</a>.
        </p>
    <?php
    }
}


/** 
 * HTML output of Settings page 
 *
 * note: This page typically displays a form for displaying any settings options. 
 * It is not needed at this point.
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/
 *
 */

if (! function_exists('ucsc_render_plugin_settings_page')) {
    function ucsc_render_plugin_settings_page()
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/ucsc-custom-functionality/plugin.php');
    ?>
        <div class="wrap cf-admin-settings-page">
            <h1><?php echo esc_html($plugin_data['Name']); ?></h1>
            <h2>Version: <?php echo esc_html($plugin_data['Version']); ?> <a href="https://github.com/ucsc/ucsc-custom-functionality/releases">(release notes)</a></h2>
            <p><?php echo esc_html($plugin_data['Description']); ?></p>
            <hr>
            <h3>Features added by this plugin:</h3>
            <ul>
                <li><strong>Google Analytics 4</strong> and <strong>Site Improve</strong> scripts to site footer</li>
                <li><strong>Shortcodes:</strong>
                    <ul>
                        <li><code>[site-search]</code>: Inserts the HTML script tag to display <strong>Google Site Search</strong> results on a page</li>
                        <li><code>[copyright]</code>: Displays copyright symbol and year (<?php echo do_shortcode('[copyright]') ?>)</li>
                        <li><code>[last-modified]</code>: Displays the <i>last modified</i> date of a page</li>
                    </ul>
                </li>
            </ul>
            <hr>
            <form action="options.php" method="post">
                <?php
                settings_fields('ucsc_custom_functionality');
                do_settings_sections('ucsc-custom-functionality-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
