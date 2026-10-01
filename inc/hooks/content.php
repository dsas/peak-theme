<?php
/**
 * Content block styles.
 *
 * @package Peak
 */

namespace Peak\Content;

add_action(
	'init',
	fn() => register_block_style( 'core/group', [ 'name' => 'boxout', 'label' => __( 'Boxout', 'peak' ) ] )
);
