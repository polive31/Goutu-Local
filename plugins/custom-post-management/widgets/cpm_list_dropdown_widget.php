<?php

// Block direct requests
if (!defined('ABSPATH'))
    die('-1');


class CPM_List_Dropdown_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'CPM_List_Dropdown_Widget',
            __('Dropdown Post Lists Widget', 'foodiepro'),
            array(
                'description' => __('Provides a customizable dropdown select box for redirecting to post filtering queries.', 'foodiepro')
            )
        );
    }

    public function widget($args, $instance)
    {
        $slug = get_post_field('post_name', get_post());
        $post_type = CPM_Assets::get_type_from_slug($slug);

        $params = array(
            'title' => __('Publish state', 'foodiepro'),
            'queryvar' => 'status',
            'options' => CPM_Assets::get_statuses($post_type, 'registered',true),
        );
        $params = apply_filters('cpm_list_dropdown_widget_args', $params, $slug);

        echo $args['before_widget'];
        echo $args['before_title'] . $params['title'] . $args['after_title'];

        $current = esc_html(get_query_var($params['queryvar'], false));

?>
        <label class="screen-reader-text" for="sort_dropdown"><?php echo $params['title']; ?></label>
        <select name="sort_dropdown" id="sort_dropdown" class="dropdown-select postform">
            <?php

            foreach ($params['options'] as $option => $data) {
                if ($option == 'all') {
                    $selected = selected($current, false, false);
                } else {
                    $selected = selected($current, $option, false);
                }
            ?>
                <option class="level-0" <?= $selected; ?> value="<?= $option ?>"><?= $data['label']; ?></option>
            <?php
            }

            ?>

        </select>


        <script type="text/javascript">
            /* <![CDATA[ */
            (function() {
                var dropdown = document.getElementById("sort_dropdown");

                function onDropDownChange() {
                    var choice = dropdown.options[dropdown.selectedIndex].value;
                    var currentLocation = jQuery(location).attr('href');
                    var newLocation = currentLocation;
                    console.log('Choice = ', choice);
                    if (choice == "all") choice="";
                    newLocation = foodieproUpdateQueryStringParameter(newLocation, '<?= $params['queryvar']; ?>', choice)

                    console.log('New Location = ' + newLocation);
                    location.href = newLocation;
                }
                dropdown.onchange = onDropDownChange;
            })();
            /* ]]> */
        </script>

    <?php
        // Output end
        echo $args['after_widget'];
    }


    // Widget Backend
    public function form($instance)
    {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('New title', 'crm');
        // Widget admin form
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
<?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function cpm_list_dropdown_widget_init()
{
    register_widget('CPM_List_Dropdown_Widget');
}
