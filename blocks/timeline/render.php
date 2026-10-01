<?php
/**
 * Writing timeline block.
 *
 * @package Peak
 */

use function Peak\Timeline\{ current_context, items, group_by_year };

$peak_limit = (int) ( $attributes['limit'] ?? 0 );
$peak_ctx   = current_context();
if ( $peak_limit ) {
	$peak_ctx['limit'] = $peak_limit;
}
if ( ! empty( $attributes['related'] ) ) {
	$peak_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
	$peak_ctx     = [
		'limit'              => $peak_limit ?: 3,
		'related_to'         => $peak_post_id,
		'related_categories' => wp_get_post_categories( $peak_post_id ),
	];
}

$peak_items = items( $peak_ctx );
if ( ! $peak_items ) {
	return;
}
$peak_variant = 'list' === ( $attributes['variant'] ?? '' ) ? 'list' : 'timeline';
$peak_excerpt = ! empty( $attributes['showExcerpt'] );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'peak-timeline is-variant-' . $peak_variant ] ); ?>>
<?php if ( 'list' === $peak_variant ) : ?>
	<ul class="peak-timeline__list">
		<?php foreach ( $peak_items as $peak_item ) : ?>
			<li>
				<time datetime="<?php echo esc_attr( $peak_item['date'] ); ?>"><?php echo esc_html( mysql2date( 'j M Y', $peak_item['date'] ) ); ?></time>
				<a href="<?php echo esc_url( $peak_item['url'] ); ?>"><?php echo esc_html( $peak_item['title'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<?php foreach ( group_by_year( $peak_items ) as $peak_year => $peak_group ) : ?>
		<section class="peak-timeline__year" id="y<?php echo (int) $peak_year; ?>" aria-labelledby="y<?php echo (int) $peak_year; ?>-heading">
			<h2 class="peak-timeline__year-heading" id="y<?php echo (int) $peak_year; ?>-heading"><?php echo (int) $peak_year; ?></h2>
			<ol class="peak-timeline__items">
				<?php foreach ( $peak_group as $peak_item ) : ?>
					<li class="peak-timeline__item">
						<p class="peak-timeline__meta">
							<time datetime="<?php echo esc_attr( $peak_item['date'] ); ?>"><?php echo esc_html( mysql2date( 'j M', $peak_item['date'] ) ); ?></time>
							<?php if ( $peak_item['category'] ) : ?>
								· <a class="peak-timeline__cat" href="<?php echo esc_url( $peak_item['category_url'] ); ?>"><?php echo esc_html( $peak_item['category'] ); ?></a>
							<?php endif; ?>
						</p>
						<a class="peak-timeline__title" href="<?php echo esc_url( $peak_item['url'] ); ?>"><?php echo esc_html( $peak_item['title'] ); ?></a>
						<?php if ( $peak_excerpt && $peak_item['excerpt'] ) : ?>
							<p class="peak-timeline__excerpt"><?php echo esc_html( $peak_item['excerpt'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endforeach; ?>
<?php endif; ?>
</div>
