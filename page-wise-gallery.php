<?php 
/*
Plugin Name: Page Wise Gallery
Plugin URI: http://greenlemon.in
Description: Provides unique gallery for each page.
Author: Anupa
Version: 1.0
Author URI: http://greenlemon.in/
*/

/**
 * Provides unique gallery for each page.
 */
add_action('admin_notices', 'pwg_admin_notices');
function pwg_admin_notices() {

if ($notices= get_option('pwg_deferred_admin_notices')) {
    foreach ($notices as $notice) {
      echo "<div class='updated'><p>$notice</p></div>";
    }
    delete_option('pwg_deferred_admin_notices');
  }
}

add_action('admin_notices', 'pwg_admin_notice');
function pwg_admin_notice() {
 $plugins = get_option('active_plugins');
 $required_plugin = 'easy-fancybox/easy-fancybox.php';
 $debug_queries_on = FALSE;
  if ( !in_array( $required_plugin , $plugins ) ) {
  $debug_queries_on = TRUE; 
  $notices= get_option('pwg_deferred_admin_notices', array());
  $notices[]= "Page Wise Gallery: Please insatll the <a href='https://wordpress.org/plugins/easy-fancybox/'>Fancybox plugin</a> to get the popup option for your gallery";
  update_option('pwg_deferred_admin_notices', $notices);
 }
}

function my_plugin_init() {
  load_plugin_textdomain( 'page-wise-gallery', false, 'page-wise-gallery/languages' );
}
add_action('init', 'my_plugin_init');

 function pwg_showgal(){
 $s=get_the_ID();
 $args = array(
   'post_type' => 'attachment',
   'numberposts' => -1,
   'post_status' => null,
   'post_parent' => $s
  );
$attachments = get_posts( $args );
      if ( $attachments ) {
      echo "<ul class='pwg_list'>";
         foreach ( $attachments as $attachment ) {
               $thumb=wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
              $full=wp_get_attachment_image_src( $attachment->ID, 'full' );
            echo  "<li><a class='fancybox' href='$full[0]' data-fancybox-group='gallery'><img src='$thumb[0]'/></a></li>";
           }echo "</ul>";
      }
 } 
 add_shortcode( 'pwg_gallery', 'pwg_showgal' );
/*----------------------widget--------------------*/   
 class pwg_gallery_plugin extends WP_Widget {
	    // constructor
	    function pwg_gallery_plugin() {

	               parent::WP_Widget(false, $name = __('PWG Gallery', 'page-wise-gallery') );

	    }
	    // widget form creation

 	  function form($instance) { 
	   if( $instance) 
	   {
		$title = esc_attr($instance['title']); 
		$width = esc_attr($instance['width']); 
		$height = esc_attr($instance['height']); 
	   }
	   else
	   {$title='';$width=100; $height=100;}
	   ?>
	   <p>
	   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Gallery Title', 'page-wise-gallery'); ?></label>
	   <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	   
	   </p>	
	   <p>
	   <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Thumbnail width', 'page-wise-gallery'); ?></label>
	    <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
	   </p> 
	   <p>
	   <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Thumbnail height', 'page-wise-gallery'); ?></label>
	    <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
	   </p>   		   
	  <?php	    }
	 
	    // widget update

 	    function update($new_instance, $old_instance) {

	        $instance = $old_instance;
	        $instance['title'] = strip_tags($new_instance['title']);
	        $instance['width'] = strip_tags($new_instance['width']);
	        $instance['height'] = strip_tags($new_instance['height']);
	        return $instance;

	    }
	
	    // widget display

	    function widget($args, $instance) {

	       extract( $args );
	       $title = apply_filters('widget_title', $instance['title']);
	       $width = apply_filters('thumb_width', $instance['width']);
	       $height = apply_filters('thumb_height', $instance['height']);
	       
	       echo $before_widget;
	       if ( $title ) {
	       echo $before_title . $title . $after_title;
	       }
	       //echo do_shortcode('[pwg_gallery]');
	        $s=get_the_ID();
 $args = array(
   'post_type' => 'attachment',
   'numberposts' => -1,
   'post_status' => null,
   'post_parent' => $s
  );
$attachments = get_posts( $args );
      if ( $attachments ) {
      echo "<ul class='pwg_list'>";
         foreach ( $attachments as $attachment ) {
               $thumb=wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
              $full=wp_get_attachment_image_src( $attachment->ID, 'full' );
            echo  "<li><a class='fancybox' href='$full[0]' data-fancybox-group='gallery'><img src='$thumb[0]' width=$width height=$height/></a></li>";
           }echo "</ul>";
      }
	       echo $after_widget;

	    }
	}
	 
// register widget
	add_action('widgets_init', create_function('', 'return register_widget("pwg_gallery_plugin");'));
/*----------------------scripts--------------------*/ 
function pwg_gallery_scripts() {
	wp_enqueue_style( 'style-name1', plugin_dir_url( __FILE__ ) . 'css/pwg.css' );

}
add_action( 'wp_enqueue_scripts', 'pwg_gallery_scripts' );
 ?>
