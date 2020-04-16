<?php

/**
 * Created by PhpStorm.
 * User: tnagy
 * Date: 2018.06.15.
 * Time: 13:25
 */
class MM_WPFS_EventHandler {

	/** @var $db MM_WPFS_Database */
	protected $db = null;
	/** @var $stripe MM_WPFS_Stripe */
	protected $stripe = null;
	/** @var $mailer MM_WPFS_Mailer */
	protected $mailer = null;
	/** @var array */
	protected $eventProcessors = array();
	/** @var MM_WPFS_LoggerService */
	private $loggerService;
	/** @var MM_WPFS_Logger */
	private $logger;

	/**
	 * MM_WPFS_WebHookEventHandler constructor.
	 *
	 * @param MM_WPFS_Database $db
	 * @param MM_WPFS_Stripe $stripe
	 * @param MM_WPFS_Mailer $mailer
	 * @param MM_WPFS_LoggerService $loggerService
	 */
	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		$this->db            = $db;
		$this->stripe        = $stripe;
		$this->mailer        = $mailer;
		$this->loggerService = $loggerService;
		$this->logger        = $this->loggerService->createWebHookEventHandlerLogger( MM_WPFS_EventHandler::class );
		$this->initProcessors();
	}

	protected function initProcessors() {
		$processors = array(
			new MM_WPFS_CustomerSubscriptionDeleted( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_InvoiceCreated( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_InvoicePaymentSucceeded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeCaptured( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeExpired( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeFailed( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargePending( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeRefunded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeSucceeded( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_ChargeUpdated( $this->db, $this->stripe, $this->mailer, $this->loggerService ),
			new MM_WPFS_CustomerSubscriptionUpdated( $this->db, $this->stripe, $this->mailer, $this->loggerService )
		);
		foreach ( $processors as $processor ) {
			$this->eventProcessors[ $processor->get_type() ] = $processor;
		}
	}

	public function handle( $event ) {
		try {
			$eventProcessed = false;
			if ( isset( $event ) && isset( $event->type ) ) {
				$eventProcessor = null;
				if ( array_key_exists( $event->type, $this->eventProcessors ) ) {
					$eventProcessor = $this->eventProcessors[ $event->type ];
				}
				if ( $eventProcessor instanceof MM_WPFS_EventProcessor ) {
					$eventProcessor->process( $event );
					$eventProcessed = true;
				}
			}

			return $eventProcessed;
		} catch ( Exception $e ) {
			$this->logger->error( __FUNCTION__, $e->getMessage(), $e );
			throw $e;
		}
	}

}

abstract class MM_WPFS_EventProcessor {

	const STRIPE_API_VERSION_2018_02_28 = '2018-02-28';
	const STRIPE_API_VERSION_2018_05_21 = '2018-05-21';

	/* @var MM_WPFS_LoggerService */
	protected $loggerService;
	/* @var $db MM_WPFS_Database */
	protected $db = null;
	/* @var $stripe MM_WPFS_Stripe */
	protected $stripe = null;
	/* @var $mailer MM_WPFS_Mailer */
	protected $mailer = null;
	/* @var boolean */
	protected $debugLog = false;

	/**
	 * MM_WPFS_WebHookEventProcessor constructor.
	 *
	 * @param MM_WPFS_Database $db
	 * @param MM_WPFS_Stripe $stripe
	 * @param MM_WPFS_Mailer $mailer
	 * @param MM_WPFS_LoggerService $loggerService
	 */
	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		$this->db            = $db;
		$this->stripe        = $stripe;
		$this->mailer        = $mailer;
		$this->loggerService = $loggerService;
	}

	public final function process( $event_object ) {
		if ( $this->get_type() === $event_object->type ) {
			$this->process_event( $event_object );
		}
	}

	public abstract function get_type();

	protected function process_event( $event ) {
		// tnagy default implementation, override in subclasses
	}

	/**
	 * @param \StripeWPFS\Event $event
	 *
	 * @return null|\StripeWPFS\ApiResource
	 */
	protected function get_data_object( $event ) {
		$object = null;
		if ( isset( $event ) && isset( $event->data ) && isset( $event->data->object ) ) {
			$object = $event->data->object;
		}

		return $object;
	}

	/**
	 * Adds an event ID to a JSON encoded array if the ID is not in the array
	 *
	 * @param string $encodedStripeEventIDs JSON encoded event ID array
	 * @param \StripeWPFS\Event $stripeEvent
	 * @param bool $success output variable to determine whether the event ID has been added to the array
	 *
	 * @return string the new JSON encoded array
	 */
	protected function insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, &$success ) {
		$decodedStripeEventIDs = json_decode( $encodedStripeEventIDs );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$decodedStripeEventIDs = array();
		}
		if ( ! is_array( $decodedStripeEventIDs ) ) {
			$decodedStripeEventIDs = array();
		}
		if ( isset( $stripeEvent ) && isset( $stripeEvent->id ) ) {
			if ( in_array( $stripeEvent->id, $decodedStripeEventIDs ) ) {
				$data    = $encodedStripeEventIDs;
				$success = false;
			} else {
				array_push( $decodedStripeEventIDs, $stripeEvent->id );
				$data = json_encode( $decodedStripeEventIDs );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					$success = true;
				} else {
					$success = false;
				}
			}
		} else {
			$data    = $encodedStripeEventIDs;
			$success = false;
		}

		return $data;
	}

	/**
	 * @param \StripeWPFS\Event $event
	 *
	 * @return null|array
	 */
	protected function get_data_previous_attributes( $event ) {
		$previous_attributes = null;
		if ( isset( $event ) && isset( $event->data ) && isset( $event->data->previous_attributes ) ) {
			$previous_attributes = $event->data->previous_attributes;
		}

		return $previous_attributes;
	}

}

abstract class MM_WPFS_InvoiceEventProcessor extends MM_WPFS_EventProcessor {

	const INVOICE_ITEM_TYPE_SUBSCRIPTION = 'subscription';

	protected function findSubscriptionIdInLine( $event, $line ) {
		$stripe_subscription_id        = null;
		$stripe_subscription_id_source = null;
		if ( strtotime( self::STRIPE_API_VERSION_2018_05_21 ) <= strtotime( $event->api_version ) ) {
			if ( self::INVOICE_ITEM_TYPE_SUBSCRIPTION === $line->type ) {
				$stripe_subscription_id        = $line->subscription;
				$stripe_subscription_id_source = 'subscription';
			}
		} else {
			$stripe_subscription_id        = $line->id;
			$stripe_subscription_id_source = 'id';
		}

		if ( $this->getLogger()->isDebugEnabled() ) {
			$this->getLogger()->debug( __FUNCTION__, "api_version={$event->api_version}, stripe_subscription_id={$stripe_subscription_id}, stripe_subscription_id_source={$stripe_subscription_id_source}" );
		}

		return $stripe_subscription_id;
	}

	/**
	 * @return MM_WPFS_Logger
	 */
	protected abstract function getLogger();

	/**
	 * @param \StripeWPFS\Event $event
	 * @param \StripeWPFS\InvoiceLineItem $line
	 *
	 * @return null
	 */
	protected function findSubmitHashInLine( $event, $line ) {
		$submitHash = null;
		if ( strtotime( self::STRIPE_API_VERSION_2018_05_21 ) <= strtotime( $event->api_version ) ) {
			if ( self::INVOICE_ITEM_TYPE_SUBSCRIPTION === $line->type ) {
				if ( isset( $line->metadata ) && isset( $line->metadata->client_reference_id ) ) {
					$submitHash = $line->metadata->client_reference_id;
				}
			}
		}

		return $submitHash;
	}

	/**
	 * @param $stripe_subscription_id
	 *
	 * @return array|null|object|void
	 */
	protected function findSubscriberByStripeSubscriptionId( $stripe_subscription_id ) {
		return $this->db->find_subscription_by_stripe_subscription_id( $stripe_subscription_id );
	}

}

class MM_WPFS_CustomerSubscriptionDeleted extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CUSTOMER_SUBSCRIPTION_DELETED;
	}

	protected function process_event( $event ) {
		$stripeSubscription = $this->get_data_object( $event );
		if ( ! is_null( $stripeSubscription ) ) {
			$wpfsSubscriber = $this->db->find_subscription_by_stripe_subscription_id( $stripeSubscription->id );
			if ( isset( $wpfsSubscriber ) ) {
				if ( MM_WPFS::SUBSCRIPTION_STATUS_ENDED !== $wpfsSubscriber->status && MM_WPFS::SUBSCRIPTION_STATUS_CANCELLED !== $wpfsSubscriber->status ) {
					if ( $wpfsSubscriber->chargeMaximumCount > 0 ) {
						if ( $wpfsSubscriber->chargeCurrentCount >= $wpfsSubscriber->chargeMaximumCount ) {
							if ( isset( $wpfsSubscriber->processedStripeEventIDs ) ) {
								$encodedStripeEventIDs = $wpfsSubscriber->processedStripeEventIDs;
							} else {
								$encodedStripeEventIDs = null;
							}
							$inserted                = false;
							$processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $event, $inserted );
							if ( $inserted ) {
								$this->db->completeSubscriptionWithEvent( $stripeSubscription->id, $processedStripeEventIDs );
							}
						} else {
							$this->db->cancel_subscription_by_stripe_subscription_id( $stripeSubscription->id );
						}
					} else {
						$this->db->cancel_subscription_by_stripe_subscription_id( $stripeSubscription->id );
					}
				}
			}
		}
	}

}

