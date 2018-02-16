<?php 
# Register Custom Post
function wp_tutorials_post() {
    register_post_type( 'portfolio',
        array(
            'labels' => array(
                'name' => __( 'Portfolio' ),
                'singular_name' => __( 'Portfolio' ),
                'add_new' => __( 'Add New' ),
                'add_new_item' => __( 'Add New Portfolio' ),
                'edit_item' => __( 'Edit Portfolio' ),
                'new_item' => __( 'New Portfolio' ),
                'view_item' => __('portfolio'),
                'not_found' => __( 'Sorry, we couldnt find the Portfolio you are looking for.' )
            ),
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'hierarchical' => false, 
        'capability_type' => 'page',
        'rewrite' => array( 'slug' => 'portfolio' ),
        'supports' => array( 'title', 'editor', 'custom-fields','thumbnail')
        )
    );
}
add_action( 'init', 'wp_tutorials_post' );

# Register Custom Taxonomy For Portfolio
 
function wp_tutorials_post_taxonomy() {
    register_taxonomy(
        'portfolio_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'portfolio',                  //post type name
        array(
            'hierarchical'          => true,
            'label'                         => 'Portfolio Category',  //Display name
            'query_var'             => true,
            'show_admin_column'             => true,
            'rewrite'                       => array(
                'slug'                  => 'portfolio-category', // This controls the base slug that will display before each term
                'with_front'    => true // Don't display the category base before
                )
            )
    );
}
add_action( 'init', 'wp_tutorials_post_taxonomy');


# Dynamic Portfolio With Shortcode
 
function portfolio_shortcode($atts){
    extract( shortcode_atts( array(
        'category' => ''
    ), $atts, '' ) );
     
    $q = new WP_Query(
        array('posts_per_page' => 50, 'post_type' => 'portfolios')
        );        
         
 
//Portfolio taxanomy query
    global $paged;
    global $post;
    $args = array(    
        'post_type' => 'portfolios',
        'paged' => $paged,
        'posts_per_page' => -1,
    );
 
    $portfolio = new WP_Query($args);
    if(is_array($portfolio->posts) && !empty($portfolio->posts)) {
        foreach($portfolio->posts as $gallery_post) {
            $post_taxs = wp_get_post_terms($gallery_post->ID, 'portfolio_category', array("fields" => "all"));
            if(is_array($post_taxs) && !empty($post_taxs)) {
                foreach($post_taxs as $post_tax) {
                    $portfolio_taxs[$post_tax->slug] = $post_tax->name;
                }
            }
        }
    }
?>        
 
        <!--Category Filter-->
         
        <div class="portfolio_button_area fix">
            <button class="filter portfolio_button active" data-filter="all">Show All</button>
            <?php foreach($portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name): ?>
                <button class="filter portfolio_button" data-filter=".<?php echo $portfolio_tax_slug; ?>"><?php echo $portfolio_tax_name; ?></button>
            <?php endforeach; ?>
        </div>        
        <!--End-->
 
         
 
<?php
 
    $list = '<div id="Container">';
    while($q->have_posts()) : $q->the_post();
        $idd = get_the_ID();
        //Get Texanmy class        
        $item_classes = '';
        $item_cats = get_the_terms($post->ID, 'portfolio_category');
        if($item_cats):
        foreach($item_cats as $item_cat) {
            $item_classes .= $item_cat->slug . ' ';
        }
        endif;
             
        $single_link = 
        $list .= '    
 
                <div class="mix '.$item_classes.'" >'.get_the_content().'</div>        
        ';        
    endwhile;
    $list.= '</div>';
    wp_reset_query();
    return $list;
}
add_shortcode('portfolio', 'portfolio_shortcode');

