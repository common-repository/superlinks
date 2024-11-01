<?php
/*
Plugin Name: SuperLinks
Description: More configurable version of the default WP 2.5 'Links' widget
Author: Rusty Geldmacher
Version: 0.1
Author URI: http://www.geldmacher.net

Copyright 2008 Rusty Geldmacher

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function widget_superlinks_init() {

  // Define a few constants, to make things less error-prone
  define('SL_ID_BASE', 'superlinks');
  define('SL_OPTIONS_KEY', 'widget_superlinks');
  define('SL_OPTION_CATID', 'catid');

  // A logging function to use for debugging
  function log_to_file($message) {
    $fh = fopen('superlinks_log.txt', 'a') or die("can't open file");
    fwrite($fh, $message . "\n");
    fclose($fh);
  }

  // This is called at the bottom of widget_superlinks_init
  function widget_superlinks_register() {
    
    if (!$options = get_option(SL_OPTIONS_KEY)) {
      $options = array();
    }
    $widget_ops = array('classname' => 'widget_superlinks', 'description' => __('A better links widget'));
    $control_ops = array('id_base' => SL_ID_BASE);
    $name = __('SuperLinks');
    
    // First check to see if there are any defined SuperLinks widgets saved
    $id = false;
    foreach (array_keys($options) as $o) {
      // This shouldn't happen but we check for it anyway
      if (!isset($options[$o][SL_OPTION_CATID])) {
	continue;
      }
      $id = SL_ID_BASE . "-$o";
      wp_register_sidebar_widget($id, $name, 'widget_superlinks', $widget_ops, array('number' => $o));
      wp_register_widget_control($id, $name, 'widget_superlinks_control', $control_ops, array('number' => $o));
    }
    
    // If there are none, we register the widget's existance with a generic template
    if (!$id) {
      wp_register_sidebar_widget('superlinks-1', $name, 'widget_superlinks', $widget_ops, array('number' => -1));
      wp_register_widget_control('superlinks-1', $name, 'widget_superlinks_control', $control_ops, array('number' => -1));
    }    
  }

  function widget_superlinks($args, $widget_args = 1) {
    extract($args, EXTR_SKIP);
    
    if (is_numeric($widget_args))
      $widget_args = array('number' => $widget_args);
    $widget_args = wp_parse_args($widget_args, array('number' => -1));
    extract($widget_args, EXTR_SKIP);
    
    $options = get_option(SL_OPTIONS_KEY);
    if (!isset($options[$number]))
      return;

    $widget_options = $options[$number];
    $catid = $widget_options[SL_OPTION_CATID];
    if ($catid == 0) {
      unset($catid);
    }

    $before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
    wp_list_bookmarks(array('category' => $catid,
			    'title_before' => $before_title, 'title_after' => $after_title,
			    'category_before' => $before_widget, 'category_after' => $after_widget,
			    'show_images' => true, 'class' => 'linkcat widget'
			    ));
  }
  
  function widget_superlinks_control($args) {
    global $wp_registered_widgets;
    static $updated = false;
    
    if (is_numeric($args)) {
      $args = array('number' => $args);
    }
    $args = wp_parse_args($args, array('number' => -1));
    extract($args, EXTR_SKIP);
    
    $options = get_option(SL_OPTIONS_KEY);
    if (!is_array($options))
      $options = array();
    
    if (!$updated && !empty($_POST['sidebar'])) {
      
      // Get the sidebar widgets that are registered in this sidebar
      $sidebar = (string) $_POST['sidebar'];
      $sidebars_widgets = wp_get_sidebars_widgets();
      if (isset($sidebars_widgets[$sidebar]))
	$my_sidebar =& $sidebars_widgets[$sidebar];
      else
	$my_sidebar = array();
      
      // First go through and see if any SuperLinks widgets have been removed
      // We know one has been removed because there would be no form element
      // named superlinks-XXX where XXX is a numeric value that exists in the
      // superliks options hash.
      foreach ($my_sidebar as $_widget_id) {
	if ('widget_superlinks' == $wp_registered_widgets[$_widget_id]['callback'] &&
	    isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
	  $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
	  if (!in_array("superlinks-$widget_number", $_POST['widget-id']))
	    // The widget has been removed.
	    unset($options[$widget_number]);
	}
      }
      
      // Next go through the superlinks options that we got from the post.
      foreach ((array) $_POST['widget-superlinks'] as $widget_number => $widget_options) {
	// Save the options for this widget
	$catid = attribute_escape($widget_options[SL_OPTION_CATID]);
	$options[$widget_number][SL_OPTION_CATID] = $catid;
      }
      
      update_option(SL_OPTIONS_KEY, $options);

      // This will make sure that next time this function is called,
      // that we don't go through all the options again -- we only 
      // need to do that once
      $updated = true;
    }
    
    if ($number == -1) {
      // For the one to be newly created
      $number = '%i%';
      $catid = 0;
    } else {
      // For an existing SuperLinks widget
      $catid = $options[$number][SL_OPTION_CATID];
    }
    
    $catid_name = "widget-superlinks[$number][" . SL_OPTION_CATID . "]";
    $cats = get_terms('link_category');
?>
                <p>
			<label for="<?php echo $catid_name; ?>"><?php _e('Link Category:'); ?></label>
			<select class="widefat" id="<?php echo $catid_name; ?>" name="<?php echo $catid_name; ?>">
                          <option value="0" <?php selected($catid, 0); ?>>Show all categories</option>
	                  <?php foreach ((array) $cats as $cat) { ?>
			    <option value="<?php echo $cat->term_id; ?>" <?php selected($catid, $cat->term_id); ?>>
			      <?php echo apply_filters("link_category", $cat->name); ?>
			    </option>
			  <?php } ?>
			</select>
		</p>
<?php
  }

  // Firsrt remove the original Links widget, since we don;t need it anymore
  unregister_sidebar_widget('links');
  // Then register our new SuperLinks widget
  widget_superlinks_register();
}

// Run our code later in case it loads prior to any required plugins.
add_action('widgets_init', 'widget_superlinks_init');

?>