class MM_WPFS_InvoicePaymentSucceeded extends MM_WPFS_InvoiceEventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::INVOICE_PAYMENT_SUCCEEDED;
	}

	protected function getLogger() {
		return $this->logger;
	}

	protected function process_event( $event ) {
		foreach ( $event->data->object->lines->data as $line ) {
			$wpfsSubscriber       = null;
			$stripeSubscriptionId = $this->findSubscriptionIdInLine( $event, $line );
			if ( $this->logger->isDebugEnabled() ) {
				$this->logger->debug( __FUNCTION__, "stripe_subscription_id=$stripeSubscriptionId" );
			}
			if ( ! is_null( $stripeSubscriptionId ) ) {
				$wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripeSubscriptionId );
				if ( isset( $wpfsSubscriber ) ) {
					if (
						MM_WPFS::SUBSCRIBER_STATUS_ENDED !== $wpfsSubscriber->status
						&& MM_WPFS::SUBSCRIBER_STATUS_CANCELLED !== $wpfsSubscriber->status
					) {
						$this->updateSubscriberWithPaymentAndEvent( $wpfsSubscriber, $event );
					} else {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, "subscription status is 'ended' or 'cancelled', skip" );
						}
					}
				} else {
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, 'subscription not found, try to find PopupFormSubmit entry...' );
					}
					$submitHash = $this->findSubmitHashInLine( $event, $line );
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, 'submitHash=' . "$submitHash" );
					}
					if ( ! is_null( $submitHash ) ) {
						$popupFormSubmit = $this->findPopupFormSubmitByHash( $submitHash );
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, 'popupFormSubmit=' . print_r( $popupFormSubmit, true ) );
						}
						if ( ! is_null( $popupFormSubmit ) ) {
							$this->updatePopupFormSubmitWithEvent( $popupFormSubmit, $event );
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, 'popupFormSubmit updated with event ID' );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param $wpfsSubscriber
	 * @param $stripeEvent
	 *
	 * @return bool|int
	 */
	protected function updateSubscriberWithPaymentAndEvent( $wpfsSubscriber, $stripeEvent ) {
		if ( isset( $wpfsSubscriber->processedEventIDs ) ) {
			$encodedStripeEventIDs = $wpfsSubscriber->processedEventIDs;
		} else {
			$encodedStripeEventIDs = null;
		}
		$inserted                = false;
		$processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
		if ( $inserted ) {
			return $this->db->updateSubscriberWithPaymentAndEvent( $wpfsSubscriber->stripeSubscriptionID, $processedStripeEventIDs );
		}

		return false;
	}

	/**
	 * @param $submitHash
	 *
	 * @return array|null|object|void
	 */
	protected function findPopupFormSubmitByHash( $submitHash ) {
		return $this->db->find_popup_form_submit_by_hash( $submitHash );
	}

	/**
	 * @param $popupFormSubmit
	 * @param \StripeWPFS\Event $stripeEvent
	 *
	 * @return bool|int
	 */
	protected function updatePopupFormSubmitWithEvent( $popupFormSubmit, $stripeEvent ) {
		if ( isset( $popupFormSubmit->relatedStripeEventIDs ) ) {
			$encodedStripeEventIDs = $popupFormSubmit->relatedStripeEventIDs;
		} else {
			$encodedStripeEventIDs = null;
		}
		$inserted              = false;
		$relatedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
		if ( $inserted ) {
			if ( $this->debugLog ) {
				MM_WPFS_Utils::log( 'MM_WPFS_InvoicePaymentSucceeded::updatePopupFormSubmitWithEvent(): ' . sprintf( 'Updating PopupFormSubmit \'%s\' with event ID \'%s\'', $popupFormSubmit->hash, $stripeEvent->id ) );
			}

			return $this->db->updatePopupFormSubmitWithEvent( $popupFormSubmit->hash, $relatedStripeEventIDs );
		}

		return false;
	}

}

