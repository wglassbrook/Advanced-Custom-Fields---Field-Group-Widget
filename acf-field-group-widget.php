<?php
/**
 * Plugin Name: ACF Field Group Widget
 * Description: A widget that displays content from a field group. Used with Advanced Custom Fields.
 * Version: 0.2
 * Author: Wayne Glassbrook
 * Author URI: http://www.designly.net
 */

class ACF_Field_Group_Widget extends WP_Widget {

    /* Setup the basic options of the widget*/
    function ACF_Field_Group_Widget() {
        $widget_ops = array('classname' => 'acf_field_group_widget', 'description' => 'Displays contents of an ACF Field Group' );
        parent::WP_Widget('ACF_Field_Group_Widget', 'ACF Field Group Widget', $widget_ops);
    }

    /* Instantiate our default variables for the widget */
    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'group-id' => '' ) );
        $groupID = esc_attr($instance['group-id']);

        /* This feels a bit gooey, but we dive into the DB to grab the name(post_title) and slug(post_name) based on the ID of the field we will be selecting */
        global $wpdb;
        $groupName = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID =  '$groupID' ");
        $groupSlug = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE ID =  '$groupID' ");
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('group-id'); ?>"><?php _e('Field Group: '); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('group-id'); ?>" name="<?php echo $this->get_field_name('group-id'); ?>">
                <option value="">
                <?php echo esc_attr( __( '-Select Field Group-' ) ); ?></option>
                <?php
                /* Get all of the available ACF Field Groups */
                $field_groups = get_pages(array (
                    'post_type'    => 'acf',
                ));
                /* List the field groups in our select options */
                foreach ( $field_groups as $field_group ) {
                    $field_group_ID = $field_group->ID;
                    $field_group_title = $field_group->post_title;
                    echo '<option value="' . $field_group_ID . '" id="' . $field_group_ID . '"', $groupID == $field_group_ID ? ' selected="selected"' : '', '>', $field_group_title, '</option>';
                }
                ?>

            </select>
        </p>
        <?php

        /* A little debug info. After a Field group is selected from the select box, displays the Name, Slug, and ID of the selected Field Group in the widget options. May be helpful when writing the template. */
        if($groupID){
            echo '<p><strong>Name: </strong>' . $groupName . '<br/><strong>Slug: </strong>' . $groupSlug . '<br/><strong>ID: </strong>' . $groupID . '</p>';
        }
    }

    /* Update the widget variables */
    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['group-id'] = strip_tags($new_instance['group-id']);
        return $instance;
    }

    /* Display the widget content */
    function widget($args, $instance) {
        extract($args, EXTR_SKIP);

        $groupID = $instance['group-id'];

        /* Instantiating these variables again?! I don't think this is how it's supposed to be done. Perhaps I can call the GLOBAL $var? Not an experienced PHP coder over here. */
        global $wpdb;
        $groupName = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID =  '$groupID' ");
        $groupSlug = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE ID =  '$groupID' ");

        echo $before_widget;

        /* Thought this might be helpful to some people. Since I have no idea what kind of field data users are going to be using, the thought was to call for a template from the current theme. Since there isnt already a ACF Widget Template file in the theme, why not make a home for it and build a placeholder template file? Perhaps this should be written to the plugin directory? I usually create ACF fields on a theme by theme basis, this may be the best option. I'm open to suggestion though */

        $temp_dir = TEMPLATEPATH . '/acf-widgets/'; //Instantiate the template directory
        $temp_file = TEMPLATEPATH . '/acf-widgets/' . $groupSlug . '-template.php'; //Instantiate the actual template file
        $temp_uri = get_template_directory_uri() . '/acf-widgets/' . $groupSlug . '-template.php'; //Instantiate the URI for the template file


        if($groupID){

            /* Look to see if there is already an '/acf-widgets/' directory in the theme. If not, we create one with 775 permissions so it can be read and writen to. */
            if(!is_dir($temp_dir)){
                $old_umask = umask(0);
                $mkdir = mkdir($temp_dir, 0775, true);
                umask($old_umask);
            }

            /* Look to see if we already have a ACF Widget Template file. */
            if (file_exists($temp_file)) {

                /* If we do, then we return its contents. */
                include($temp_file);

            } else {

                /* If not, we create one in our '/acf-widgets/' directory. Pre-populate it with some instructive text. Also set with 775 permissions. */
                $fh = fopen($temp_file, 'a+');
                fwrite($fh, '<p><strong>Template Empty!</strong><br /> Create an ACF Widget Template at ' . $temp_uri . '</p><p><strong>Name: </strong>' . $groupName . '<br/><strong>Slug: </strong>' . $groupSlug . '<br/><strong>ID: </strong>' . $groupID . '</p>' . "\n");
                fclose($fh);
                chmod($temp_file, 0775);
                include($temp_file);

            }
        }

        echo $after_widget;
    }
}


add_action( 'widgets_init', create_function('', 'return register_widget("ACF_Field_Group_Widget");') );
?>
