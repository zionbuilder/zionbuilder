<?php

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

get_header();

	ZionBuilder\Plugin::$instance->page_templates->render_content();

get_footer();
