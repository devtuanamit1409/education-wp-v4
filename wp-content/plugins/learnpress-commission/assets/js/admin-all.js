jQuery(document).ready(function ($) {

	$('#learn_press_commission_enable_paypal_withdrawal_method').change(function(){

		if($('#learn_press_commission_enable_paypal_withdrawal_method').is(":checked")){

			$('#learn_press_commission_enable_paypal_sandbox_mode').removeClass('off');
			$('#learn_press_commission_paypal_app_client_id').removeClass('off');
			$('#learn_press_commission_paypal_app_secret').removeClass('off');

		}else{

			$('#learn_press_commission_enable_paypal_sandbox_mode').addClass('off');
			$('#learn_press_commission_paypal_app_client_id').addClass('off');
			$('#learn_press_commission_paypal_app_secret').addClass('off');

		}

	});

});
