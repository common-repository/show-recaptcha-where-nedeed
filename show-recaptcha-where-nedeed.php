<?php
/**
 * @package Show_Recaptcha_Where_Nedeed
 * @version 1.2.3
 */
/*
Plugin Name: Show Recaptcha Where Nedeed
Plugin URI: https://www.getwebsolution.it/mostra-recaptcha-dove-necessario/
Description: Fixes Contact Form 7's Recaptcha v3 so it only appears on pages with contact forms.
Author: Ermald Billa
Version: 1.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author URI: https://getwebsolution.it
Requires Plugins: contact-form-7
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function srrw_recaptcha_cf7_decide()
{
    if(get_option('srrw_recaptcha_cf7_enabled') !== '1') {
        return;
    }

    $post = get_post();
    if($post instanceof WP_Post && strpos($post->post_content, '[contact-form-7') === false){
        remove_action('wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 10);
        remove_action('wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20);
    }
}
add_action('wp_enqueue_scripts', 'srrw_recaptcha_cf7_decide', 9, 0);

function srrw_recaptcha_cf7_sanitize_checkbox($input) {
    return $input === '1' ? '1' : '0';
}

function srrw_recaptcha_cf7_sanitize_url($input) {
    return esc_url_raw($input);
}

function srrw_recaptcha_cf7_settings_init() {
    register_setting('srrw_recaptcha_cf7_settings', 'srrw_recaptcha_cf7_enabled', 'srrw_recaptcha_cf7_sanitize_checkbox');
    register_setting('srrw_recaptcha_cf7_settings', 'srrw_recaptcha_cf7_donate_link', 'srrw_recaptcha_cf7_sanitize_url');

    add_settings_section(
        'srrw_recaptcha_cf7_section',
        esc_html__('Show Recaptcha Settings', 'srrw_recaptcha_cf7'),
        'srrw_recaptcha_cf7_section_callback',
        'srrw_recaptcha_cf7_settings'
    );

    add_settings_field(
        'srrw_recaptcha_cf7_enabled',
        esc_html__('Enable Recaptcha', 'srrw_recaptcha_cf7'),
        'srrw_recaptcha_cf7_enabled_render',
        'srrw_recaptcha_cf7_settings',
        'srrw_recaptcha_cf7_section'
    );
    add_settings_field(
        'srrw_recaptcha_cf7_donate_link',
        esc_html__('Donate Link', 'srrw_recaptcha_cf7'),
        'srrw_recaptcha_cf7_donate_link_render',
        'srrw_recaptcha_cf7_settings',
        'srrw_recaptcha_cf7_section'
    );
}

function srrw_recaptcha_cf7_section_callback() {
    echo esc_html__('Enable or disable the Recaptcha functionality for Contact Form 7.', 'srrw_recaptcha_cf7');
}

function srrw_recaptcha_cf7_enabled_render() {
    $enabled = get_option('srrw_recaptcha_cf7_enabled');
    ?>
    <input type='checkbox' name='srrw_recaptcha_cf7_enabled' value='1' <?php checked(1, $enabled, true); ?> />
    <?php
}

function srrw_recaptcha_cf7_donate_link_render() {
    $default_donate_link = 'https://paypal.me/akaicanbe';
    $donate_link = get_option('srrw_recaptcha_cf7_donate_link', $default_donate_link);
    ?>
    <a href='<?php echo esc_url($donate_link); ?>' target='_blank'><?php esc_html_e('Donate', 'srrw_recaptcha_cf7'); ?></a>
    <p class="description"><?php esc_html_e('Click the link to donate.', 'srrw_recaptcha_cf7'); ?></p>
    <?php
}

function srrw_recaptcha_cf7_options_page() {
    ?>
    <div class="wrap">
        <div style="margin-top: 20px;">
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__)) . 'assets/logo.png'; ?>" alt="Show Recaptcha CF7" style="max-width: 200px;">
        </div>
        <!--<h1>Show Recaptcha Settings</h1>-->
        <form action='options.php' method='post'>
            <?php
            settings_fields('srrw_recaptcha_cf7_settings');
            do_settings_sections('srrw_recaptcha_cf7_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function srrw_recaptcha_cf7_add_admin_menu() {
    add_options_page(
        'Show Recaptcha',
        'Show Recaptcha',
        'manage_options',
        'srrw_recaptcha_cf7',
        'srrw_recaptcha_cf7_options_page'
    );
}
add_action('admin_menu', 'srrw_recaptcha_cf7_add_admin_menu');
add_action('admin_init', 'srrw_recaptcha_cf7_settings_init');
?>
