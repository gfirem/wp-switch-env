<select name="wp_switch_options[current_env]" id="wp_switch_options[current_env]">
    <option value=""></option>
	<?php foreach ( $settings as $key => $data ): ?>
        <option <?php selected( intval( $current_value ), $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $data['name'] ) ?></option>
	<?php endforeach; ?>
</select>