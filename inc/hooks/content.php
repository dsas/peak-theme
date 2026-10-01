<?php
/**
 * Content block styles.
 *
 * @package Peak
 */

namespace Peak\Content;

add_action(
	'init',
	function () {
		register_block_style( 'core/group', [ 'name' => 'boxout', 'label' => __( 'Boxout', 'peak' ) ] );
		register_block_style( 'core/list', [ 'name' => 'timeline', 'label' => __( 'Timeline', 'peak' ) ] );
	}
);
