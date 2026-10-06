<?php
// Runs on every Playground start: sets the site title, creates the pages, and adds the four worksheets with their PDFs.
require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

update_option( 'blogname', 'Printable Worksheets' );
update_option( 'blogdescription', 'Simple, printable worksheets' );
wp_delete_post( 2, true );

$pages = array(
	'Worksheets' => 'Worksheets coming soon.',
	'About'      => 'This site is a hand-coded WordPress block theme, built from scratch with a custom template for each page and no page builder. It is a working example of my WordPress development, and the worksheets are the content. The theme code is public on GitHub.',
	'Contact'    => 'Want to work together or have a question about the code? Reach out through my GitHub profile: <a href="https://github.com/aaronwsmith12">github.com/aaronwsmith12</a>',
);

foreach ( $pages as $title => $content ) {
	wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $content,
	) );
}

$worksheets = array(
	'Addition Practice'     => 'addition-practice.pdf',
	'Multiplication Facts'  => 'multiplication-facts.pdf',
	'Sight Word Practice'   => 'sight-word-practice.pdf',
	'Number Patterns'       => 'number-patterns.pdf',
);

foreach ( $worksheets as $title => $file ) {
	$upload = wp_upload_bits( $file, null, file_get_contents( __DIR__ . '/worksheets/' . $file ) );

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'application/pdf',
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $upload['file'] );

	$url     = wp_get_attachment_url( $attachment_id );
	$content = '<!-- wp:file {"id":' . $attachment_id . ',"href":"' . $url . '"} -->'
		. '<div class="wp-block-file"><a href="' . $url . '">' . $title . '</a>'
		. '<a href="' . $url . '" class="wp-block-file__button wp-element-button" download>Download</a></div>'
		. '<!-- /wp:file -->';

	wp_insert_post( array(
		'post_type'    => 'worksheet',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $content,
	) );
}