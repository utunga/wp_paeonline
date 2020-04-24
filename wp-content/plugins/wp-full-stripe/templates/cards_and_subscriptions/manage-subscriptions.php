<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2019.02.19.
 * Time: 09:57
 */

/**
 * @var MM_WPFS_CardUpdateModel $model
 */

?>
<div id="wpfs-manage-subscriptions-container" class="wpfs-form wpfs-w-60">
	<div class="wpfs-form-title"><?php esc_html_e( 'Manage your subscriptions', 'wp-full-stripe' ); ?></div>
	<div class="wpfs-form-lead">
		<div class="wpfs-form-description wpfs-form-description--sm">
			<?php printf( esc_html__( 'You can manage the credit card and subscriptions associated with %s.', 'wp-full-stripe' ), $model->getCustomerEmail() ); ?>
			<?php if ( $model->getAuthenticationType() == MM_WPFS_CardUpdateModel::AUTHENTICATION_TYPE_PLUGIN ): ?>
				<a href="" id="wpfs-anchor-logout" class="wpfs-btn wpfs-btn-link"><?php esc_html_e( 'Log out', 'wp-full-stripe' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<div class="wpfs-form-subtitle"><?php esc_html_e( 'Credit/debit card', 'wp-full-stripe' ); ?></div>
	<form id="wpfs-default-card-form">
		<div class="wpfs-credit-cards">
			<div class="wpfs-credit-card">
				<?php if ( empty( $model->getCardNumber() ) ): ?>
					<div><?php esc_html_e( 'You don\'t have a default card.', 'wp-full-stripe' ); ?></div>
				<?php else: ?>
					<div class="wpfs-credit-card-logo">
						<img src="<?php echo esc_attr( $model->getCardImageUrl() ); ?>" alt="<?php echo esc_attr( $model->getCardName() ); ?>">
					</div>
					<div class="wpfs-credit-card-data">
						<div class="wpfs-credit-card-name"><?php echo esc_html( $model->getCardName() . ' ' . $model->getFormattedCardNumber() ); ?></div>
						<div class="wpfs-credit-card-expires">
							<?php esc_html_e( 'Expires', 'wp-full-stripe' ); ?>
							<br><?php echo esc_html( $model->getExpiration() ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<a id="wpfs-anchor-update-card" class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold"><?php esc_html_e( 'Update card', 'wp-full-stripe' ); ?></a>
		</div>
	</form>
	<form id="wpfs-update-card-form" style="display: none;">
		<div class="wpfs-form-group wpfs-w-45">
			<div class="wpfs-form-control" id="wpfs-card" data-toggle="card"></div>
		</div>
		<div class="wpfs-form-actions wpfs-mt-3 wpfs-mb-4">
			<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2" type="submit"><?php esc_html_e( 'Update card', 'wp-full-stripe' ); ?></button>
			<a id="wpfs-anchor-discard-card-changes" class="wpfs-btn wpfs-btn-link"><?php esc_html_e( 'Discard', 'wp-full-stripe' ); ?></a>
		</div>
	</form>
	<div class="wpfs-form-subtitle" id="wpfs-subscriptions-subtitle"><?php esc_html_e( 'Subscriptions', 'wp-full-stripe' ); ?></div>
	<form id="wpfs-cancel-subscription-form">
		<div id="wpfs-subscriptions-table" class="wpfs-subscriptions"></div>
		<div class="wpfs-form-actions">
			<button id="wpfs-button-cancel-subscription" class="wpfs-btn wpfs-btn-primary" type="submit" disabled><?php esc_html_e( 'Cancel subscription', 'wp-full-stripe' ); ?></button>
		</div>
	</form>
</div>
<script type="text/template" id="wpfs-subscription-show-row">
	<div class="wpfs-form-check">
		<input type="checkbox" class="wpfs-form-check-input" id="<%- idAttribute %>" name="<%- nameAttribute %>" value="<%- id %>">
		<label class="wpfs-form-check-label" for="<%- idAttribute %>">
			<div class="wpfs-subscription-data">
				<div class="wpfs-subscription-name">
					<%- planLabel %> - <span class="wpfs-subscription-status <%- statusClass %>"><%- status %></span>
				</div>
				<div class="wpfs-subscription-meta"><%- priceAndIntervalLabel %>
					• <?php esc_html_e( 'Created at', 'wp-full-stripe' ); ?> <%- created %>
				</div>
				<div class="wpfs-subscription-actions">
					<% if (allowMultipleSubscriptions) { %>
					<a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-subscription-update-quantity-action" href="" data-wpfs-subscription-id="<%- id %>">
						<?php esc_html_e( 'Change quantity', 'wp-full-stripe' ); ?>
					</a>
					<% } %>
					<a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-subscription-cancel-action" href="" data-wpfs-subscription-id="<%- id %>">
						<?php esc_html_e( 'Cancel', 'wp-full-stripe' ); ?>
					</a>
				</div>
			</div>
		</label>
	</div>
</script>
<script type="text/template" id="wpfs-subscription-update-row">
	<div class="wpfs-subscription-name"><%- planName %></div>
	<form class="wpfs-form">
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="name"><?php esc_html_e( 'Subscription quantity', 'wp-full-stripe' ); ?></label>
			<div class="wpfs-stepper wpfs-w-15">
				<input class="wpfs-form-control" type="text" name="wpfs-subscription-plan-quantity" value="<%- planQuantity %>" data-toggle="stepper" data-min="1" data-default-value="1"
				<% if (maximumPlanQuantity > 0) { %>data-max="<%- maximumPlanQuantity %>"<% } %>>
			</div>
		</div>
		<div class="wpfs-subscription-summary-title"><?php esc_html_e( 'Summary:', 'wp-full-stripe' ); ?></div>
		<div class="wpfs-subscription-summary-description">
			<%- summary %>
		</div>
		<div class="wpfs-form-actions wpfs-mt-3 wpfs-mb-4">
			<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2 wpfs-subscription-button-update-quantity" type="submit">
				<?php esc_html_e( 'Update quantity', 'wp-full-stripe' ); ?>
			</button>
			<a href="" class="wpfs-btn wpfs-btn-link wpfs-subscription-link-cancel-update-quantity"><?php esc_html_e( 'Cancel', 'wp-full-stripe' ); ?></a>
		</div>
	</form>
</script>
<script type="text/template" id="wpfs-subscription-empty-subscription-list">
    <?php esc_html_e( 'You don\'t have any subscriptions.', 'wp-full-stripe' ); ?>
</script>
<script type="text/template" id="wpfs-subscription-update-quantity-success-message">
	<strong><?php esc_html_e( 'Updated subscription quantity.', 'wp-full-stripe' ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-update-quantity-error-message">
	<strong><?php esc_html_e( 'We couldn\'t update subscription. Please try again', 'wp-full-stripe' ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-cancel-success-message">
	<strong><?php esc_html_e( 'Cancelled subscription.', 'wp-full-stripe' ); ?></strong>
</script>
<script type="text/template" id="wpfs-subscription-cancel-error-message">
	<strong><?php esc_html_e( 'Failed subscription cancellation, try again.', 'wp-full-stripe' ); ?></strong>
</script>
