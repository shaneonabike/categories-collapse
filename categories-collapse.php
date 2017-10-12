<?php
   /*
   Plugin Name: Categories Collapse
   Plugin URI: https://beesonabike.com
   Description: A Collapsible Categories Widget integrating into X Theme
   Version: 1.0
   Author: Shane Bill
   Author URI: https://beesonabike.com
   License: GPL2
   */


  // Register JS
  function categories_collapse_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('categories-collapse', plugins_url('/js/categories-collapse.js', __FILE__) );
  }
  add_action('wp_enqueue_scripts', 'categories_collapse_scripts');


  // Register and load the widget
  function categories_collapse_load_widget() {
    register_widget( 'categories_collapse_widget' );
  }
  add_action( 'widgets_init', 'categories_collapse_load_widget' );
 
  // Widget Information 
  class categories_collapse_widget extends WP_Widget {
 
	function __construct() {
	    parent::__construct(
 
           // Base ID of your widget
	  'categories_collapse_widget', 
 
	  // Widget name will appear in UI
	  __('Collapsible Categories', 'categories_collapse_widget_domain'), 
 
	  // Widget description
	  array( 'description' => __( 'A Collapsible Categories Widget Integrated into X Theme', 'categories_collapse_widget_domain' ), ) 
	);
  }
 
  // Widget Front-end 
  public function widget( $args, $instance ) {
	$title = apply_filters( 'widget_title', $instance['title'] );
	$post_type = $instance['post_type'];
 
	// Any special widget arguments setup by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
 
	// This is where you run the code and display the output
	categories_collapse_posts_by_taxonomy($instance['post_type']);
	echo $args['after_widget'];
   }
         
  // Widget Backend 
  public function form( $instance ) {
      if ( isset( $instance[ 'title' ] ) ) { 
	   $title = $instance[ 'title' ];
       } else {
	  $title = __( 'Collapsible Categories', 'categories_collapse_widget_domain' );
       }
       $curr_post_type = '';
      if (isset($instance['post_type'] ) ) {
	$curr_post_type = $instance['post_type'];
      }

      // Grab all the post types
      $post_types= get_post_types(array('publicly_queryable'=>TRUE), 'objects');

      // Widget admin form
?>
  <p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); echo $curr_post_type; ?></label> 
  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  <select id="<?php echo $this->get_field_id('post_type'); ?>"  name="<?php echo $this->get_field_name('post_type'); ?>">
  <?php foreach ( $post_types as $post_type ) : ?>
	<option <?php echo $post_type->name == $curr_post_type ? 'selected="selected"' : '';?> value="<?php echo $post_type->name; ?>"><?php echo $post_type->name; ?></option>
   <?php endforeach; ?>
   </select>
  </p>

<?php 
  }
     
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
	$instance = array();

	// Update title
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

	// Update category selected
	$instance['post_type'] = $new_instance['post_type'];
	return $instance;
  }

} // Class categories_collapse_widget ends here

/*
 * Loop through Categories and Display Posts within
 */
function categories_collapse_posts_by_taxonomy($post_type = 'posts') {
 
	// Get all the taxonomies for this post type
	$taxonomies = get_object_taxonomies( array( 'post_type' => $post_type ) );

	// Lopo through the taxonomies 
	foreach( $taxonomies as $taxonomy ) :
 
	// Gets every "category" (term) in this taxonomy to get the respective posts
 	$terms = get_terms( $taxonomy );
 
    	  foreach( $terms as $term ) : ?>
              <?php
	      // Output the posts associated to this term
      	      $args = array(
                'post_type' => $post_type,
                'posts_per_page' => -1,  //show all posts
                'tax_query' => array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $term->slug,
                    )
                )
 
               );
              $posts = new WP_Query($args);
	    ?>

      	    <div class="single-term-wrapper"><fieldset class="open-widget collapsible">
              <legend><span class="fieldset-legend">
                <a class="fieldset-title" href="#"><?php echo $term->name; ?> (<?php echo $posts->found_posts;?>)</a>
              </span></legend>
              <div class="fieldset-wrapper collapse"> 

	    <?php
              if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post(); ?>
		   <div class="term-post-wrapper">
                     <h3 class="<?php echo $post_type; ?>-title"><?php  echo get_the_title(); ?></h3>
                     <div class="post-desc"><?php the_excerpt(); ?></div>
		     <?php if ($post_type == 'resource') : ?>
		       <div class="resource-description"><?php echo get_post_meta(get_the_ID(), 'resource_description', true); ?></div>
		       <div class="resource-link"><a href="<?php echo get_post_meta(get_the_ID(), 'resource_link', true); ?>"><?php _e('See website', 'categories-collapse-link-click'); ?></a></div>
		    <?php endif; ?>
                   </div>
              <?php endwhile; endif; ?>
	     </div></fieldset></div>
 
          <?php endforeach;

        endforeach;
}
