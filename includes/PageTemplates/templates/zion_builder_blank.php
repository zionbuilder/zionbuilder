<?php

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<link rel="profile" href="http://gmpg.org/xfn/11"/>

		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php
			/**
			 * Print page template
			 */
			ZionBuilder\Plugin::$instance->page_templates->render_content();
		?>

		<?php wp_footer(); ?>
	</body>
</html>
