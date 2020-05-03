<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CGR_Public
{
    const METHOD = 'math'; // Can be 'math' or 'google'
    const VERSION = 2; // can be 2 or 3

    protected static $captchaInstance;

    public function __construct()
    {
        self::$captchaInstance = 0;
    }

    public static function display($classes='')
    {
        $id = 'recaptcha' . self::$captchaInstance;

        echo '<div class="recaptcha-container '. self::METHOD . ' '. $classes . '" id="'. $id . '" data-instance="' . self::$captchaInstance . '">';

        if (self::METHOD == 'google') {
            CRCA_Google::display(self::VERSION);
        }
        elseif (self::METHOD == 'math')
            CRCA_Math::display();

        echo '</div>';

    }

    public static function verify()
    {
        if (self::METHOD == 'google')
            return CRCA_Google::verify();
        elseif (self::METHOD == 'math')
            return CRCA_Math::verify();
        else
            return 'success';
    }

    /**
     * Adds recaptcha to submit button markup
     *
     * @param  mixed $submit_button
     * @param  mixed $args
     * @return void
     */
    public function comment_form_add_recaptcha($submit_button)
    {
        $recaptcha = '';
        if (!is_user_logged_in()) {
            $recaptcha = self::display('');
            self::$captchaInstance++;
        }
        $html = $recaptcha . $submit_button;
        return $html;
    }


    /**
     * * Logged-in users : populate invisible inputs (user name & email)
     * * Logged-out users : check recaptcha
     *
     * @param  mixed $commentdata
     * @return void
     */
    public function verify_comment_recaptcha($commentdata)
    {
        $captchaResult = 'success';
        if (!is_user_logged_in()) {
            $captchaResult = self::verify();
        }

        if ($captchaResult == 'success')
            return $commentdata;
        else {
            $msg = __('<strong>ERROR</strong>: CAPTCHA verification failed, please go back and try again...', 'foodiepro');

            ob_start(); ?>
            <p class="error">
                <?= $msg ?>
            </p>
            <?php
            $html = ob_get_clean();
            wp_die($html);
        }
    }

}
