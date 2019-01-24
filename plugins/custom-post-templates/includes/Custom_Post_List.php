<?php

class Custom_Posts_List {

    const POST_PUBLISH_SLUG = 'publier-recettes';
    const POST_NEW_SLUG = 'nouvelle-recette';
    const POST_EDIT_SLUG = 'modifier-recette';

    protected static $PLUGIN_PATH;  
    protected static $PLUGIN_URI;  
    protected static $_UploadPath; 

    protected static $required_fields; 
    protected static $required_fields_labels; 
    
    // public function __construct( $name = 'user-submissions' ) {
    public function __construct() {
        self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
        self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );


        // add_action( 'wp', array( $this, 'hydrate') );

        add_filter( 'custom_post_list_buttons', array( $this, 'add_post_edit_button') );

        // Submission form customization
        add_filter( 'wp_dropdown_cats', array($this, 'add_lang_to_select'));
        // add_action('wp_head',array($this,'add_custom_js'));        

        // Ajax callbacks
        add_action( 'wp_ajax_custom_post_submissions_delete_post', array( $this, 'ajax_user_delete_post') );
        add_action( 'wp_ajax_nopriv_custom_post_submissions_delete_post', array( $this, 'ajax_user_delete_post') );
      
    }

/********************************************************************************
****                   POST LIST FUNCTIONS                           **********
********************************************************************************/

    public function display_posts( $posts, $edit=false, $title='' ) {
        $output = '';

        $output .= '<h3>' . $title . '</h3>';

        $output .= '<table class="custom-post-list">';

        $statuses = get_post_statuses();
        
        foreach ( $posts as $post ) {
            $image_url = $post->image_ID() > 0 ? $post->image_url( 'mini-thumbnail' ) : self::$PLUGIN_URI . '/img/image_placeholder.png';
 
            $view_url = 'href="' . get_permalink($post->ID()) . '" ';    
            $view_title = 'title="' . __('Preview post', 'foodiepro') . '" ';
            // $edit_url = $edit?'href="' . get_permalink() . self::post_EDIT_SLUG . '?wpurp-edit-post=' . $post->ID() . '" ':$view_url;   
            $edit_url = $edit?'href="' . get_permalink() . self::post_EDIT_SLUG . '?wpurp-edit-post=' . $post->ID() . '" ':$view_url;   
            $edit_title = $edit?'title="' . __('Edit post', 'foodiepro') . '" ':$view_title;
 
            $item = '<tr class="post-list-row ' . $post->post_status() . '">';
            // $item .= '<td class="post-list-thumbnail"><a ' . $edit_url . $edit_title . '><img src="' . $image_url . '"></a></td>';
            $item .= '<td class="post-list-thumbnail"><a ' . $view_url . $view_title . '><img src="' . $image_url . '"></a></td>';
            // $item .= '<td class="post-list-title"><a ' . $edit_url . $edit_title . '>' . $post->title() . '</a></td>';
            $item .= '<td class="post-list-title"><a ' . $view_url . $view_title . '>' . $post->title() . '</a></td>';

            // $favinfo = Custom_post_Favorite::is_favorite_post( $post->ID() );
            // $favlist = $favinfo[1];
            // $favicon = Custom_post_Favorite::get_icon( $favlist );
            

            if ($edit) {
                $item .= '<td class="post-list-status">' . $statuses[ $post->post_status() ] . '</td>';
                $item .= '<td class="post-list-actions">';
                    $item .= '<div class="post-edit" title="' . __('Edit post', 'foodiepro') . '">';
                    $item .= '<a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a>';
                    // $item .= '<a ' . $view_url . $view_title . '><i class="fa fa-eye"></i></a>';
                    $item .= '</div>';
                
                    $item .= '<div class="post-delete" title="' . __('Delete post', 'foodiepro') . '"><i class="fa fa-trash user-submissions-delete-post nodisplay" data-id="' . $post->ID() . '" data-title="' . esc_attr( $post->title() ) . '"></i></td>';
                    $item .= '</div>';
                $item .= '</td>';
            }
            else {
                // $item .= '<td class="post-list-list" title="' . Custom_post_Favorite::get_field( $favlist, 'label' ) . '">' . $favicon . '</td>';
            }
            $item .= '</tr>';
 
            $output .= apply_filters( 'custom_wpurp_post_list_item', $item, $post );
        }
        $output .= '</table>';
        return $output;
    }  




/********************************************************************************
*********************         ACTIONS CALLBACKS       ***************************
********************************************************************************/

    //* Customize the entry meta in the entry header (requires HTML5 theme support)
    public function add_post_edit_button($post_info) {

        if ( !is_singular('post') ) return;

        global $post;
        $current_user = wp_get_current_user();
        if ($post->post_author == $current_user->ID || current_user_can('administrator')) { 

            $edit_url = 'href="' . get_permalink() . self::post_EDIT_SLUG . '?wpurp-edit-post=' . $post->ID . '" ';
            $edit_title = 'title="' . __('Edit post', 'foodiepro') . '" ';

            $post_info .= '<span class="edit-button"><a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a></span>';    
        }
        return $post_info;
    }    


/********************************************************************************
****                           AJAX CALLBACKS                          **********
********************************************************************************/

    public function ajax_user_delete_post() {

        if( ! is_user_logged_in() ) die();

        if(check_ajax_referer( 'custom_user_submissions_list', 'security', false ) ) {
            global $user_ID;

            $post_id = intval( $_POST['post'] );
            $post = get_post( $post_id );

            if( $post->post_type == 'post' && $post->post_author == $user_ID ) {
                wp_delete_post( $post_id );
                wp_send_json_success( 'post deleted !');
            }
            else {
                wp_send_json_error( 'post not deleted');
            }
        }
        else {
            wp_send_json_error( 'Nonce not recognized');
        }

        die();
    }


}

new Custom_Posts_List();