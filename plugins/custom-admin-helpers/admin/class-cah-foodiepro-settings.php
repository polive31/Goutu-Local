<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CAH_Options
{
    const PREFIX = 'color-theme-';
    const LOGIN_PREFIX = 'custom-login-styles-';
    const DEFAULT_COLOR_THEME = 'spring';
    const DEFAULT_LOGIN_COLOR_THEME = 'spring';

    public function hydrate()
    {
        if (!CAH_Assets::get_option('color')) {
            CAH_Assets::set_option('color', self::DEFAULT_COLOR_THEME);
        }
        if (!CAH_Assets::get_option('login-color')) {
            CAH_Assets::set_option('login-color', self::DEFAULT_LOGIN_COLOR_THEME);
        }
        if (!CAH_Assets::get_option('path')) {
            CAH_Assets::set_option('path', get_stylesheet_directory() . '/assets/css/color/');
        }
        if (!CAH_Assets::get_option('login-path')) {
            CAH_Assets::set_option('login-path', get_stylesheet_directory() . '/login/');
        }
        if (!CAH_Assets::get_option('reload')) {
            CAH_Assets::set_option('reload', '0');
        }
        if (!CAH_Assets::get_option('adminbar_visibility')) {
            CAH_Assets::set_option('adminbar_visibility', 'admin'); // wp, loggedin, all
        }
        if (!CAH_Assets::get_option('show-console-logs')) {
            CAH_Assets::set_option('show-console-logs', '0'); // '1' or '0'
        }
    }

    /* Admin notice */
    public function maybe_display_admin_notice()
    {
        if (CAH_Assets::get_option('reload') == '1') {
            add_action('admin_notices', array($this, 'force_reload_activated_admin_notice'));
        }
    }
    public function force_reload_activated_admin_notice()
    {
        echo '<div id="message" class="error fade"><p style="line-height: 150%">';
        _e('<strong>Stylesheet Forced Reload is activated within Custom Color Theme plugin</strong> This reduces the page load speed for your users.');
        echo '</p></div>';
    }

    public function add_foodiepro_options_page()
    {
        add_submenu_page('themes.php', 'Foodiepro Display Settings', 'Foodiepro Display Settings', 'manage_options', 'foodiepro-options', array($this, 'foodiepro_options_form_render'));
    }

    public function register_color_theme_settings()
    {
        //register our settings
        register_setting('cct_settings_group', 'foodiepro');

        add_settings_section(
            'cct_general_settings',
            __('Custom Color Theme general settings', 'foodiepro'),
            array($this, 'cct_general_settings_cb'),
            'cct_settings_group'
        );

        add_settings_field(
            'cct_path_field',
            __('Path to the color themes', 'foodiepro'),
            array($this, 'cct_path_field_render'),
            'cct_settings_group',
            'cct_general_settings',
            array('class' => 'cct-path')
        );

        add_settings_field(
            'cct_login_path_field',
            __('Path to the login color themes', 'foodiepro'),
            array($this, 'cct_login_path_field_render'),
            'cct_settings_group',
            'cct_general_settings',
            array('class' => 'cct-path')
        );

        add_settings_field(
            'cct_reload_field',
            __('Reload all stylesheets', 'foodiepro'),
            array($this, 'cct_reload_field_render'),
            'cct_settings_group',
            'cct_general_settings',
            array('class' => 'cct-reload')
        );

        add_settings_field(
            'cct_color_theme_select_field',
            __('Current Color Theme', 'foodiepro'),
            array($this, 'cct_color_theme_select_field_render'),
            'cct_settings_group',
            'cct_general_settings'
        );

        add_settings_field(
            'cct_login_color_theme_select_field',
            __('Current Login Color Theme', 'foodiepro'),
            array($this, 'cct_login_color_theme_select_field_render'),
            'cct_settings_group',
            'cct_general_settings'
        );
    }

    public function register_adminbar_settings()
    {
        //register our settings
        register_setting('cah_adminbar_settings_group', 'foodiepro');

        add_settings_section(
            'cah_adminbar_general_settings',
            __('Foodiepro Debug settings', 'foodiepro'),
            array($this, 'cah_adminbar_general_settings_cb'),
            'cah_adminbar_settings_group'
        );

        add_settings_field(
            'cah_visibility_field',
            __('Admin Toolbar Visibility', 'foodiepro'),
            array($this, 'cah_visibility_field_cb'),
            'cah_adminbar_settings_group',
            'cah_adminbar_general_settings',
            array('class' => 'cah-adminbar')
        );

        add_settings_field(
            'cah_showlog_field',
            __('Javascript Console Logs Visibility', 'foodiepro'),
            array($this, 'cah_showlog_field_cb'),
            'cah_adminbar_settings_group',
            'cah_adminbar_general_settings',
            array('class' => 'cah-adminbar')
        );
    }

    /* DEBUG SETTINGS CALLBACKS */

    public function cah_adminbar_general_settings_cb()
    {
        echo __('These are the debug general settings.', 'foodiepro');
    }

    public function cah_visibility_field_cb()
    {
?>
        <select class="widefat" id="" name="foodiepro[adminbar_visibility]" ;">
            <option value="admin" <?php selected(CAH_Assets::get_option('adminbar_visibility'), 'admin'); ?>><?= __('Admin accounts', 'foodiepro');; ?></option>
            <option value="wp" <?php selected(CAH_Assets::get_option('adminbar_visibility'), 'wp'); ?>><?= __('WP User setting', 'foodiepro');; ?></option>
            <option value="loggedin" <?php selected(CAH_Assets::get_option('adminbar_visibility'), 'loggedin'); ?>><?= __('Logged-in users', 'foodiepro');  ?></option>
            <option value="all" <?php selected(CAH_Assets::get_option('adminbar_visibility'), 'all'); ?>><?= __('All visitors', 'foodiepro');  ?></option>
        </select>
    <?php
    }

    public function cah_showlog_field_cb()
    {
    ?>
        <input type='checkbox' name='foodiepro[show-console-logs]' value='1' <?php checked(CAH_Assets::get_option('show-console-logs')); ?>'>
    <?php
    }


    // CUSTOM COLOR THEME CALLBACKS
    public function cct_general_settings_cb()
    {
        echo __('These are the Custom Color Theme settings.', 'foodiepro');
    }

    public function cct_path_field_render()
    {
    ?>
        <input type='text' name='foodiepro[path]' value='<?php echo CAH_Assets::get_option('path'); ?>'>
    <?php
    }

    public function cct_login_path_field_render()
    {
    ?>
        <input type='text' name='foodiepro[login-path]' value='<?php echo CAH_Assets::get_option('login-path'); ?>'>
    <?php
    }

    public function cct_reload_field_render()
    {
    ?>
        <select class="widefat" id="" name="foodiepro[reload]" ;">
            <option value="1" <?php selected(CAH_Assets::get_option('reload'), '1'); ?>><?= __('Yes', 'foodiepro');; ?></option>
            <option value="0" <?php selected(CAH_Assets::get_option('reload'), '0'); ?>><?= __('No', 'foodiepro');  ?></option>
        </select>
    <?php
    }

    public function cct_color_theme_select_field_render()
    {
    ?>
        <select class="widefat" id="" name="foodiepro[color]" style="width:100%;">
            <?php
            $stylesheets = list_files(CAH_Assets::get_option('path'), 1, array());
            foreach ($stylesheets as $stylesheet) {
                $info = pathinfo($stylesheet);
                if (isset($info['extension']) && $info['extension'] == 'css' && !strpos($info['basename'], '.min.css')) {
                    $color = str_replace(self::PREFIX, '', $info['filename']);
            ?>
                    <option value="<?= $color; ?>" <?php selected(CAH_Assets::get_option('color'), $color); ?>><?= $color; ?></option>
            <?php
                    // echo $info['basename'] . '<br>';
                }
            } ?>
        </select>
    <?php
    }

    public function cct_login_color_theme_select_field_render()
    {
    ?>
        <select class="widefat" id="" name="foodiepro[login-color]" style="width:100%;">
            <?php
            $stylesheets = list_files(CAH_Assets::get_option('login-path'), 1, array());
            foreach ($stylesheets as $stylesheet) {
                $info = pathinfo($stylesheet);
                if (isset($info['extension']) && $info['extension'] == 'css' && !strpos($info['basename'], '.min.css')) {
                    $color = str_replace(self::LOGIN_PREFIX, '', $info['filename']);
            ?>
                    <option value="<?= $color; ?>" <?php selected(CAH_Assets::get_option('login-color'), $color); ?>><?= $color; ?></option>
            <?php
                    // echo $info['basename'] . '<br>';
                }
            } ?>
        </select>
    <?php
    }



    /* FORM */
    public function foodiepro_options_form_render()
    {
    ?>
        <div class="wrap">
            <h2>Foodiepro Display Settings</h2>
            <form method="post" action="options.php">

                <?php settings_fields('cct_settings_group'); ?>
                <?php do_settings_sections('cct_settings_group'); ?>

                <?php settings_fields('cah_adminbar_settings_group'); ?>
                <?php do_settings_sections('cah_adminbar_settings_group'); ?>

                <?php submit_button(__('Save Options', 'foodiepro')) ?>

            </form>
        </div>
<?php
    }
}
