<?php
/**
 * Year rail block. Hidden when there is only one year.
 *
 * @package Peak
 */

use function Peak\Timeline\{ current_context, items, years };

$peak_years = years( items( current_context() ) );
if ( count( $peak_years ) < 2 ) {
	return;
}
?>
<nav <?php echo get_block_wrapper_attributes( [ 'class' => 'peak-year-rail', 'aria-label' => esc_attr__( 'Jump to year', 'peak' ) ] ); ?>>
	<ol>
		<?php foreach ( $peak_years as $peak_year ) : ?>
			<li><a href="#y<?php echo (int) $peak_year; ?>" data-year="<?php echo (int) $peak_year; ?>"><?php echo (int) $peak_year; ?></a></li>
		<?php endforeach; ?>
	</ol>
</nav>
