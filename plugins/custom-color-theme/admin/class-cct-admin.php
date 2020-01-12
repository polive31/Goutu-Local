<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CCT_Admin
{
    const PREFIX= 'color-theme-';
    const DEFAULT_COLOR_THEME='spring';
    private $options;

    public function __construct()
    {
        $this->options = get_option('cct_options');
        if (empty($this->options['color'])) {
            $this->options['color'] = self::DEFAULT_COLOR_THEME;
        }
        if (empty($this->options['path'])) {
            $this->options['path'] = get_stylesheet_directory() . '/assets/css/color/';
        }
    }

    public function add_cct_options_page()
    {
        // add_options_page('Custom Color Theme', 'Custom Color Theme', 'manage_options', 'cct-options', array($this, 'cct_options'), 60);
        add_submenu_page('themes.php','Custom Color Theme', 'Custom Color Theme', 'manage_options', 'cct-options', array($this, 'cct_options'));
    }

    public function register_cct_settings()
    {
        //register our settings
        register_setting('cct_settings_group', 'cct_options');

        add_settings_section(
            'cct_general_settings',
            __('Custom Color Theme general settings', 'foodiepro'),
            array($this, 'cct_general_settings_cb'),
            'cct_settings_group'
        );

        add_settings_section(
            'cct_color_settings',
            __('Custom Color Theme color settings', 'foodiepro'),
            array($this, 'cct_color_settings_cb'),
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
            'cct_color_theme_select_field',
            __('Current Color Theme', 'foodiepro'),
            array($this, 'cct_color_theme_select_field_render'),
            'cct_settings_group',
            'cct_color_settings'
        );
    }


    /* CALLBACKS */
    public function cct_general_settings_cb()
    {
        echo __('These are the general settings for the Custom Color Theme selection plugin.', 'wordpress');
    }

    public function cct_color_settings_cb()
    {
        echo __('These are the color settings for the Custom Color Theme selection plugin.', 'wordpress');
    }


    public function cct_path_field_render()
    {
?>
        <input type='text' name='cct_options[path]' value='<?php echo $this->options['path']; ?>'>
    <?php
    }

    public function cct_color_theme_select_field_render_bak()
    {
    ?>
        <input type='text' name='cct_options[color]' value='<?php echo $this->options['color']; ?>'>
    <?php
    }

    public function cct_color_theme_select_field_render()
    {
    ?>
        <select class="widefat" id="" name="cct_options[color]" style="width:100%;">
            <?php
            $stylesheets = list_files($this->options['path'], 1, array());
            foreach ($stylesheets as $stylesheet) {
                $info = pathinfo($stylesheet);
                if ($info['extension'] == 'css' && !strpos($info['basename'], '.min.css')) {
                    $color=str_replace( self::PREFIX, '', $info['filename']);
            ?>
                    <option value="<?= $color; ?>" <?php selected($this->options['color'], $color); ?>><?= $color; ?></option>
            <?php
                   // echo $info['basename'] . '<br>';
                }
            } ?>
        </select>
    <?php
    }



    /* FORM */
    public function cct_options()
    {
    ?>
        <div class="wrap">
            <h2>Custom Color Theme Selection</h2>
            <form method="post" action="options.php">

                <?php settings_fields('cct_settings_group'); ?>
                <?php do_settings_sections('cct_settings_group'); ?>

                <?php submit_button(__('Save Options', 'foodiepro')) ?>

            </form>
        </div>
<?php
    }
}
