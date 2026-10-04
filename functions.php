<?php
// Load the theme's style.css on the front end and in the editor.
function block_theme_portfolio_styles() {
	wp_enqueue_style(
		'block-theme-portfolio-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'block_theme_portfolio_styles' );
add_action( 'enqueue_block_editor_assets', 'block_theme_portfolio_styles' );

// Make our stylesheet link host-relative so it works on any address (e.g. Codespaces).
function block_theme_portfolio_relative_style( $src, $handle ) {
	if ( 'block-theme-portfolio-style' === $handle ) {
		return wp_make_link_relative( $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'block_theme_portfolio_relative_style', 10, 2 );