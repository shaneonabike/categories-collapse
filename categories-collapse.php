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

  // Widget admin form
?>
  <p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  <select id="<?php echo $this->get_field_id('post_type'); ?>"  name="<?php echo $this->get_field_name('post_type'); ?>">
			<?php for($x=1;$x<=10;$x++): ?>
			<option <?php echo $x == $post_type ? 'selected="selected"' : '';?> value="<?php echo $x;?>"><?php echo $x; ?></option>
			<?php endfor;?>
		</select>
  </p>
<?php 
  }
     
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
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
 
      	    <?php echo $term->name; ?>
 
            <?php
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
 
            if( $posts->have_posts() ): while( $posts->have_posts() ) : $posts->the_post(); ?>
 
                    <?php if(has_post_thumbnail()) { ?>
                            <?php the_post_thumbnail(); ?>
                    <?php }
                    /* no post image so show a default img */
                    else { ?>
                           <img src="<?php bloginfo('template_url'); ?>/assets/img/default-img.png" alt="<?php echo get_the_title(); ?>" title="<?php echo get_the_title(); ?>" width="110" height="110" />
                    <?php } ?>
 
                   <?php  echo get_the_title(); ?>
 
                        <?php the_excerpt(); ?>
                   
 
            <?php endwhile; endif; ?>
 
          <?php endforeach;
 
       endforeach; ?>
}
