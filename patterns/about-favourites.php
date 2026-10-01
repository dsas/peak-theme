<?php
/**
 * Title: About: favourites
 * Slug: peak/about-favourites
 * Categories: peak, about
 * Description: A small grid of favourites.
 */

$peak_favourites = [
	[ 'Book', 'Add a favourite book', 'One line on why.' ],
	[ 'Ride', 'Add a favourite route', 'One line on why.' ],
	[ 'Place', 'Add a favourite place', 'One line on why.' ],
	[ 'Album', 'Add a favourite album', 'One line on why.' ],
];
?>
<!-- wp:heading -->
<h2 class="wp-block-heading">Favourites</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"peak-favourites","layout":{"type":"grid","minimumColumnWidth":"12rem"}} -->
<div class="wp-block-group peak-favourites">
<?php foreach ( $peak_favourites as $peak_fav ) : ?>
<!-- wp:group {"className":"peak-favourite","layout":{"type":"default"}} -->
<div class="wp-block-group peak-favourite">
<!-- wp:paragraph {"className":"peak-section-label"} -->
<p class="peak-section-label"><?php echo esc_html( $peak_fav[0] ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $peak_fav[1] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo esc_html( $peak_fav[2] ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
