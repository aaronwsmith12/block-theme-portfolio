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
	'Addition Practice'    => array(
		'file'    => 'addition-practice.pdf',
		'excerpt' => 'Printable addition problems for building speed and accuracy.',
	),
	'Multiplication Facts' => array(
		'file'    => 'multiplication-facts.pdf',
		'excerpt' => 'Practice multiplication facts with a printable worksheet.',
	),
	'Sight Word Practice'  => array(
		'file'    => 'sight-word-practice.pdf',
		'excerpt' => 'Printable practice for reading common sight words.',
	),
	'Number Patterns'      => array(
		'file'    => 'number-patterns.pdf',
		'excerpt' => 'Spot and continue number patterns with this printable worksheet.',
	),
);

foreach ( $worksheets as $title => $data ) {
	$file   = $data['file'];
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

	$post_id = wp_insert_post( array(
		'post_type'    => 'worksheet',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $content,
		'post_excerpt' => $data['excerpt'],
	) );

	$image_file   = str_replace( '.pdf', '.png', $file );
	$image_upload = wp_upload_bits( $image_file, null, file_get_contents( __DIR__ . '/worksheets/' . $image_file ) );

	$image_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/png',
		'post_title'     => $title . ' preview',
		'post_status'    => 'inherit',
	), $image_upload['file'], $post_id );

	wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $image_upload['file'] ) );
	set_post_thumbnail( $post_id, $image_id );
}

flush_rewrite_rules();