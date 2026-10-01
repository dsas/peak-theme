<?php
/**
 * Title: Not found
 * Slug: peak/not-found
 * Inserter: no
 */

$peak_writing = \Peak\Site\writing_url();
$peak_photos  = \Peak\Site\photos_url();
?>
<!-- wp:group {"className":"peak-404__top","layout":{"type":"default"}} -->
<div class="wp-block-group peak-404__top">
<!-- wp:group {"className":"peak-404__message","layout":{"type":"default"}} -->
<div class="wp-block-group peak-404__message">
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Lost on the moors?</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>There’s nothing at this address. Follow the signpost, or try a search.</p>
<!-- /wp:paragraph -->
<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search"} /-->
</div>
<!-- /wp:group -->
<!-- wp:html -->
<nav class="peak-fingerpost" aria-label="<?php esc_attr_e( 'Where to next', 'peak' ); ?>">
	<a class="peak-fingerpost__arm is-right" href="<?php echo esc_url( $peak_writing ); ?>"><?php esc_html_e( 'Writing', 'peak' ); ?></a>
	<a class="peak-fingerpost__arm is-left" href="<?php echo esc_url( $peak_photos ); ?>"><?php esc_html_e( 'Photos', 'peak' ); ?></a>
</nav>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"peak-404__recent","layout":{"type":"default"}} -->
<div class="wp-block-group peak-404__recent">
<!-- wp:heading {"level":2,"className":"peak-section-label"} -->
<h2 class="wp-block-heading peak-section-label">Recent writing</h2>
<!-- /wp:heading -->
<!-- wp:peak/timeline {"variant":"list","limit":3,"showAllLink":true} /-->
</div>
<!-- /wp:group -->
