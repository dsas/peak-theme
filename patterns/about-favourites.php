<?php
/**
 * Title: About: favourites
 * Slug: peak/about-favourites
 * Categories: peak, about
 * Description: Short lists of favourite books, rides, places, albums and games.
 */

$peak_favourites = [
	'Books'  => 'Add a book',
	'Rides'  => 'Add a ride',
	'Places' => 'Add a place',
	'Albums' => 'Add an album',
	'Games'  => 'Add a game',
];
?>
<!-- wp:heading -->
<h2 class="wp-block-heading">Favourites</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"peak-favourites","layout":{"type":"default"}} -->
<div class="wp-block-group peak-favourites">
<?php foreach ( $peak_favourites as $peak_category => $peak_placeholder ) : ?>
<!-- wp:group {"className":"peak-favourite","layout":{"type":"default"}} -->
<div class="wp-block-group peak-favourite">
<!-- wp:heading {"level":3,"className":"peak-section-label"} -->
<h3 class="wp-block-heading peak-section-label"><?php echo esc_html( $peak_category ); ?></h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"peak-favourite__list"} -->
<ul class="wp-block-list peak-favourite__list"><?php for ( $peak_i = 0; $peak_i < 5; $peak_i++ ) : ?><!-- wp:list-item -->
<li><?php echo esc_html( $peak_placeholder ); ?></li>
<!-- /wp:list-item --><?php endfor; ?></ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
