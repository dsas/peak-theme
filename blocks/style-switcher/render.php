<?php
/**
 * Style switcher block. A toggle for two colourways, a select for more. Hidden until JS runs.
 *
 * @package Peak
 */

$peak_list = \Peak\Variations\load();
if ( count( $peak_list ) < 2 ) {
	return;
}
$peak_data = array_map( fn( $v ) => [ 'slug' => $v['slug'], 'title' => $v['title'], 'scheme' => $v['scheme'] ], $peak_list );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'peak-switcher' ] ); ?> hidden data-variations="<?php echo esc_attr( wp_json_encode( $peak_data ) ); ?>" data-label="<?php /* translators: %s: colourway name. */ echo esc_attr__( 'Switch to %s', 'peak' ); ?>">
	<?php if ( 2 === count( $peak_list ) ) : ?>
		<button type="button" class="peak-switcher__toggle">
			<svg class="peak-switcher__moon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z" fill="currentColor"/></svg>
			<svg class="peak-switcher__sun" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.5" fill="currentColor"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
		</button>
	<?php else : ?>
		<label class="screen-reader-text" for="peak-style-select"><?php esc_html_e( 'Colour scheme', 'peak' ); ?></label>
		<select id="peak-style-select" class="peak-switcher__select">
			<?php foreach ( $peak_list as $peak_v ) : ?>
				<option value="<?php echo esc_attr( $peak_v['slug'] ); ?>"><?php echo esc_html( $peak_v['title'] ); ?></option>
			<?php endforeach; ?>
		</select>
	<?php endif; ?>
</div>
