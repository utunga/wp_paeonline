<?php
/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.07.25.
 * Time: 9:23
 */

/** @var $selectedPlanId */
/** @var stdClass $popupFormSubmit */
/** @var $view */

if ( $view instanceof MM_WPFS_FormView ) {
	// tnagy noop
	// MM_WPFS_Utils::log( 'template: view=' . print_r( $view, true ) );
}

?>
<form <?php $view->formAttributes(); ?>>
	<?php if ( isset( $popupFormSubmit ) && $popupFormSubmit->formHash === $view->getFormHash() ): ?>
		<?php
		$messageClass = 'wpfs-form-message--incorrect';
		if ( MM_WPFS_CheckoutSubmissionService::POPUP_FORM_SUBMIT_STATUS_SUCCESS === $popupFormSubmit->status ) {
			$messageClass = 'wpfs-form-message--correct';
		}
		?>
		<div class="wpfs-form-message <?php echo $messageClass; ?>">
			<div class="wpfs-form-message-title"><?php echo esc_html( $popupFormSubmit->lastMessageTitle ); ?></div>
			<?php echo esc_html( $popupFormSubmit->lastMessage ); ?>
		</div>
	<?php endif; ?>
	<?php // (common)(field): action ?>
	<input id="<?php $view->action()->id(); ?>" name="<?php $view->action()->name(); ?>" value="<?php $view->action()->value(); ?>" <?php $view->action()->attributes(); ?>>
	<?php // (common)(field): form name ?>
	<input id="<?php $view->formName()->id(); ?>" name="<?php $view->formName()->name(); ?>" value="<?php $view->formName()->value(); ?>" <?php $view->formName()->attributes(); ?>>
    <?php // (common)(field): form get parameters ?>
    <input id="<?php $view->formGetParameters()->id(); ?>" name="<?php $view->formGetParameters()->name(); ?>" value="<?php $view->formGetParameters()->value(); ?>" <?php $view->formGetParameters()->attributes(); ?>>
	<?php // (inline_payment|popup_payment)(field): list of amount ?>
	<?php if ( $view instanceof MM_WPFS_PaymentFormView && ! is_null( $view->customAmountOptions() ) ): ?>
		<?php if ( MM_WPFS::PAYMENT_TYPE_LIST_OF_AMOUNTS === $form->customAmount ): ?>
			<?php if ( MM_WPFS::AMOUNT_SELECTOR_STYLE_DROPDOWN === $form->amountSelectorStyle ): ?>
				<div class="wpfs-form-group">
					<label class="wpfs-form-label" for="<?php $view->customAmountOptions()->id(); ?>"><?php $view->customAmountOptions()->label(); ?></label>
					<div class="wpfs-ui wpfs-form-select">
						<select id="<?php $view->customAmountOptions()->id(); ?>" name="<?php $view->customAmountOptions()->name(); ?>" data-toggle="selectmenu" data-wpfs-select="wpfs-custom-amount-select" class="wpfs-custom-amount wpfs-custom-amount-select" <?php $view->customAmountOptions()->attributes(); ?>>
							<?php foreach ( $view->customAmountOptions()->options() as $customAmountOption ): ?>
								<?php /** @var MM_WPFS_Control $customAmountOption */ ?>
								<option value="<?php $customAmountOption->value(); ?>" <?php $customAmountOption->attributes(); ?>><?php $customAmountOption->label(); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( MM_WPFS::AMOUNT_SELECTOR_STYLE_BUTTON_GROUP === $form->amountSelectorStyle ): ?>
				<fieldset class="wpfs-form-check-group wpfs-button-group">
					<legend class="wpfs-form-check-group-title"><?php $view->customAmountOptions()->label(); ?></legend>
					<div class="wpfs-button-group-row wpfs-button-group-row--fixed">
						<?php foreach ( $view->customAmountOptions()->options() as $customAmountOption ): ?>
							<?php /** @var MM_WPFS_Control $customAmountOption */ ?>
							<div class="wpfs-button-group-item">
								<input id="<?php $customAmountOption->id(); ?>" name="<?php $customAmountOption->name(); ?>" type="radio" class="wpfs-form-check-input wpfs-custom-amount" value="<?php $customAmountOption->value(); ?>" <?php $customAmountOption->attributes(); ?>>
								<label class="wpfs-btn wpfs-btn-outline-primary" for="<?php $customAmountOption->id(); ?>"><?php $customAmountOption->label(); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<?php if ( MM_WPFS::AMOUNT_SELECTOR_STYLE_RADIO_BUTTONS === $form->amountSelectorStyle ): ?>
				<fieldset class="wpfs-form-check-group">
					<legend class="wpfs-form-check-group-title"><?php $view->customAmountOptions()->label(); ?></legend>
					<?php foreach ( $view->customAmountOptions()->options() as $customAmountOption ): ?>
						<?php /** @var MM_WPFS_Control $customAmountOption */ ?>
						<div class="wpfs-form-check">
							<input id="<?php $customAmountOption->id(); ?>" name="<?php $customAmountOption->name(); ?>" type="radio" class="wpfs-form-check-input wpfs-custom-amount" value="<?php $customAmountOption->value(); ?>" <?php $customAmountOption->attributes(); ?>>
							<label class="wpfs-form-check-label" for="<?php $customAmountOption->id(); ?>">
								<?php $customAmountOption->label(); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php // (inline_payment|popup_payment)(field): custom amount ?>
	<?php if ( $view instanceof MM_WPFS_PaymentFormView && ( 1 == $form->allowListOfAmountsCustom || MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $form->customAmount ) ): ?>
		<div class="wpfs-form-group wpfs-w-20" data-wpfs-amount-row="custom-amount" <?php echo( MM_WPFS::PAYMENT_TYPE_CUSTOM_AMOUNT == $form->customAmount ? '' : 'style="display: none;"' ); ?>>
			<label <?php $view->customAmount()->labelAttributes(); ?> for="<?php $view->customAmount()->id(); ?>"><?php $view->customAmount()->label(); ?></label>
			<div class="wpfs-input-group">
				<div class="wpfs-input-group-prepend">
					<span class="wpfs-input-group-text"><?php $view->_currencySymbol(); ?></span>
				</div>
				<input id="<?php $view->customAmount()->id(); ?>" name="<?php $view->customAmount()->name(); ?>" type="text" class="wpfs-input-group-form-control wpfs-custom-amount--unique" placeholder="<?php $view->customAmount()->placeholder(); ?>" <?php $view->customAmount()->attributes(); ?>>
			</div>
		</div>
	<?php endif; ?>
	<?php // (inline_subscription|popup_subscription)(field): plans ?>
	<?php if ( $view instanceof MM_WPFS_PopupSubscriptionFormView && 1 == $form->simpleButtonLayout ): ?>
		<?php if ( is_null( $view->firstPlan() ) ): ?>
			<div class="wpfs-form-message wpfs-form-message--incorrect">
				<div class="wpfs-form-message-title"><?php esc_html_e( 'Invalid plan', 'wp-full-stripe' ); ?></div>
				<?php printf(
					esc_html__( 'Checkout subscription form \'%s\': cannot find subscription plan \'%s\'.', 'wp-full-stripe' ),
					$view->getFormName(),
					$view->getFirstPlanName()
				); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! is_null( $view->firstPlan() ) ): ?>
			<input id="<?php $view->plans()->id(); ?>" name="<?php $view->plans()->name(); ?>" value="<?php $view->firstPlan()->value(); ?>" <?php $view->firstPlan()->attributes(); ?>>
		<?php endif; ?>
	<?php elseif ( $view instanceof MM_WPFS_SubscriptionFormView ): ?>
		<?php if ( count( $view->plans()->options() ) > 1 ): ?>
			<?php if ( MM_WPFS::PLAN_SELECTOR_STYLE_DROPDOWN === $form->planSelectorStyle ): ?>
				<div class="wpfs-form-group">
					<label class="wpfs-form-label" for="<?php $view->plans()->id(); ?>"><?php $view->plans()->label(); ?></label>
					<div class="wpfs-ui wpfs-form-select">
						<select name="<?php $view->plans()->name(); ?>" id="<?php $view->plans()->id(); ?>" data-toggle="selectmenu" data-wpfs-select="wpfs-subscription-plan-select" class="wpfs-subscription-plan-select">
							<?php foreach ( $view->plans()->options() as $plan ): ?>
								<?php /** @var MM_WPFS_Control $plan */ ?>
								<option value="<?php $plan->value(); ?>" <?php $plan->attributes(); ?> <?php echo( $plan->value( false ) === $selectedPlanId ? "selected='selected'" : "" ); ?>><?php $plan->label(); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php elseif ( MM_WPFS::PLAN_SELECTOR_STYLE_LIST === $form->planSelectorStyle ): ?>
				<fieldset class="wpfs-form-check-group">
					<legend class="wpfs-form-check-group-title"><?php $view->plans()->label(); ?></legend>
					<?php foreach ( $view->plans()->options() as $plan ): ?>
						<?php /** @var MM_WPFS_Control $plan */ ?>
						<div class="wpfs-form-check">
							<input type="radio" id="<?php $plan->id(); ?>" name="<?php $plan->name(); ?>" value="<?php $plan->value(); ?>" class="wpfs-form-check-input wpfs-subscription-plan-radio" <?php $plan->attributes(); ?> <?php echo( $plan->value( false ) === $selectedPlanId ? "checked='checked'" : "" ); ?>>
							<label class="wpfs-form-check-label" for="<?php $plan->id(); ?>">
								<?php $plan->label(); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php elseif ( count( $view->plans()->options() ) == 1 ): ?>
			<input id="<?php $view->plans()->id(); ?>" name="<?php $view->plans()->name(); ?>" value="<?php $view->firstPlan()->value(); ?>" <?php $view->firstPlan()->attributes(); ?> class="wpfs-subscription-plan-hidden">
		<?php else: ?>
			<div class="wpfs-form-message wpfs-form-message--incorrect">
				<div class="wpfs-form-message-title"><?php esc_html_e( 'Internal Error', 'wp-full-stripe' ); ?></div>
				<?php esc_html_e( 'Select at least one plan for this form!', 'wp-full-stripe' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php // (inline_subscription|popup_subscription)(field): plans ?>
	<?php if ( $view instanceof MM_WPFS_SubscriptionFormView && $form->allowMultipleSubscriptions == '1' ): ?>
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="<?php $view->planQuantity()->id(); ?>"><?php $view->planQuantity()->label(); ?></label>
			<div class="wpfs-stepper wpfs-w-15">
				<input id="<?php $view->planQuantity()->id(); ?>" name="<?php $view->planQuantity()->name(); ?>" class="wpfs-form-control" type="text" data-toggle="stepper" <?php $view->planQuantity()->attributes(); ?>>
			</div>
		</div>
	<?php endif; ?>
	<?php // (inline_payment|inline_subscription|inline_card_capture)(field): cardholder name ?>
	<?php if ( $view instanceof MM_WPFS_InlinePaymentFormView || $view instanceof MM_WPFS_InlineCardCaptureFormView || $view instanceof MM_WPFS_InlineSubscriptionFormView ): ?>
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="<?php $view->cardHolderName()->id(); ?>"><?php $view->cardHolderName()->label(); ?></label>
			<input id="<?php $view->cardHolderName()->id(); ?>" name="<?php $view->cardHolderName()->name(); ?>" type="text" class="wpfs-form-control">
		</div>
	<?php endif; ?>
	<?php // (inline_payment|inline_subscription|inline_card_capture)(field): cardholder email ?>
	<?php if ( $view instanceof MM_WPFS_InlinePaymentFormView || $view instanceof MM_WPFS_InlineCardCaptureFormView || $view instanceof MM_WPFS_InlineSubscriptionFormView ): ?>
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="<?php $view->cardHolderEmail()->id(); ?>"><?php $view->cardHolderEmail()->label(); ?></label>
			<input id="<?php $view->cardHolderEmail()->id(); ?>" name="<?php $view->cardHolderEmail()->name(); ?>" type="email" class="wpfs-form-control" value="<?php $view->cardHolderEmail()->value(); ?>">
		</div>
	<?php endif; ?>
	<?php
	// (common)(field): custom inputs
	$showCustomInputGroup = isset( $form->showCustomInput ) && 1 == $form->showCustomInput;
	if ( $view instanceof MM_WPFS_PopupSubscriptionFormView && 1 == $form->simpleButtonLayout ) {
		$showCustomInputGroup = false;
	}
	?>
	<?php if ( $showCustomInputGroup ): ?>
		<?php foreach ( $view->customInputs() as $input ): ?>
			<?php /** @var MM_WPFS_Control $input */ ?>
			<div class="wpfs-form-group">
				<label class="wpfs-form-label" for="<?php $input->id(); ?>"><?php $input->label(); ?></label>
				<input id="<?php $input->id(); ?>" name="<?php $input->name(); ?>" type="text" class="wpfs-form-control" <?php $input->attributes(); ?>>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php // (inline_payment|inline_subscription|inline_card_capture)(field): billing and shipping address ?>
	<?php include( 'fullstripe_form_billing_shipping_address.php' ); ?>
	<?php // (popup_subscription)(field): custom VAT billing address country ?>
	<?php if ( $view instanceof MM_WPFS_PopupSubscriptionFormView && ! is_null( $view->customVATBillingCountry() ) ): ?>
		<div class="wpfs-form-group">
			<label class="wpfs-form-label" for="<?php $view->customVATBillingCountry()->id(); ?>"><?php $view->customVATBillingCountry()->label(); ?></label>
			<div class="wpfs-ui wpfs-form-select">
				<select id="<?php $view->customVATBillingCountry()->id(); ?>" name="<?php $view->customVATBillingCountry()->name(); ?>" data-toggle="selectmenu" data-wpfs-select="wpfs-billing-address-country-select" class="wpfs-billing-address-country-select" <?php $view->customVATBillingCountry()->attributes(); ?>>
					<?php foreach ( $view->customVATBillingCountry()->options() as $country ) : ?>
						<?php /** @var MM_WPFS_Control $country */ ?>
						<option value="<?php $country->value(); ?>" <?php $country->attributes(); ?>><?php $country->caption(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	<?php
	// (inline_subscription|popup_subscription)(field): coupon input
	$showCouponInputGroup = isset( $form->showCouponInput ) && 1 == $form->showCouponInput;
	if ( $view instanceof MM_WPFS_PopupSubscriptionFormView && 1 == $form->simpleButtonLayout ) {
		$showCouponInputGroup = false;
	}
	?>
	<?php if ( $showCouponInputGroup ): ?>
		<div class="wpfs-form-group">
			<label class="wpfs-form-label wpfs-form-label--with-info" for="coupon">
				<?php $view->coupon()->label(); ?>
				<span class="wpfs-icon-help-circle wpfs-form-label-info" data-toggle="tooltip" data-tooltip-content="info-tooltip"></span>
			</label>
			<div class="wpfs-tooltip-content" data-tooltip-id="info-tooltip">
				<div class="wpfs-info-tooltip">
					<?php $view->coupon()->tooltip(); ?>
				</div>
			</div>
			<div class="wpfs-coupon wpfs-coupon-redeemed-row" style="display: none;">
				<span class="wpfs-coupon-redeemed-label" data-wpfs-coupon-redeemed-label="<?php esc_attr_e( 'Coupon code <strong>%s</strong> added.', 'wp-full-stripe' ); ?>">&nbsp;</span>
				<a class="wpfs-btn wpfs-btn-link wpfs-btn-link--bold wpfs-coupon-remove-link" href=""><?php esc_html_e( 'Remove', 'wp-full-stripe' ); ?></a>
			</div>
			<div class="wpfs-input-group wpfs-coupon-to-redeem-row">
				<input id="<?php $view->coupon()->id(); ?>" name="<?php $view->coupon()->name(); ?>" type="text" class="wpfs-input-group-form-control" placeholder="<?php $view->coupon()->placeholder(); ?>">
				<div class="wpfs-input-group-append">
					<a class="wpfs-input-group-link wpfs-coupon-redeem-link" href=""><span><?php esc_html_e( 'Redeem', 'wp-full-stripe' ); ?></span></a>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php // (inline_payment|inline_subscription|inline_card_capture)(field): card ?>
	<?php if ( $view instanceof MM_WPFS_InlinePaymentFormView || $view instanceof MM_WPFS_InlineCardCaptureFormView || $view instanceof MM_WPFS_InlineSubscriptionFormView ): ?>
		<div class="wpfs-form-group wpfs-w-45">
			<label class="wpfs-form-label" for="<?php $view->card()->id(); ?>"><?php $view->card()->label(); ?></label>
			<div class="wpfs-form-control" id="<?php $view->card()->id(); ?>" data-toggle="card" data-wpfs-form-id="<?php $view->_formName(); ?>"></div>
		</div>
	<?php endif; ?>
	<?php // (common)(field): terms of use ?>
	<?php if ( isset( $form->showTermsOfUse ) && 1 == $form->showTermsOfUse ): ?>
		<div class="wpfs-form-check">
			<input type="checkbox" class="wpfs-form-check-input" id="<?php $view->tOUAccepted()->id(); ?>" name="<?php $view->tOUAccepted()->name(); ?>" value="1">
			<label class="wpfs-form-check-label" for="<?php $view->tOUAccepted()->id(); ?>">
				<?php $view->tOUAccepted()->label(); ?>
			</label>
		</div>
	<?php endif; ?>
	<?php // (inline_payment|inline_subscription|inline_card_capture)(div): captcha ?>
	<?php if ( $view instanceof MM_WPFS_InlinePaymentFormView || $view instanceof MM_WPFS_InlineCardCaptureFormView || $view instanceof MM_WPFS_InlineSubscriptionFormView ): ?>
		<?php if ( MM_WPFS_Utils::get_secure_inline_forms_with_google_recaptcha() ): ?>
			<div class="wpfs-form-group">
				<label class="wpfs-form-label"><?php _e( 'Prove you are a human', 'wp-full-stripe' ); ?></label>
				<div class="wpfs-form-captcha" data-wpfs-field-name="g-recaptcha-response" data-wpfs-form-hash="<?php echo esc_attr( $view->getFormHash() ); ?>"></div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( $view instanceof MM_WPFS_PopupPaymentFormView || $view instanceof MM_WPFS_PopupCardCaptureFormView || $view instanceof MM_WPFS_PopupSubscriptionFormView ): ?>
		<?php if ( MM_WPFS_Utils::get_secure_checkout_forms_with_google_recaptcha() ): ?>
			<div class="wpfs-form-group">
				<label class="wpfs-form-label"><?php _e( 'Prove you are a human', 'wp-full-stripe' ); ?></label>
				<div class="wpfs-form-captcha" data-wpfs-field-name="g-recaptcha-response" data-wpfs-form-hash="<?php echo esc_attr( $view->getFormHash() ); ?>"></div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php // (common)(button): submit ?>
	<div class="wpfs-form-actions">
		<button class="wpfs-btn wpfs-btn-primary wpfs-mr-2" id="<?php $view->submitButton()->id(); ?>" type="submit" <?php $view->submitButton()->attributes(); ?>><?php $view->submitButton()->caption(); ?></button>
		<?php
		// (inline_subscription|popup_subscription)(table): payment details
		$showPaymentDetailsTable = $view instanceof MM_WPFS_SubscriptionFormView;
		if ( $view instanceof MM_WPFS_PopupSubscriptionFormView && 1 == $form->simpleButtonLayout ) {
			$showPaymentDetailsTable = false;
		}
		if ( $view instanceof MM_WPFS_SubscriptionFormView && count( $view->plans()->options() ) == 0 ) {
			$showPaymentDetailsTable = false;
		}
		?>
		<?php if ( $showPaymentDetailsTable ): ?>
			<a href="" class="wpfs-btn wpfs-btn-link wpfs-btn-link--sm" data-toggle="tooltip" data-tooltip-content="<?php echo esc_attr( 'wpfs-form-summary-' . $view->getFormHash() ); ?>"><?php _e( 'Payment details', 'wp-full-stripe' ); ?></a>
			<div class="wpfs-tooltip-content" data-tooltip-id="<?php echo esc_attr( 'wpfs-form-summary-' . $view->getFormHash() ); ?>">
				<div class="wpfs-summary">
					<table class="wpfs-summary-table">
						<tbody>
						<tr class="wpfs-summary-table-row" data-wpfs-summary-row="setup-fee">
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-label="setup-fee"><?php esc_html_e( 'Setup fee', 'wp-full-stripe' ); ?></td>
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-value="setup-fee">&nbsp;</td>
						</tr>
						<tr class="wpfs-summary-table-row" data-wpfs-summary-row="subscription">
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-label="subscription" data-wpfs-summary-row-label-value="<?php echo esc_attr( '%d%s %s' ); ?>"><?php esc_html_e( 'Subscription plan', 'wp-full-stripe' ); ?></td>
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-value="subscription">
								&nbsp;</td>
						</tr>
						<tr class="wpfs-summary-table-row" data-wpfs-summary-row="discount">
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-label="discount" data-wpfs-summary-row-label-value="<?php esc_attr_e( 'Coupon discount (%s)', 'wp-full-stripe' ); ?>"><?php esc_html_e( 'Coupon discount (%s)', 'wp-full-stripe' ); ?></td>
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-value="discount">&nbsp;</td>
						</tr>
						<tr class="wpfs-summary-table-row" data-wpfs-summary-row="vat">
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-label="vat" data-wpfs-summary-row-label-value="<?php esc_attr_e( 'VAT (%s%%)', 'wp-full-stripe' ); ?>"><?php echo esc_html( sprintf( __( 'VAT (%s%%)', 'wp-full-stripe' ), $view->getCurrentVATPercent() ) ); ?></td>
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-value="vat">&nbsp;</td>
						</tr>
						</tbody>
						<tfoot>
						<tr class="wpfs-summary-table-total" data-wpfs-summary-row="total">
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-label="total"><?php esc_html_e( 'Total', 'wp-full-stripe' ); ?></td>
							<td class="wpfs-summary-table-cell" data-wpfs-summary-row-value="total">&nbsp;</td>
						</tr>
						</tfoot>
					</table>
					<p class="wpfs-summary-description">&nbsp;</p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</form>
