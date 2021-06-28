<?php

namespace ZionBuilder;

use ZionBuilder\Whitelabel;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$body_classes = apply_filters( 'zionbulder/editor/body_class', '' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> id="znpb-html-app">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>
		<?php
		echo esc_attr( Whitelabel::get_title() ) . ' | ' . esc_html( get_the_title() );
		?>
			</title>
		<?php wp_head(); ?>

		<script>
			var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php', 'relative' ) ); ?>';
		</script>
	</head>
	<body class="<?php echo esc_attr( $body_classes ); ?>">
		<div id="znpb-app"></div>

		<?php
			wp_footer();
			/*
			 * Prints any scripts and data queued for the footer.
			 * It is mainly needed for the WP editor
			 *
			 * @since 1.0.0
			 */
			do_action( 'admin_print_footer_scripts' );
		?>
	</body>
</html>
