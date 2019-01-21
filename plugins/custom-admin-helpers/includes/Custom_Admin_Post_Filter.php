<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CustomAdminPostFilter {

    const POST_TYPE ='recipe';
    const META_KEY ='ingredient_note';

    public function __construct() { 
        add_action( 'restrict_manage_posts', array($this,'restrict_manage_posts') );
        add_filter( 'parse_query', array($this, 'add_posts_filter' ) );
    }

    

    public function restrict_manage_posts(){
        $type = 'post';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }

        //only add filter to post type you want
        if (self::POST_TYPE == $type){
            //change this to the list of values you want to show
            //in 'label' => 'value' format
            $values = array(
                'label' => 'value', 
                'label1' => 'value1',
                'label2' => 'value2',
            );
            ?>
            <select name="ADMIN_FILTER_FIELD_VALUE">
            <option value=""><?php _e('Filter By ', 'foodiepro'); ?></option>
            <?php
                $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
                foreach ($values as $label => $value) {
                    printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v? ' selected="selected"':'',
                            $label
                        );
                    }
            ?>
            </select>
            <?php
        }
    }

    public function add_posts_filter( $query ){
        global $pagenow;
        $type = 'post';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }
        if ( self::POST_TYPE == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
            $query->query_vars['meta_key'] = self::META_KEY;
            $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
        }
    }

}