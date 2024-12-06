jQuery( function( $ ) {
	const checkout = $( '#learn-press-checkout' ),
		_input_field = checkout.find( 'input[name^="learn-press-2checkout-payment"]' ),
		_select_field = checkout.find( 'select[name^="learn-press-2checkout-payment"]' );
	if ( checkout.find( '#payment_method_2checkout' ).is( ':checked' ) ) {
		_input_field.prop( 'disabled', false );
		_select_field.prop( 'disabled', false );
	}

	checkout.find( 'input[type=radio][name="payment_method"]' ).on( 'click', function() {
		if ( this.value === '2checkout' ) {
			_input_field.prop( 'disabled', false );
			_select_field.prop( 'disabled', false );
		} else {
			_input_field.prop( 'disabled', true );
			_select_field.prop( 'disabled', true );
		}
	} );
} );
