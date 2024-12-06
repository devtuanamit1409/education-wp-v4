<?php
/**
 * https://docs.stripe.com/currencies
 * Rules for calculate the amount of a currency.
 */
return apply_filters(
	'learn-press/gateway-payment/stripe-currency-rules',
	[
		'zero-decimal'  => [ 'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF' ],
		'three-decimal' => [ 'TND', 'OMR', 'KWD', 'JOD', 'BHD' ],
		'special-case'  => [ 'ISK', 'HUF', 'TWD', 'UGX' ],
	]
);
