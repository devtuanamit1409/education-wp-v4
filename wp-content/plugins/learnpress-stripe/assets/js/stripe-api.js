/**
 * Stripe js handle payment with form mount from Lib Stripe JS
 *
 * @since 4.0.2
 * @version 1.0.0
 */
import { loadStripe } from '@stripe/stripe-js';
import { lpAjaxParseJsonOld } from './utils';

const lpStripe = ( () => {
	let Stripe;
	let elCheckoutForm;
	let elStripeForm, elPageCheckout;
	const idStripeForm = 'lp-stripe-payment-form';
	const idBtnPlaceHolder = 'learn-press-checkout-place-order';
	const idPageCheckout = 'learn-press-checkout';
	let urlHandle;
	let mountStripeDone = false;
	let errorMessage;

	const fetchAPI = ( args, callBack = {} ) => {
		fetch( urlHandle, {
			method: 'POST',
			/*headers: {
				'X-WP-Nonce': lpGlobalSettings.nonce,
			},*/
			body: args,
		} )
			.then( ( res ) => res.text() )
			.then( ( data ) => {
				data = lpAjaxParseJsonOld( data );
				callBack.success( data );
			} )
			.finally( () => {
				callBack.completed();
			} )
			.catch( ( err ) => callBack.error( err ) );
	};

	/**
	 * Load Stripe JS via publish key
	 * then mount form Stripe
	 *
	 * @return {Promise<void>}
	 */
	const mountFormStripe = async () => {
		try {
			Stripe = await loadStripe( lpStripeSetting.publish_key || '' );

			// Display form Stripe
			const options = {
				clientSecret: lpStripeSetting.publishableKey || '',
				appearance: {},
			};
			elStripeForm = Stripe.elements( options );

			// Create and mount the Payment Element
			const paymentElement = elStripeForm.create( 'payment' );
			paymentElement.on( 'loaderror', function( event ) {
				errorMessage = event.error.message;
			} );
			paymentElement.on( 'ready', function( event ) {
				mountStripeDone = true;
			} );
			paymentElement.mount( `#${ idStripeForm }` );
		} catch ( error ) {
			errorMessage = error.message;
		}
	};

	/**
	 * Confirm the PaymentIntent with url has parameter "payment_intent"
	 *
	 * https://stripe.com/docs/js/payment_intents/confirm_payment
	 *
	 * @param  return_url
	 * @param  callBack
	 */
	const confirmStripePayment = ( return_url, callBack ) => {
		if ( typeof Stripe === 'undefined' ) {
			console.log( 'Stripe is not defined' );
		}
		if ( typeof elStripeForm === 'undefined' ) {
			console.log( 'Stripe form not mounted' );
		}

		Stripe.confirmPayment( {
			elements: elStripeForm,
			confirmParams: {
				return_url,
			},
		} ).then( function( result ) {
			callBack( result );
		} );
	};
	const setMessageError = ( mess_text ) => {
		const elMessage = `<div class="learn-press-message error">${ mess_text }</div>`;
		elPageCheckout.insertAdjacentHTML( 'afterbegin', elMessage );
		elPageCheckout.scrollIntoView( {
			behavior: 'smooth',
		} );
	};

	return {
		init: async () => {
			if ( 'undefined' === typeof lpStripeSetting ) {
				console.log( 'LP Stripe Setting is not defined' );
			}

			if ( 'undefined' === typeof lpStripeSetting.payment_stripe_via_iframe ) {
				return;
			}

			if ( 'undefined' !== typeof lpStripeSetting.error ) {
				const elStripeMethod = document.querySelector( '.payment_method_stripe' );
				if ( elStripeMethod ) {
					elStripeMethod.insertAdjacentHTML(
						'afterbegin',
						`<span class="error" style="color: #e01618">Error: ${ lpStripeSetting.error }</span>`
					);
				}
				return;
			}

			elPageCheckout = document.getElementById( idPageCheckout );
			elCheckoutForm = document.getElementById( 'learn-press-checkout-form' );
			if ( ! elCheckoutForm ) {
				return;
			}

			await mountFormStripe();
		},
		events: () => {
			document.addEventListener( 'click', function( e ) {
				const target = e.target;

				if ( target.id === idBtnPlaceHolder ) {
					const elBtnPlaceHolder = target;
					elCheckoutForm = document.getElementById( 'learn-press-checkout-form' );
					if ( ! elCheckoutForm ) {
						return;
					}

					const formData = new FormData( elCheckoutForm );
					if ( formData.get( 'payment_method' ) !== 'stripe' ) {
						return;
					}
					e.preventDefault();

					if ( ! elPageCheckout ) {
						console.error( 'Page checkout is not defined' );
						return;
					}

					const elMes = elPageCheckout.querySelectorAll( '.learn-press-message.error' );
					if ( elMes.length > 0 ) {
						elMes.forEach( ( el ) => {
							el.remove();
						} );
					}

					if ( 'undefined' !== typeof errorMessage ) {
						setMessageError( errorMessage );
						return;
					}

					if ( 'undefined' === typeof lpStripeSetting.publishableKey ) {
						setMessageError( 'Stripe Publish Key is generate failed, please check again config key!' );
						return;
					}

					if ( ! mountStripeDone ) {
						setMessageError( 'Stripe payment not load done. Please wait!' );

						return;
					}

					/**
					 * Submit form Stripe to check valid fields
					 * https://stripe.com/docs/js/elements/submit
					 *
					 * When check all fields is valid, will submit checkout to create order,
					 * then pass url lp order to confirmStripePayment() to call Stripe API,
					 * Stripe API will return url redirect has parameter "payment_intent",
					 * server will catch it and check if payment_intent is valid, LP Order will set to Complete
					 */
					elStripeForm.submit()
						.then( function( result ) {
							if ( 'undefined' !== typeof result.error ) {
								return;
							}

							elBtnPlaceHolder.classList.add( 'loading' );
							elBtnPlaceHolder.disabled = true;
							elBtnPlaceHolder.innerText = lpCheckoutSettings.i18n_processing;

							urlHandle = new URL( lpCheckoutSettings.ajaxurl );
							urlHandle.searchParams.set( 'lp-ajax', 'checkout' );

							fetchAPI( formData, {
								before: () => {},
								success: ( res ) => {
									if ( 'fail' === res.result ) {
										elBtnPlaceHolder.classList.remove( 'loading' );
										elBtnPlaceHolder.disabled = false;
										elBtnPlaceHolder.innerText = lpCheckoutSettings.i18n_place_order;
										setMessageError( res.messages );
									} else if ( 'processing' === res.result ) {
										elBtnPlaceHolder.innerText = lpCheckoutSettings.i18n_redirecting;
										confirmStripePayment( res.redirect, ( result ) => {
											console.log( result );
											if ( result.error ) {
												elBtnPlaceHolder.classList.remove( 'loading' );
												elBtnPlaceHolder.disabled = false;
												elBtnPlaceHolder.innerText = lpCheckoutSettings.i18n_place_order;
												setMessageError( result.error.message );
											}
										} );
									}
								},
								error: ( error ) => {},
								completed: () => {},
							} );
						} );
				}
			} );
		},
	};
} )();

document.addEventListener( 'DOMContentLoaded', () => {
	lpStripe.init().then( () => {} );
} );

lpStripe.events();
