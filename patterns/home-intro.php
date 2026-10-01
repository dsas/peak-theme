<?php
/**
 * Title: Home intro
 * Slug: peak/home-intro
 * Categories: peak
 * Description: Photo and short intro for the homepage valley.
 */
?>
<!-- wp:group {"className":"peak-home-intro","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group peak-home-intro">
<!-- wp:image {"width":"96px","height":"96px","scale":"cover","sizeSlug":"thumbnail","className":"is-style-rounded"} -->
<figure class="wp-block-image size-thumbnail is-resized is-style-rounded"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/portrait-placeholder.svg' ) ); ?>" alt="" style="object-fit:cover;width:96px;height:96px"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph -->
<p>Software engineer at Automattic, living in Chesterfield on the edge of the Peak District. Father, husband, cyclist, reader and a geek. <a href="/about/">More about me →</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
