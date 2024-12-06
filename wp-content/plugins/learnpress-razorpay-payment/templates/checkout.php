<?php get_header();



?>


<button id="rzp-button1">Pay</button>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  // const Razorpay = require('razorpay');
  // var instance = new Razorpay({ key:'rzp_test_oTzjSpqQcvMK7s', key_secret: 'KboDXv4HT308t7rUvsRh4qUy' })
  // // console.log(instance);
  // var options = {
  //   amount: 50000,  // amount in the smallest currency unit
  //   currency: "INR",
  //   receipt: "order_rcptid_11"
  // };
  // instance.orders.create(options, function(err, order) {
  //   console.log(order);
  // });
</script>
<!-- <script>
var options = {
	"key": "rzp_test_b5lGRkj3x29B4m", // Enter the Key ID generated from the Dashboard
	"amount": "50000", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
	"currency": "INR",
	"name": "Acme Corp",
	"description": "Test Transaction",
	"image": "https://example.com/your_logo",
	// "order_id": "order_IluGWxBm9U8zJ8", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
	"callback_url": "https://google.com/",
	"prefill": {
		"name": "Gaurav Kumar",
		"email": "gaurav.kumar@example.com",
		"contact": "9000090000"
	},
	"notes": {
		"address": "Razorpay Corporate Office"
	},
	"theme": {
		"color": "#3399cc"
	}
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button1').onclick = function(e){
	rzp1.open();
	e.preventDefault();
}
</script> -->

<?php get_footer(); ?>