class MM_WPFS_InvoiceCreated extends MM_WPFS_InvoiceEventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::INVOICE_CREATED;
	}

	protected function getLogger() {
		return $this->logger;
	}

	protected function process_event( $event ) {
		foreach ( $event->data->object->lines->data as $line ) {
			$wpfsSubscriber         = null;
			$stripe_subscription_id = $this->findSubscriptionIdInLine( $event, $line );
			if ( $this->logger->isDebugEnabled() ) {
				$this->logger->debug( __FUNCTION__, "stripe_subscription_id=$stripe_subscription_id" );
			}
			if ( ! is_null( $stripe_subscription_id ) ) {
				$wpfsSubscriber = $this->findSubscriberByStripeSubscriptionId( $stripe_subscription_id );
				if ( isset( $wpfsSubscriber ) ) {
					if (
						MM_WPFS::SUBSCRIBER_STATUS_ENDED !== $wpfsSubscriber->status
						&& MM_WPFS::SUBSCRIBER_STATUS_CANCELLED !== $wpfsSubscriber->status
					) {
						if ( $wpfsSubscriber->chargeMaximumCount > 0 ) {
							if ( $wpfsSubscriber->chargeCurrentCount >= $wpfsSubscriber->chargeMaximumCount ) {
								$this->completeSubscription( $wpfsSubscriber, $event );
							} else {
								if ( $this->logger->isDebugEnabled() ) {
									$this->logger->debug( __FUNCTION__, 'subscription charged until maximum charge reached' );
								}
							}
						} else {
							if ( $this->logger->isDebugEnabled() ) {
								$this->logger->debug( __FUNCTION__, "subscription->chargeMaximumCount is zero" );
							}
						}
					} else {
						if ( $this->logger->isDebugEnabled() ) {
							$this->logger->debug( __FUNCTION__, "subscription status is 'ended' or 'cancelled', skip" );
						}
					}
				} else {
					if ( $this->logger->isDebugEnabled() ) {
						$this->logger->debug( __FUNCTION__, "subscription not found" );
					}
				}
			}
		}
	}

	/**
	 * @param $wpfsSubscriber
	 * @param \StripeWPFS\Event $stripeEvent
	 */
	protected function completeSubscription( $wpfsSubscriber, $stripeEvent ) {
		if ( isset( $wpfsSubscriber->processedStripeEventIDs ) ) {
			$encodedStripeEventIDs = $wpfsSubscriber->processedStripeEventIDs;
		} else {
			$encodedStripeEventIDs = null;
		}
		$inserted                = false;
		$processedStripeEventIDs = $this->insertIfNotExists( $encodedStripeEventIDs, $stripeEvent, $inserted );
		if ( $inserted ) {
			$this->db->completeSubscriptionWithEvent( $wpfsSubscriber->stripeSubscriptionID, $processedStripeEventIDs );
			$this->stripe->cancel_subscription( $wpfsSubscriber->stripeCustomerID, $wpfsSubscriber->stripeSubscriptionID );
			// tnagy reload WPFS subscriber
			$wpfsSubscriber    = $this->findSubscriberByStripeSubscriptionId( $wpfsSubscriber->stripeSubscriptionID );
			$stripePlan        = $this->stripe->retrieve_plan( $wpfsSubscriber->planID );
			$country_composite = MM_WPFS_Countries::get_country_by_name( $wpfsSubscriber->addressCountry );
			$billing_address   = MM_WPFS_Utils::prepare_address_data( $wpfsSubscriber->addressLine1, $wpfsSubscriber->addressLine2, $wpfsSubscriber->addressCity, $wpfsSubscriber->addressState, $wpfsSubscriber->addressCountry, is_null( $country_composite ) ? '' : $country_composite['alpha-2'], $wpfsSubscriber->addressZip );
			$shipping_address  = MM_WPFS_Utils::prepare_address_data( $wpfsSubscriber->shippingAddressLine1, $wpfsSubscriber->shippingAddressLine2, $wpfsSubscriber->shippingAddressCity, $wpfsSubscriber->shippingAddressState, $wpfsSubscriber->shippingAddressCountry, is_null( $country_composite ) ? '' : $country_composite['alpha-2'], $wpfsSubscriber->addressZip );
			$product_name      = '';
			$send_receipt      = false;
			if ( isset( $stripePlan ) ) {
				$form_send_receipt = false;
				$form              = $this->db->get_subscription_form_by_id( $wpfsSubscriber->formId );
				if ( isset( $form ) ) {
					$form_send_receipt = $form->sendEmailReceipt == 1 ? true : false;
				}
				if ( $form_send_receipt ) {
					$options           = get_option( 'fullstripe_options' );
					$send_plugin_email = false;
					if ( $options['receiptEmailType'] == 'plugin' ) {
						$send_plugin_email = true;
					}
					$send_receipt = $form_send_receipt && $send_plugin_email;
				}
			}
			if ( $send_receipt ) {
				$vat_percent                 = $wpfsSubscriber->vatPercent;
				$plan_net_setup_fee          = MM_WPFS_Utils::get_setup_fee_for_plan( $stripePlan );
				$setup_fee_gross_composite   = MM_WPFS_Utils::calculateGrossFromNet( $plan_net_setup_fee, $vat_percent );
				$plan_gross_setup_fee        = $setup_fee_gross_composite['gross'];
				$plan_net_amount             = $stripePlan->amount;
				$plan_amount_gross_composite = MM_WPFS_Utils::calculateGrossFromNet( $plan_net_amount, $vat_percent );
				$plan_gross_amount           = $plan_amount_gross_composite['gross'];
				$this->mailer->send_subscription_finished_email_receipt(
					$wpfsSubscriber->email,
					$stripePlan->product->name,
					$stripePlan->currency,
					$plan_net_setup_fee,
					$plan_gross_setup_fee,
					$plan_gross_setup_fee - $plan_net_setup_fee,
					$vat_percent,
					$plan_net_amount,
					$plan_gross_amount,
					$plan_gross_amount - $plan_net_amount,
					$vat_percent,
					$plan_gross_amount + $plan_gross_setup_fee,
					$wpfsSubscriber->name,
					$wpfsSubscriber->name,
					$billing_address,
					$wpfsSubscriber->shippingName,
					$shipping_address,
					$product_name,
					$wpfsSubscriber->stripeSubscriptionID,
					$wpfsSubscriber->formName
				);
			}
		}
	}
}

