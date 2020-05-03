<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CRCA_Google
{

    private static $V2_PUBLIC_KEY;
    private static $V2_PRIVATE_KEY;
    private static $V3_PUBLIC_KEY;
    private static $V3_PRIVATE_KEY;

    const G_RECAPTCHA_API = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct()
    {
        $options = get_option('cgr_keys_array');
        self::$V2_PUBLIC_KEY = $options['v2_public'];
        self::$V2_PRIVATE_KEY = $options['v2_private'];
        self::$V3_PUBLIC_KEY = $options['v3_public'];
        self::$V3_PRIVATE_KEY = $options['v3_private'];
    }

    /* =================================================================*/
    /* = DISPLAY GOOGLE RECAPTCHA
    /* =================================================================*/
    public static function display($version = 2)
    {
        if ($version == 2)
            $key = self::v2key('public');
        elseif ($version == 3)
            $key = self::v3key('public');

        self::enqueue_scripts();

        $size = 'normal';
        $style= '-moz-transform:scale(0.77); -ms-transform:scale(0.77); -o-transform:scale(0.77); -moz-transform-origin:0; -ms-transform-origin:0; -o-transform-origin:0; -webkit-transform:scale(0.77); transform:scale(0.77); -webkit-transform-origin:0 0; transform-origin:0; filter: progid:DXImageTransform.Microsoft.Matrix(M11=0.77,M12=0,M21=0,M22=0.77,SizingMethod=\'auto expand\');';
        $style= wp_is_mobile()?$style:'';

        ?>
            <div class="g-recaptcha" style="<?= $style; ?>" data-sitekey="<?= $key; ?>" data-callback="" data-size="<?= $size ?>"></div>
        <?php
    }

    /* =================================================================*/
    /* = VERIFY GOOGLE RECAPTCHA
	/* =================================================================*/
    public static function verify()
    {
        $captcha = false;
        $retries = 4;
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
        }
        if (!$captcha) {
            return 'missing';
        }
        do {
            $ip = $_SERVER['REMOTE_ADDR'];

            $filename = add_query_arg(array(
                'secret'    => self::v2key('private'),
                'response'  => $captcha,
                'remoteip'  => $ip,
            ), self::G_RECAPTCHA_API);

            // $filename = "https://www.google.com/recaptcha/api/siteverify?secret=" . self::v2key('private') . "&response=" . $captcha . "&remoteip=" . $ip;
            $response = file_get_contents($filename);
            $responseKeys = json_decode($response, true);
            if (intval($responseKeys["success"]) !== 1) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
            --$retries;
        } while ($result == 'fail' && $retries > 0);
        return $result;
    }

    /* =================================================================*/
    /* = GETTERS
	/* =================================================================*/
    public static function v2key($type = 'public')
    {
        // return ($type=='private') ? get_option('cgr_keys_array[v2_private]') : get_option('cgr_keys_array[v2_public]');
        return ($type == 'private') ? self::$V2_PRIVATE_KEY : self::$V2_PUBLIC_KEY;
    }

    public static function v3key($type = 'public')
    {
        // return ($type=='private') ? get_option('cgr_keys_array[v3_private]') : get_option('cgr_keys_array[v3_public]');
        return ($type == 'private') ? self::$V3_PRIVATE_KEY : self::$V3_PUBLIC_KEY;
    }

    /* =================================================================*/
    /* = Assets management
    /* =================================================================*/
    public function register_recaptcha_script()
    {
        wp_register_script('g-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0.0', true);
    }

    // This function is to be called by any form needing the ccf captcha
    public static function enqueue_scripts()
    {
        wp_enqueue_script('g-recaptcha');
    }


}
