<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2017.09.06.
 * Time: 16:30
 */
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label class="control-label"><?php esc_html_e( 'Collect Billing Address?', 'wp-full-stripe' ); ?> </label>
		</th>
		<td>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="hide_address_input" value="0" checked="checked">
				<?php esc_html_e( 'Hide', 'wp-full-stripe' ); ?>
			</label>
			<label class="radio inline">
				<input type="radio" name="form_show_address_input" id="show_address_input" value="1">
				<?php esc_html_e( 'Show', 'wp-full-stripe' ); ?>
			</label>
			<p class="description"><?php esc_html_e( 'Should this form also ask for the customers billing address?', 'wp-full-stripe' ); ?></p>
		</td>
	</tr>
</table>
