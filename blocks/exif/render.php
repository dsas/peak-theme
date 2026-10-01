<?php
/**
 * Photo details block.
 *
 * @package Peak
 */

$peak_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
$peak_thumb   = (int) get_post_thumbnail_id( $peak_post_id );
$peak_meta    = $peak_thumb ? ( wp_get_attachment_metadata( $peak_thumb ) ?: [] ) : [];
$peak_parts   = \Peak\Exif\format( $peak_meta['image_meta'] ?? [] );

if ( ! $peak_parts ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( [ 'class' => 'peak-exif' ] ); ?>>
	<?php foreach ( $peak_parts as $peak_part ) : ?>
		<li><?php echo esc_html( $peak_part ); ?></li>
	<?php endforeach; ?>
</ul>
