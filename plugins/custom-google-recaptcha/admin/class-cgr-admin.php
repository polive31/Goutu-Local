<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}



class CGR_Admin
{

    public function add_cgr_options_page()
    {
        add_options_page('Custom Google Recaptcha', 'Custom Google Recaptcha', 'manage_options', 'cgr-options', array($this, 'cgr_options'));
    }

    public function register_cgr_settings()
    {
        //register our settings
        register_setting('cgr_settings_group', 'cgr_keys_array');

        add_settings_section(
            'cgr_settings_section_v2',
            __('Google Recaptcha V2 keys', 'foodiepro'),
            array($this, 'cgr_settings_section_v2_cb'),
            'cgr_settings_group'
        );

        add_settings_section(
            'cgr_settings_section_v3',
            __('Google Recaptcha V3 keys', 'foodiepro'),
            array($this, 'cgr_settings_section_v3_cb'),
            'cgr_settings_group'
        );

        add_settings_field(
            'cgr_settings_field_v2_public',
            __('V2 Public Key', 'foodiepro'),
            array($this, 'cgr_settings_field_v2_public_render'),
            'cgr_settings_group',
            'cgr_settings_section_v2'
        );

        add_settings_field(
            'cgr_settings_field_v2_private',
            __('V2 Private Key', 'foodiepro'),
            array($this, 'cgr_settings_field_v2_private_render'),
            'cgr_settings_group',
            'cgr_settings_section_v2'
        );

        add_settings_field(
            'cgr_settings_field_v3_public',
            __('V3 Public Key', 'foodiepro'),
            array($this, 'cgr_settings_field_v3_public_render'),
            'cgr_settings_group',
            'cgr_settings_section_v3'
        );

        add_settings_field(
            'cgr_settings_field_v3_private',
            __('V3 Private Key', 'foodiepro'),
            array($this, 'cgr_settings_field_v3_private_render'),
            'cgr_settings_group',
            'cgr_settings_section_v3'
        );
    }


    /* CALLBACKS */
    public function cgr_settings_section_v2_cb()
    {
        echo __('Those are the public & private keys for the Google recaptcha V2 API.', 'wordpress');
    }

    public function cgr_settings_section_v3_cb()
    {
        echo __('Those are the public & private keys for the Google recaptcha V3 API.', 'wordpress');
    }

    public function cgr_settings_field_v2_public_render()
    {
        $options = get_option('cgr_keys_array');
        ?>
        <input type='text' name='cgr_keys_array[v2_public]' value='<?php echo $options['v2_public']; ?>'>
    <?php
        }

    public function cgr_settings_field_v2_private_render()
    {
        $options = get_option('cgr_keys_array');
        ?>
    <input type='text' name='cgr_keys_array[v2_private]' value='<?php echo $options['v2_private']; ?>'>
<?php
    }

    public function cgr_settings_field_v3_private_render()
    {
        $options = get_option('cgr_keys_array');
        ?>
    <input type='text' name='cgr_keys_array[v3_private]' value='<?php echo $options['v3_private']; ?>'>
<?php
    }

    public function cgr_settings_field_v3_public_render()
    {
        $options = get_option('cgr_keys_array');
        ?>
    <input type='text' name='cgr_keys_array[v3_public]' value='<?php echo $options['v3_public']; ?>'>
<?php
    }



    /* FORM */
    public function cgr_options()
    {
        ?>
    <div class="wrap">
        <h2>Custom Google Recaptcha Options</h2>
        <form method="post" action="options.php">

            <?php settings_fields('cgr_settings_group'); ?>
            <?php do_settings_sections('cgr_settings_group'); ?>

            <?php submit_button(__('Save Options', 'foodiepro')) ?>

        </form>
    </div>
    <?php
    }
}
