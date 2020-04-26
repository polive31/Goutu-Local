<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/* =================================================================*/
/* = CUSTOM LOGIN =*/
/* =================================================================*/

/* Add color theme body class to login page */
add_filter('login_body_class', 'foodiepro_login_classes');
function foodiepro_login_classes($classes)
{
$classes[] = 'login_color-theme-' . LOGIN_COLOR_THEME;
return $classes;
}

add_filter('login_errors', 'foodiepro_login_error_message');
function foodiepro_login_error_message($error){
    //check if that's the error you are looking for
    $pos = strpos($error, 'incorrect');
    if (is_int($pos)) {
        //its the right error so you can overwrite it
        $error = __('<strong>ERROR :</strong> Incorrect email address or password.','foodiepro');
    }
    return $error;
}

/* Sets login page color theme */
add_action('login_enqueue_scripts', 'custom_login_style');
function custom_login_style()
{
$theme_css = 'custom-login-styles-' . LOGIN_COLOR_THEME . '.css';
foodiepro_enqueue_style('login-commons', '/login/custom-login-styles-default.css');
foodiepro_enqueue_style('login-theme', '/login/' . $theme_css, CHILD_THEME_URL, CHILD_THEME_PATH, array('login-commons'));
wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Oswald', array(), CHILD_THEME_VERSION);
}

/* Sets login page logo & url */
add_filter('login_headerurl', 'my_login_logo_url');
function my_login_logo_url()
{
return get_bloginfo('url');
}

add_filter('login_headertext ', 'my_login_logo_url_title');
function my_login_logo_url_title()
{
$output = get_bloginfo('name') . '-' . get_bloginfo('description');
return $output;
}

/* customize username label in any login form called by wp_login_form() function */
add_filter('login_form_defaults', 'custom_wp_login_form');
function custom_wp_login_form()
{
$args = array(
'label_username' => __('Enter Email Address', 'foodiepro'),
'label_password' => __('Enter Password', 'foodiepro'),
'label_remember' => __('Remember Login State', 'foodiepro'),
'label_log_in' => __('Please Log In', 'foodiepro'),
);
return $args;
}

/* Redirect register url towards peepso register page */
add_filter('register_url', 'custom_register_url');
function custom_register_url($register_url)
{
    // $register_url = do_shortcode('[permalink peepso="register"]');
    $register_url = foodiepro_get_permalink(array('community'=>'register'));
    return $register_url;
}


/* Redirect register url towards peepso register page */
add_filter('lostpassword_url', 'custom_lostpassword_url');
function custom_lostpassword_url()
{
    // $lostpassword_url = do_shortcode('[permalink peepso="register"]');
    $lostpassword_url = foodiepro_get_permalink(array('community'=> 'recover'));
    return $lostpassword_url;
}

/* customize username label in wp-login.php page
Indeed login_form_defaults filter isn't active */
add_action('login_head', 'cc_login_username_label');
function cc_login_username_label()
{
add_filter('gettext', 'cc_login_username_label_change', 20, 3);
}
function cc_login_username_label_change($translated_text, $text, $domain)
{
if ($text === 'Username or Email Address') {
$translated_text = foodiepro_esc(__('Email Address', 'foodiepro')); // Use WordPress's own translation of 'Username'
} elseif ($text === 'Register') {
$translated_text = foodiepro_esc(__('Not yet a member ?', 'foodiepro')); // Use WordPress's own translation of 'Username'
}
return $translated_text;
}

// Change login credentials to email address only
remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
add_filter('authenticate', 'my_authenticate_username_password', 20, 3);
function my_authenticate_username_password($user, $username, $password)
{
if (!empty($username)) {
if (!strpos($username, '@'))
return new WP_Error('Invalid email address.', foodiepro_esc(__('<strong>ERROR</strong>: Invalid login. Please make sure to use your EMAIL ADDRESS to log-in.', 'foodiepro'))); //returns nothing if not valid email
$user = get_user_by('email', $username);
}
if (isset($user->user_login, $user))
$username = $user->user_login;
return wp_authenticate_username_password(NULL, $username, $password);
}


/* Redirect towards homepage on logout */
add_action('wp_logout', 'go_home');
function go_home()
{
wp_redirect(home_url());
exit;
}



// add_filter('login_redirect', 'redirect_and_flush_cache_on_login', 10, 2);
// function redirect_and_flush_cache_on_login($redirect_final, $redirect_initial)
// {
// // $clear_cache_path = '?action=wpfastestcache&type=clearcache&token=' . WPFC_CLEAR_CACHE_URL_TOKEN;
// // $url = home_url($clear_cache_path);
// // $home_ID = get_option('page_on_front');
// // wpfc_clear_post_cache_by_id($home_ID);
// return home_url();
// }


/* Prevent new users (not yet approved) to log in */
// add_filter('wp_authenticate_user', 'block_new_users',10,1);
/* IMPORTANT DO NOT USE WITH PEEPSO OTHERWISE IT WILL CONFLICT WITH THE ACTIVATION PROCESS !!!
PEEPSO ALREADY IMPLEMENTS A MANUAL USER VERIFICATION BY ADMINISTRATOR SO THIS MAKES
THIS FUNCTION USELESS & CONFLICTING */
// function block_new_users ($user) {
// $role=$user->roles[0];
// if ( $role=='pending' ) {
// $approve_url=do_shortcode('[permalink slug="attente-approbation"]');
// // $approve_url=get_permalink('10066');
// $msg=sprintf(__( '<strong>ERROR</strong>: User pending <a href="%s">approval</a>.', 'foodiepro' ),$approve_url);
// return new WP_Error( 'user_not_approved', $msg);
// }
// else
// return $user;
// }