class MM_WPFS_ChargeCaptured extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_CAPTURED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status
				)
			);
		}
	}
}

class MM_WPFS_ChargeExpired extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_EXPIRED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status,
					'expired'            => true
				)
			);
		}
	}
}

class MM_WPFS_ChargeFailed extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_FAILED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status,
					'failure_code'       => $charge->failure_code,
					'failure_message'    => $charge->failure_message,
				)
			);
		}
	}
}

class MM_WPFS_ChargePending extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_PENDING;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status
				)
			);
		}
	}
}

class MM_WPFS_ChargeRefunded extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_REFUNDED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status,
				)
			);
		}
	}
}

class MM_WPFS_ChargeSucceeded extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_SUCCEEDED;
	}

	protected function process_event( $event ) {
		$charge = $this->get_data_object( $event );
		if ( ! is_null( $charge ) ) {
			$this->db->update_payment_by_event_id(
				$charge->payment_intent,
				array(
					'paid'               => $charge->paid,
					'captured'           => $charge->captured,
					'refunded'           => $charge->refunded,
					'last_charge_status' => $charge->status,
				)
			);
		}
	}
}

class MM_WPFS_ChargeUpdated extends MM_WPFS_EventProcessor {

	/* @var MM_WPFS_Logger */
	private $logger;

