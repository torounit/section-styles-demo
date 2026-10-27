<?php
add_action( 'enqueue_block_assets', function () {
	wp_enqueue_style(
		'theme-style',
		get_parent_theme_file_uri( 'style.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
} );
