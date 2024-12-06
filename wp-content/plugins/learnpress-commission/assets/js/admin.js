jQuery(document).ready(function ($) {
    
    let statusWD = 'pending';
    $('#lp_withdraw_status_select_box').on('change', function(e){
        statusWD = $(this).val();
        $('#lp_input_status').val(statusWD);
    });
    

	$('#lp_reject').on('click', function (e) {
		e.preventDefault();
		var $form = $('#post');
		var $status_input = $('#lp_input_status');
		var reject = $status_input.data('reject');
		$status_input.val(reject);
		$form.submit();
	});

	$('#lp_paid').on('click', function (e) {
		e.preventDefault();
		var $form = $('#post');
		var $status_input = $('#lp_input_status');
		$status_input.val(statusWD);
		$form.submit();
	});

	$('#lp_withdraw_apply_btn').click(function(event){
		event.preventDefault();
		var $form = $('#post');
		$form.submit();
	});

});
