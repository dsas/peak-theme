<?php
/**
 * Title: Not found
 * Slug: peak/not-found
 * Inserter: no
 */

$peak_writing = \Peak\Site\writing_url();
$peak_photos  = \Peak\Site\photos_url();
?>
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Lost on the moors?</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>There’s nothing at this address. Try a search, or head back to <a href="<?php echo esc_url( $peak_writing ); ?>">Writing</a> or <a href="<?php echo esc_url( $peak_photos ); ?>">Photos</a>.</p>
<!-- /wp:paragraph -->
<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search"} /-->
