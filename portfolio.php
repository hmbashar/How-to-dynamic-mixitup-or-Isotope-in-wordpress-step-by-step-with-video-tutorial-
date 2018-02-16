<?php 
 
 
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
 
 
 
 
?>
