<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Karlılık Ayarları', 'melontik-profit-stock' ); ?></h1>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'mps_settings_group' );
		do_settings_sections( 'mps_settings' );
		submit_button();
		?>
	</form>
</div>
