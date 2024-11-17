<?php
/*
Plugin Name: Simple Top Banner
Description: Adds a customizable banner with styled link to the top of your site. Es viernes y el cuerpo lo sabe.
Version: 1.1.0
Author: Bill Smith
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

define('TOP_BANNER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TOP_BANNER_PLUGIN_URL', plugin_dir_url(__FILE__));

function top_banner_enqueue_assets($hook) {
    // Load Font Awesome on all pages (admin and frontend)
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );

    // Load Google Fonts
    wp_enqueue_style(
        'top-banner-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@400;500;600&display=swap',
        array(),
        '1.0.0'
    );

    // Load admin-specific assets
    if ('toplevel_page_top-banner-settings' === $hook) {
        wp_enqueue_style(
            'top-banner-admin',
            TOP_BANNER_PLUGIN_URL . 'assets/css/admin.css',
            array('font-awesome'), // Make admin CSS depend on Font Awesome
            '1.0.0'
        );
        
        wp_enqueue_script(
            'top-banner-admin',
            TOP_BANNER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}

// Replace your existing action hooks with these
add_action('admin_enqueue_scripts', 'top_banner_enqueue_assets');
add_action('wp_enqueue_scripts', 'top_banner_enqueue_assets');

// Add menu item to WordPress admin
function top_banner_menu()
{
    add_menu_page(
        'Top Banner Settings',
        'Top Banner',
        'manage_options',
        'top-banner-settings',
        'top_banner_settings_page',
        'dashicons-admin-customizer'
    );
}
add_action('admin_menu', 'top_banner_menu');

// Add settings page assets
function top_banner_admin_scripts($hook)
{
    if ('toplevel_page_top-banner-settings' !== $hook) {
        return;
    }
    ?>
    <style>
        .font-preview {
            font-size: 16px;
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
    <?php
}
add_action('admin_head', 'top_banner_admin_scripts');

// Create the settings page
function top_banner_settings_page()
{
    // Save settings
    if (isset($_POST['top_banner_link_text'])) {
        update_option('top_banner_color', sanitize_hex_color($_POST['top_banner_color']));
        update_option('top_banner_height', absint($_POST['top_banner_height']));
        update_option('top_banner_enabled', isset($_POST['top_banner_enabled']) ? '1' : '0');
        update_option('top_banner_link_text', sanitize_text_field($_POST['top_banner_link_text']));
        update_option('top_banner_link_url', esc_url_raw($_POST['top_banner_link_url']));
        update_option('top_banner_link_color', sanitize_hex_color($_POST['top_banner_link_color']));
        update_option('top_banner_font_size', absint($_POST['top_banner_font_size']));
        update_option('top_banner_font_family', sanitize_text_field($_POST['top_banner_font_family']));
        update_option('top_banner_custom_css', sanitize_textarea_field($_POST['top_banner_custom_css']));
        update_option('top_banner_icon', sanitize_text_field($_POST['top_banner_icon']));
        update_option('top_banner_icon_position', sanitize_text_field($_POST['top_banner_icon_position']));
    }

    // Get current settings
    $banner_color = get_option('top_banner_color', '#e5e5e5');
    $banner_height = get_option('top_banner_height', 35);
    $banner_enabled = get_option('top_banner_enabled', '1');
    $banner_link_text = get_option('top_banner_link_text', 'Learn More');
    $banner_link_url = get_option('top_banner_link_url', '#');
    $banner_link_color = get_option('top_banner_link_color', '#FFFFFF');
    $banner_font_size = get_option('top_banner_font_size', 16);
    $banner_font_family = get_option('top_banner_font_family', 'Arial');

    // Enqueue Google Fonts
    function top_banner_enqueue_fonts()
    {
        wp_enqueue_style(
            'top-banner-fonts',
            'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Quicksand:wght@400;500;600&display=swap',
            array(),
            '1.0.0'
        );
    }
    add_action('wp_enqueue_scripts', 'top_banner_enqueue_fonts');
    add_action('admin_enqueue_scripts', 'top_banner_enqueue_fonts');

    // Updated font options array
    $font_options = array(
        'Montserrat' => 'Montserrat, sans-serif',
        'Quicksand' => 'Quicksand, sans-serif',
        'Arial' => 'Arial, sans-serif',
        'Helvetica' => 'Helvetica, Arial, sans-serif',
        'Georgia' => 'Georgia, serif',
        'System UI' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif'
    );
    ?>
    <div class="wrap">
        <h1>Top Banner Settings</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="top_banner_enabled">Enable Banner</label></th>
                    <td>
                        <input type="checkbox" id="top_banner_enabled" name="top_banner_enabled" <?php checked($banner_enabled, '1'); ?>>
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_height">Banner Height (px)</label></th>
                    <td>
                        <input type="number" id="top_banner_height" name="top_banner_height"
                            value="<?php echo esc_attr($banner_height); ?>" min="20" max="200" step="1">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_color">Background Color</label></th>
                    <td>
                        <input type="color" id="top_banner_color" name="top_banner_color"
                            value="<?php echo esc_attr($banner_color); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_link_text">Link Text</label></th>
                    <td>
                        <input type="text" id="top_banner_link_text" name="top_banner_link_text"
                            value="<?php echo esc_attr($banner_link_text); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_link_url">Link URL</label></th>
                    <td>
                        <input type="url" id="top_banner_link_url" name="top_banner_link_url"
                            value="<?php echo esc_url($banner_link_url); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_link_color">Link Color</label></th>
                    <td>
                        <input type="color" id="top_banner_link_color" name="top_banner_link_color"
                            value="<?php echo esc_attr($banner_link_color); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_font_size">Font Size (px)</label></th>
                    <td>
                        <input type="number" id="top_banner_font_size" name="top_banner_font_size"
                            value="<?php echo esc_attr($banner_font_size); ?>" min="10" max="32" step="1">
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_font_family">Font Family</label></th>
                    <td>
                        <select id="top_banner_font_family" name="top_banner_font_family">
                            <?php foreach ($font_options as $label => $value): ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($banner_font_family, $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="font-preview" style="font-family: <?php echo esc_attr($banner_font_family); ?>">
                            Font Preview: <?php echo esc_html($banner_link_text); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                <tr>
                    <th><label for="top_banner_icon">Link Icon</label></th>
                    <td>
                        <?php
                        // Replace your select element with this
                        $current_icon = get_option('top_banner_icon', '');
                        $icons = array(
                            '' => array('name' => 'No Icon', 'class' => ''),
                            'arrow-right' => array('name' => 'Arrow Right', 'class' => 'fa-solid fa-arrow-right'),
                            'external-link' => array('name' => 'External Link', 'class' => 'fa-solid fa-arrow-up-right-from-square'),
                            'chevron-right' => array('name' => 'Chevron Right', 'class' => 'fa-solid fa-chevron-right'),
                            'star' => array('name' => 'Star', 'class' => 'fa-solid fa-star'),
                            'user' => array('name' => 'User', 'class' => 'fa-regular fa-user'),
                            'pencil' => array('name' => 'Pencil', 'class' => 'fa-solid fa-pencil'),
                            'book-open' => array('name' => 'Book', 'class' => 'fa-solid fa-book-open'),
                            'paintbrush' => array('name' => 'Paintbrush', 'class' => 'fa-solid fa-paintbrush')
                        );
                        ?>

                        <div class="icon-select-wrapper">
                            <input type="hidden" name="top_banner_icon" id="top_banner_icon"
                                value="<?php echo esc_attr($current_icon); ?>">
                            <div class="icon-select-current">
                                <?php if (!empty($current_icon) && isset($icons[$current_icon])): ?>
                                    <i class="<?php echo esc_attr($icons[$current_icon]['class']); ?>"></i>
                                    <span><?php echo esc_html($icons[$current_icon]['name']); ?></span>
                                <?php else: ?>
                                    <span>Select an icon</span>
                                <?php endif; ?>
                            </div>
                            <div class="icon-select-options">
                                <?php foreach ($icons as $value => $icon): ?>
                                    <div class="icon-option" data-value="<?php echo esc_attr($value); ?>">
                                        <?php if (!empty($icon['class'])): ?>
                                            <i class="<?php echo esc_attr($icon['class']); ?>"></i>
                                        <?php endif; ?>
                                        <span><?php echo esc_html($icon['name']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="top_banner_icon_position">Icon Position</label></th>
                    <td>
                        <select id="top_banner_icon_position" name="top_banner_icon_position">
                            <option value="before" <?php selected(get_option('top_banner_icon_position'), 'before'); ?>>
                                Before Text</option>
                            <option value="after" <?php selected(get_option('top_banner_icon_position'), 'after'); ?>>After
                                Text</option>
                        </select>
                    </td>
                </tr>
                <th><label for="top_banner_custom_css">Custom CSS</label></th>
                <td>
                    <textarea id="top_banner_custom_css" name="top_banner_custom_css" rows="8" class="large-text code"><?php
                    echo esc_textarea(get_option('top_banner_custom_css', ''));
                    ?></textarea>
                    <p class="description">
                        Add custom CSS for advanced styling. Example:<br>
                        <code>#top-banner { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }</code><br>
                        <code>#top-banner .banner-link { border-bottom: 2px solid currentColor; }</code>
                    </p>
                </td>
                </tr>
                <tr>
                    <td>
                        <p>Fuck Micky Mouse</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

// Add banner to the site
function display_top_banner()
{
    if (get_option('top_banner_enabled', '1') !== '1') {
        return;
    }

    $banner_color = get_option('top_banner_color', '#e5e5e5');
    $banner_height = get_option('top_banner_height', 40);
    $banner_link_text = get_option('top_banner_link_text', 'Learn More');
    $banner_link_url = get_option('top_banner_link_url', '#');
    $banner_link_color = get_option('top_banner_link_color', '#000000');
    $banner_font_size = get_option('top_banner_font_size', 16);
    $banner_font_family = get_option('top_banner_font_family', 'Arial, sans-serif');

    $icon = get_option('top_banner_icon', '');
    $icon_position = get_option('top_banner_icon_position', 'after');

    // Get icon class based on selection
    $icon_class = '';
    switch ($icon) {
        case 'arrow-right':
            $icon_class = 'fa-solid fa-arrow-right';
            break;
        case 'external-link':
            $icon_class = 'fa-solid fa-arrow-up-right-from-square';
            break;
        case 'chevron-right':
            $icon_class = 'fa-solid fa-chevron-right';
            break;
        case 'star':
            $icon_class = 'fa-solid fa-star';
            break;
        case 'info-circle':
            $icon_class = 'fa-solid fa-circle-info';
            break;
        case 'user':
            $icon_class = 'fa-regular fa-user';
            break;
        case 'book-open':
            $icon_class = 'fa-solid fa-book-open';
            break;
        case 'pencil':
            $icon_class = 'fa-solid fa-pencil';
            break;
        case 'paintbrush':
            $icon_class = 'fa-solid fa-paintbrush';
            break;

    }
    ?>
    <style>
        #top-banner {
            background-color:
                <?php echo esc_attr($banner_color); ?>
            ;
            padding: 0 20px;
            height:
                <?php echo esc_attr($banner_height); ?>px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            position: sticky;
            z-index: 9999;
            box-sizing: border-box;
        }

        #top-banner .banner-link {
            color:
                <?php echo esc_attr($banner_link_color); ?>
            ;
            text-decoration: none;
            font-size:
                <?php echo esc_attr($banner_font_size); ?>px;
            font-family:
                <?php echo esc_attr($banner_font_family); ?>
            ;
            transition: opacity 0.2s ease;
        }

        #top-banner .banner-link:hover {
            opacity: 0.8;
        }

        #top-banner .banner-link i {
            margin-left:
                <?php echo $icon_position === 'after' ? '0.5em' : '0'; ?>
            ;
            margin-right:
                <?php echo $icon_position === 'before' ? '0.5em' : '0'; ?>
            ;
            font-size:
                <?php echo esc_attr($banner_font_size); ?>px;
        }

        <?php
        $custom_css = get_option('top_banner_custom_css', '');
        if (!empty($custom_css)) {
            echo wp_strip_all_tags($custom_css);
        }
        ?>
    </style>
    <div id="top-banner">
        <a href="<?php echo esc_url($banner_link_url); ?>" class="banner-link">
            <?php if ($icon && $icon_position === 'before'): ?>
                <i class="<?php echo esc_attr($icon_class); ?>"></i>
            <?php endif; ?>

            <?php echo esc_html($banner_link_text); ?>

            <?php if ($icon && $icon_position === 'after'): ?>
                <i class="<?php echo esc_attr($icon_class); ?>"></i>
            <?php endif; ?>
        </a>
    </div>
    <?php
}
add_action('wp_body_open', 'display_top_banner');