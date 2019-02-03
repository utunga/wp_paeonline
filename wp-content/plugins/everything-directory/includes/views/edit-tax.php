<?php
$options = get_option( $this->settings_field );

if ( array_key_exists( $_REQUEST['id'], (array) $options ) ) {
	$taxonomy = stripslashes_deep( $options[$_REQUEST['id']] );
} else {
	wp_die( __( "Nice try, partner. But that taxonomy doesn't exist or can't be edited. Click back and try again.", 'everything-directory' ) );
}
?>

<h2><?php _e( 'Edit Taxonomy', 'everything-directory' ); ?></h2>

<form method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->menu_page . '&amp;action=edit' ); ?>">
<?php wp_nonce_field( 'everythingdir-action_edit-taxonomy' ); ?>
<table class="form-table">

	<tr class="form-field">
		<th scope="row" valign="top"><label for="everythingdir_taxonomy[id]"><?php _e( 'ID', 'everything-directory' ); ?></label></th>
		<td>
		<input type="text" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" disabled="disabled" />
		<input name="everythingdir_taxonomy[id]" id="everythingdir_taxonomy[id]" type="hidden" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" />
		<p class="description"><?php _e( 'The unique ID is used to register the taxonomy. (cannot be changed)', 'everything-directory' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="everythingdir_taxonomy[name]"><?php _e( 'Plural Name', 'everything-directory' ); ?></label></th>
		<td><input name="everythingdir_taxonomy[name]" id="everythingdir_taxonomy[name]" type="text" value="<?php echo esc_html( $taxonomy['labels']['name'] ); ?>" size="40" />
		<p class="description"><?php _e( 'Example: "Property Types" or "Locations"', 'everything-directory' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="everythingdir_taxonomy[singular_name]"><?php _e( 'Singular Name', 'everything-directory' ); ?></label></th>
		<td><input name="everythingdir_taxonomy[singular_name]" id="everythingdir_taxonomy[singular_name]" type="text" value="<?php echo esc_html( $taxonomy['labels']['singular_name'] ); ?>" size="40" />
		<p class="description"><?php _e( 'Example: "Property Type" or "Location"', 'everything-directory' ); ?></p></td>
	</tr>

</table>

<?php submit_button( __( 'Update', 'everything-directory' ) ); ?>

</form>