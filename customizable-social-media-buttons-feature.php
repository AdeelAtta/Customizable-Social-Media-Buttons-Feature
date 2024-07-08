<?php
/*
Plugin Name: Customizable Social Media Buttons Feature
Plugin URI: https://github.com/AdeelAtta/Simple-Social-Buttons-Feature
Description: Plugin to add Customized social media Buttons into the wordpress.
Version: 1.0
Author: Adeel Atta
Author URI: https://www.linkedin.com/in/adeel-atta/
Licence:GL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue CSS and JS
function fsb_enqueue_assets() {
    wp_enqueue_style('fsb-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('fsb-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'fsb_enqueue_assets');

// Create Settings Page
function fsb_add_admin_menu() {
    add_menu_page(
        'Floating Social Buttons Settings', // Page title
        'Social Buttons', // Menu title
        'manage_options', // Capability
        'fsb-settings', // Menu slug
        'fsb_settings_page_html' // Callback function to display the page content
    );
}
add_action('admin_menu', 'fsb_add_admin_menu');

// Register Settings
function fsb_register_settings() {
    register_setting('fsb_options_group', 'fsb_options', 'fsb_options_validate');

    add_settings_section(
        'fsb_main_section',
        'Main Settings',
        'fsb_section_text',
        'fsb-settings'
    );

    $social_media = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'pinterest' => 'Pinterest'
    ];

    foreach ($social_media as $key => $label) {
        add_settings_field(
            "fsb_{$key}_url",
            "{$label} URL",
            "fsb_{$key}_url_input",
            'fsb-settings',
            'fsb_main_section'
        );
    }

    add_settings_field(
        'fsb_selected_buttons',
        'Select Buttons to Display',
        'fsb_selected_buttons_input',
        'fsb-settings',
        'fsb_main_section'
    );

    add_settings_field(
        'fsb_display_mode',
        'Display Mode',
        'fsb_display_mode_input',
        'fsb-settings',
        'fsb_main_section'
    );

    add_settings_field(
        'fsb_display_position',
        'Display Position',
        'fsb_display_position_input',
        'fsb-settings',
        'fsb_main_section'
    );

    add_settings_field(
        'fsb_button_design',
        'Button Design for Social Buttons',
        'fsb_button_design_input',
        'fsb-settings',
        'fsb_main_section'
    );

    add_settings_field(
        'fsb_post_share_button_design',
        'Button Design for Post Share Buttons',
        'fsb_post_share_button_design_input',
        'fsb-settings',
        'fsb_main_section'
    );

    add_settings_field(
        'fsb_share_buttons',
        'Share Buttons on Posts',
        'fsb_share_buttons_input',
        'fsb-settings',
        'fsb_main_section'
    );
}
add_action('admin_init', 'fsb_register_settings');

// Display Mode Input
function fsb_display_mode_input() {
    $options = get_option('fsb_options');
    $display_mode = isset($options['display_mode']) ? $options['display_mode'] : 'horizontal';

    echo '<label><input type="radio" name="fsb_options[display_mode]" value="horizontal" ' . checked('horizontal', $display_mode, false) . '> Horizontal</label><br>';
    echo '<label><input type="radio" name="fsb_options[display_mode]" value="vertical" ' . checked('vertical', $display_mode, false) . '> Vertical</label>';
}

// Display Position Input
function fsb_display_position_input() {
    $options = get_option('fsb_options');
    $display_position = isset($options['display_position']) ? $options['display_position'] : 'bottom-right';

    echo '<label><input type="radio" name="fsb_options[display_position]" value="bottom-left" ' . checked('bottom-left', $display_position, false) . '> Bottom Left</label><br>';
    echo '<label><input type="radio" name="fsb_options[display_position]" value="bottom-right" ' . checked('bottom-right', $display_position, false) . '> Bottom Right</label>';
}

// Button Design Input
function fsb_button_design_input() {
    $options = get_option('fsb_options');
    $button_design = isset($options['button_design']) ? $options['button_design'] : 'default';

    $designs = [
        'default' => 'Default',
        'design1' => 'Design 1',
    ];

    foreach ($designs as $design => $label) {
        $checked = checked($button_design, $design, false);
        echo "<label style='display:flex;justifyContent:center;alignItems:center'><input type='radio' name='fsb_options[button_design]' value='$design' $checked> $label<br><img src='" . plugin_dir_url(__FILE__) . "images/{$design}.png' alt='{$label}' style='width: 100px; height: auto;'></label><br>";
    }
}

// Button Design Input for Post Share Buttons
function fsb_post_share_button_design_input() {
    $options = get_option('fsb_options');
    $post_share_button_design = isset($options['post_share_button_design']) ? $options['post_share_button_design'] : 'design1';

    $designs = [
        'design1' => 'Regular Share Buttons with Icon & Name/Text',
        'design2' => 'Share Button with Name/Text Only (No Icon)',
        'design3' => 'Share Button with Icon Only',
        'design4' => 'Share Button with Icon and Name/Text Appearing on Hover',
        'design5' => 'Vertical Button'
    ];

    foreach ($designs as $design => $label) {
        $checked = checked($post_share_button_design, $design, false);
        echo "<label><input type='radio' name='fsb_options[post_share_button_design]' value='$design' $checked> $label<br></label>";
    }
}

// Share Buttons Input
function fsb_share_buttons_input() {
    $options = get_option('fsb_options');
    $share_buttons = isset($options['share_buttons']) ? $options['share_buttons'] : false;

    $checked = checked($share_buttons, true, false);
    echo "<label><input type='checkbox' name='fsb_options[share_buttons]' value='1' $checked> Display share buttons on posts</label>";
}

// Settings Section Text
function fsb_section_text() {
    echo '<p>Main description of this section here.</p>';
}

// Social Media URL Input
function fsb_social_media_url_input($args) {
    $options = get_option('fsb_options');
    $url = isset($options["{$args['id']}_url"]) ? esc_url($options["{$args['id']}_url"]) : '';
    echo "<input id='fsb_{$args['id']}_url' name='fsb_options[{$args['id']}_url]' type='text' value='{$url}' />";
}

// Individual Social Media URL Inputs
function fsb_facebook_url_input() { fsb_social_media_url_input(['id' => 'facebook']); }
function fsb_twitter_url_input() { fsb_social_media_url_input(['id' => 'twitter']); }
function fsb_instagram_url_input() { fsb_social_media_url_input(['id' => 'instagram']); }
function fsb_linkedin_url_input() { fsb_social_media_url_input(['id' => 'linkedin']); }
function fsb_youtube_url_input() { fsb_social_media_url_input(['id' => 'youtube']); }
function fsb_pinterest_url_input() { fsb_social_media_url_input(['id' => 'pinterest']); }

// Selected Buttons Input
function fsb_selected_buttons_input() {
    $options = get_option('fsb_options');
    $buttons = isset($options['selected_buttons']) ? $options['selected_buttons'] : [];
    $all_buttons = ['facebook' => 'Facebook', 'twitter' => 'Twitter', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'pinterest' => 'Pinterest'];
    foreach ($all_buttons as $key => $label) {
        $checked = in_array($key, $buttons) ? 'checked' : '';
        echo "<input type='checkbox' id='fsb_{$key}' name='fsb_options[selected_buttons][]' value='{$key}' {$checked} /> {$label}<br/>";
    }
}

// Validate Options
function fsb_options_validate($input) {
    foreach (['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest'] as $social) {
        $input["{$social}_url"] = esc_url_raw($input["{$social}_url"]);
    }
    return $input;
}

// Settings Page HTML
function fsb_settings_page_html() {
    ?>
    <div class="wrap">
        <h1>Floating Social Buttons Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('fsb_options_group');
            do_settings_sections('fsb-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Shortcode for Displaying Social Buttons
function fsb_social_buttons_shortcode() {
    $options = get_option('fsb_options');
    $selected_buttons = isset($options['selected_buttons']) ? $options['selected_buttons'] : [];
    $display_mode = isset($options['display_mode']) ? $options['display_mode'] : 'horizontal';
    $display_position = isset($options['display_position']) ? $options['display_position'] : 'bottom-right';

    $social_media = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'pinterest' => 'Pinterest'
    ];

    $icons = [
        'facebook' => 'fab fa-facebook-f',
        'twitter' => 'fab fa-twitter',
        'instagram' => 'fab fa-instagram',
        'linkedin' => 'fab fa-linkedin-in',
        'youtube' => 'fab fa-youtube',
        'pinterest' => 'fab fa-pinterest'
    ];

    if ($display_mode === 'vertical') {
        $buttons = '<div class="floating-social-container ';
        if ($display_position === 'bottom-right') {
            $buttons .= ' container-right ';
        } else {
            $buttons .= ' container-left ';
        }
        $buttons .= ' "> ';
        $buttons .= '<div class="floating-social-button">';
        $buttons .= '<i class="fas fa-share"></i>'; // Font Awesome icon for share
        $buttons .= '</div>';

        $buttons .= '<div class="floating-social-buttons ';

        if ($display_position === 'bottom-right') {
            $buttons .= ' right-0 ';
        } else {
            $buttons .= ' left-0 ';
        }
        $buttons .= ' "> ';

        foreach ($social_media as $key => $label) {
            if (in_array($key, $selected_buttons)) {
                $buttons .= '<a href="' . esc_url($options["{$key}_url"]) . '" target="_blank" class="social-icon"><i class="' . $icons[$key] . '"></i></a>';
            }
        }

        $buttons .= '</div>'; // Close floating-social-buttons

        $buttons .= '</div>'; // Close floating-social-container
    } else {
        $buttons = '<div class="floating-social-buttons-horizontal ';

        if ($display_position === 'bottom-right') {
            $buttons .= ' right-0 ';
        } else {
            $buttons .= ' left-0 ';
        }
        $buttons .= ' "> ';

        foreach ($social_media as $key => $label) {
            if (in_array($key, $selected_buttons)) {
                $buttons .= '<a title="' . $icons[$key] . '" href="' . esc_url($options["{$key}_url"]) . '" target="_blank" class="social-icon"><i class="' . $icons[$key] . '"></i></a>';
            }
        }
    }
    return $buttons;
}
add_shortcode('fsb_social_buttons', 'fsb_social_buttons_shortcode');

// Add Shortcode to Footer
function fsb_add_buttons_to_footer() {
    echo do_shortcode('[fsb_social_buttons]');
}
add_action('wp_footer', 'fsb_add_buttons_to_footer');

// Function to generate share links for the current post
function fsb_get_share_links($post) {
    $post_url = get_permalink($post->ID);
    $post_title = get_the_title($post->ID);

    $share_links = [
        'facebook' => 'https://www.facebook.com/sharer.php?u=' . urlencode($post_url),
        'twitter' => 'https://twitter.com/share?url=' . urlencode($post_url) . '&text=' . urlencode($post_title),
        'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($post_url),
        'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($post_url) . '&description=' . urlencode($post_title),
        'reddit' => 'https://reddit.com/submit?url=' . urlencode($post_url) . '&title=' . urlencode($post_title),
        'email' => 'mailto:?subject=' . rawurlencode($post_title) . '&body=' . rawurlencode($post_url)
    ];

    return $share_links;
}

// Function to display share buttons on posts
function fsb_display_share_buttons($content) {
    if (is_single()) {
        $options = get_option('fsb_options');
        if (isset($options['share_buttons']) && $options['share_buttons']) {
            $post = get_post();
            $share_links = fsb_get_share_links($post);
            $design = isset($options['post_share_button_design']) ? $options['post_share_button_design'] : 'design1';
            
            // Get the selected buttons to display
            $selected_buttons = isset($options['selected_buttons']) ? $options['selected_buttons'] : [];
            
            $share_buttons = '<div class="fsb-share-buttons ' . esc_attr($design) . '">';
            $share_buttons .= '<p>Share:</p>';
            foreach ($selected_buttons as $network) {
                if (isset($share_links[$network])) {
                    $share_buttons .= '<a class="fsb-share-icon ' . esc_attr($network) . '" href="' . esc_url($share_links[$network]) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-' . esc_attr($network) . '"></i> <span>' . esc_html(ucfirst($network)) . '</span></a>';
                }
            }
            $share_buttons .= '</div>';
            return $content . $share_buttons;
        }
    }
    return $content;
}


add_filter('the_content', 'fsb_display_share_buttons');