	public function __construct( MM_WPFS_Database $db, MM_WPFS_Stripe $stripe, MM_WPFS_Mailer $mailer, MM_WPFS_LoggerService $loggerService ) {
		parent::__construct( $db, $stripe, $mailer, $loggerService );
		$this->logger = $this->loggerService->createWebHookEventHandlerLogger( __CLASS__ );
		// $this->logger->setLevel( MM_WPFS_LoggerService::LEVEL_DEBUG );
	}

	public function get_type() {
		return \StripeWPFS\Event::CHARGE_UPDATED;
	}

	protected function process_event( $event ) {
		// tnagy charge description or metadata updated, nothing to do here
	}
}

class MM_WPFS_CustomerSubscriptionUpdated extends MM_WPFS_EventProcessor {

	public function get_type() {
		return \StripeWPFS\Event::CUSTOMER_SUBSCRIPTION_UPDATED;
	}

	protected function process_event( $event ) {
		$previous_attributes = $this->get_data_previous_attributes( $event );
		if ( ! is_null( $previous_attributes ) ) {
			/** @var \StripeWPFS\Subscription $stripe_subscription */
			$stripe_subscription = $this->get_data_object( $event );
			if ( ! is_null( $stripe_subscription ) ) {
				$subscription = $this->db->find_subscription_by_stripe_subscription_id( $stripe_subscription->id );
				if ( isset( $subscription ) ) {
					if ( array_key_exists( 'quantity', $previous_attributes ) ) {
						$this->db->update_subscriber(
							$subscription->subscriberID,
							array( 'quantity' => $stripe_subscription->quantity )
						);
					}
				}
			}
		}
	}

}