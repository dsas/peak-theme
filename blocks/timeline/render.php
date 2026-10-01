<?php
/**
 * Writing timeline block.
 *
 * @package Peak
 */

use function Peak\Timeline\{ current_context, items, group_by_year, years, adjacent_years };

$peak_limit = (int) ( $attributes['limit'] ?? 0 );
$peak_ctx   = current_context();
if ( $peak_limit ) {
	$peak_ctx['limit'] = $peak_limit;
}
$peak_heading = '';
if ( ! empty( $attributes['related'] ) ) {
	$peak_post_id    = (int) ( $block->context['postId'] ?? get_the_ID() );
	$peak_categories = get_the_category( $peak_post_id );
	$peak_category   = $peak_categories ? $peak_categories[0] : null;
	$peak_ctx        = [
		'limit'              => $peak_limit ?: 3,
		'related_to'         => $peak_post_id,
		'related_categories' => $peak_category ? [ $peak_category->term_id ] : [],
	];
	// The heading names the category the list is drawn from.
	$peak_heading = $peak_category
		? sprintf(
			/* translators: %s: linked category name. */
			esc_html__( 'More from %s', 'peak' ),
			'<a href="' . esc_url( get_category_link( $peak_category ) ) . '">' . esc_html( $peak_category->name ) . '</a>'
		)
		: esc_html__( 'More writing', 'peak' );
}

$peak_items = items( $peak_ctx );
if ( ! $peak_items ) {
	return;
}
$peak_variant = 'list' === ( $attributes['variant'] ?? '' ) ? 'list' : 'timeline';
$peak_excerpt = ! empty( $attributes['showExcerpt'] );
$peak_thumbs  = ! empty( $attributes['showThumbnails'] );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'peak-timeline is-variant-' . $peak_variant ] ); ?>>
<?php if ( $peak_heading ) : ?>
	<h2 class="peak-section-label"><?php echo $peak_heading; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></h2>
<?php endif; ?>
<?php if ( 'list' === $peak_variant ) : ?>
	<ul class="peak-timeline__list">
		<?php foreach ( $peak_items as $peak_item ) : ?>
			<li>
				<time datetime="<?php echo esc_attr( $peak_item['date'] ); ?>"><?php echo esc_html( mysql2date( 'j M Y', $peak_item['date'] ) ); ?></time>
				<a href="<?php echo esc_url( $peak_item['url'] ); ?>"><?php echo esc_html( $peak_item['title'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( ! empty( $attributes['showAllLink'] ) ) : ?>
		<p class="peak-timeline__all"><a href="<?php echo esc_url( \Peak\Site\writing_url() ); ?>"><?php esc_html_e( 'All writing →', 'peak' ); ?></a></p>
	<?php endif; ?>
<?php else : ?>
	<?php foreach ( group_by_year( $peak_items ) as $peak_year => $peak_group ) : ?>
		<section class="peak-timeline__year" id="y<?php echo (int) $peak_year; ?>" aria-labelledby="y<?php echo (int) $peak_year; ?>-heading">
			<h2 class="peak-timeline__year-heading" id="y<?php echo (int) $peak_year; ?>-heading"><?php echo (int) $peak_year; ?></h2>
			<ol class="peak-timeline__items">
				<?php foreach ( $peak_group as $peak_item ) : ?>
					<li class="peak-timeline__item<?php echo $peak_thumbs && $peak_item['thumbnail'] ? ' has-thumbnail' : ''; ?>">
						<p class="peak-timeline__meta">
							<time datetime="<?php echo esc_attr( $peak_item['date'] ); ?>"><?php echo esc_html( mysql2date( 'j M', $peak_item['date'] ) ); ?></time>
							<?php if ( $peak_item['category'] && empty( $peak_ctx['category'] ) ) : // Not repeated on its own category page. ?>
								· <a class="peak-timeline__cat" href="<?php echo esc_url( $peak_item['category_url'] ); ?>"><?php echo esc_html( $peak_item['category'] ); ?></a>
							<?php endif; ?>
						</p>
						<a class="peak-timeline__title" href="<?php echo esc_url( $peak_item['url'] ); ?>"><?php echo esc_html( $peak_item['title'] ); ?></a>
						<?php if ( $peak_excerpt && $peak_item['excerpt'] ) : ?>
							<p class="peak-timeline__excerpt"><?php echo esc_html( $peak_item['excerpt'] ); ?></p>
						<?php endif; ?>
						<?php if ( $peak_thumbs && $peak_item['thumbnail'] ) : ?>
							<?php // Repeats the title link, so it's kept out of the tab order and accessibility tree. ?>
							<a class="peak-timeline__thumb" href="<?php echo esc_url( $peak_item['url'] ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_get_attachment_image( $peak_item['thumbnail'], 'thumbnail', false, [ 'alt' => '', 'loading' => 'lazy' ] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endforeach; ?>
	<?php
	// Year archives: step to the nearest older and newer years that have writing.
	if ( ! empty( $peak_ctx['year'] ) && empty( $peak_ctx['monthnum'] ) ) :
		$peak_adjacent = adjacent_years( years( items( [] ) ), (int) $peak_ctx['year'] );
		if ( $peak_adjacent['older'] || $peak_adjacent['newer'] ) :
			?>
	<nav class="peak-year-nav" aria-label="<?php esc_attr_e( 'Other years', 'peak' ); ?>">
		<?php if ( $peak_adjacent['older'] ) : ?>
			<a class="peak-year-nav__older" href="<?php echo esc_url( get_year_link( $peak_adjacent['older'] ) ); ?>">← <?php echo (int) $peak_adjacent['older']; ?></a>
		<?php endif; ?>
		<?php if ( $peak_adjacent['newer'] ) : ?>
			<a class="peak-year-nav__newer" href="<?php echo esc_url( get_year_link( $peak_adjacent['newer'] ) ); ?>"><?php echo (int) $peak_adjacent['newer']; ?> →</a>
		<?php endif; ?>
	</nav>
			<?php
		endif;
	endif;
	?>
<?php endif; ?>
</div>
