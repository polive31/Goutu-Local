  <?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CRCA_Math {

    public static function display() {
        return self::pdscaptcha('ask');
    }

    public static function verify()
    {
        return self::pdscaptcha('check');
    }


    //TODO ADD HONEYPOT
    public static function pdscaptcha($step)
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
