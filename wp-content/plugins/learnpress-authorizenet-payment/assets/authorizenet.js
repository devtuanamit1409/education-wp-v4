jQuery( function( $ ) {
	var _checkout = $('#learn-press-checkout'),
		_input_field = _checkout.find('input[name^="learn-press-authorizenet-payment"]'),
		_select_field = _checkout.find('select[name^="learn-press-authorizenet-payment"]');
	if (_checkout.find('#payment_method_authorizenet').is(':checked')) {
		_input_field.prop('disabled', false);
		_select_field.prop('disabled', false);
	}

	_checkout.find('input[type=radio][name="payment_method"]').on( 'click', function () {
		if (this.value === 'authorizenet') {
			_input_field.prop('disabled', false);
			_select_field.prop('disabled', false);
		} else {
			_input_field.prop('disabled', true);
			_select_field.prop('disabled', true);
		}
	});
} );
