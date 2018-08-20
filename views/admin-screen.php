<div class="wrap">
    <form method="post" action="options.php">
		<?php wp_nonce_field( 'update-options' ); ?>
		<?php settings_fields( 'wp_switch_options' ); ?>
		<?php do_settings_sections( 'wp_switch_options' ); ?>
    </form>
</div>
