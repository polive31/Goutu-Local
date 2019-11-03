<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CGR_Public
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
    public function display($classes = '', $id='', $size = 'normal', $js_callback='', $version=2)
    {
        if ($version==2)
            $key = self::v2key('public');
        elseif ($version==3)
            $key = self::v3key('public');

        ?>
        <div id="<?= $id ?>" class="g-recaptcha <?= $classes; ?>" data-sitekey="<?= $key; ?>" data-callback="<?= $js_callback; ?>" data-size="<?= $size ?>" ></div>
        <?php
    }

    /* =================================================================*/
    /* = VERIFY GOOGLE RECAPTCHA
	/* =================================================================*/
    public static function verify()
    {
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
                wp_register_script('g-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0.0');
            }

            // This function is to be called by any form needing the ccf captcha
            public static function enqueue_scripts()
            {
                wp_enqueue_script('g-recaptcha');
            }

            /* =================================================================*/
            /* = Assets management
    /* =================================================================*/
            /*pds_captcha.php - un captcha mathématique bidouillé par passeurs de savoirs<br>
	plus d'infos sur http://passeurs-de-savoirs.fr/lab/lab2015/captcha-math.php
	*/
            public function pdscaptcha($step)
            {
                if ($step == "ask") {
                    $msg = __('For security reasons, and to avoid spam, please solve the following operation : ', 'foodiepro');
                    $tchiffres = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
                    $tlettres = array(
                        __('zero', 'foodiepro'),
                        __('one', 'foodiepro'),
                        __('two', 'foodiepro'),
                        __('three', 'foodiepro'),
                        __('four', 'foodiepro'),
                        __('five', 'foodiepro'),
                        __('six', 'foodiepro'),
                        __('seven', 'foodiepro'),
                        __('eight', 'foodiepro'),
                        __('nine', 'foodiepro'),
                        __('ten', 'foodiepro'),
                        __('eleven', 'foodiepro'),
                        __('twelve', 'foodiepro')
                    );
                    $premier = rand(0, count($tchiffres) - 1);
                    $second = rand(0, count($tchiffres) - 1);

                    if ($second <= $premier) {
                        $resultat = $tchiffres[$premier] - $tchiffres[$second];
                        $operation = "Combien font " . $tlettres[$premier] . " moins " . $tlettres[$second] . " (en chiffres) ?";
                    } else if ($second > $premier) {
                        $resultat = $tchiffres[$second] - $tchiffres[$premier];
                        $operation = "Combien font " . $tlettres[$second] . " moins " . $tlettres[$premier] . " (en chiffres) ?";
                    } else {
                        $resultat = $tchiffres[$premier] + $tchiffres[$second];
                        $operation = "Combien font " . $tlettres[$premier] . " plus " . $tlettres[$second] . " (en chiffres) ?";
                    }
                    // echo 'resultat de reference avant md5 : ' . $resultat . "<br>";
                    $resultat = md5($resultat);
                    // echo 'resultat de reference après md5 : ' . $resultat . "<br>";
                    $o = "";
                    foreach (str_split(utf8_decode($operation)) as $obj) {
                        $o .= "&#" . ord($obj) . ";";
                    }

                    $html = '<p><label for="reponsecap">' . $msg . '<br>';
                    $html .= '<span class="mathquestion">' . $o . '</span></label><br>';
                    $html .= '<input type="text" name="reponsecap" value="" />';
                    $html .= '<input name="reponsecapcode" type="hidden" value="' . $resultat . '" /></p>';
                    return $html;
                } else {
                    // echo 'reponse utilisateur' . $step["reponsecap"] . "<br>";
                    // echo 'MD5 de reference' . $step["reponsecapcode"] . "<br>";
                    if (md5(htmlspecialchars($step["reponsecap"])) == htmlspecialchars($step["reponsecapcode"]))
                        return true;
                    else
                        return false;
                }
            }
        }
