const openPopupInstamojo = () => {
    const paymentElements = document.querySelectorAll('#checkout-payment input.gateway-input');

    if ( ! paymentElements.length ) return;

    paymentElements.forEach( (element) => {
        element.addEventListener('change', (e) => {
            if ( e.target.value === 'instamojo' ) {
                clickShow();
            }
        });
        if( element.checked ) {
            if ( element.value === 'instamojo' ) {
                clickShow();
            }
        }
    });
}


const clickShow = () => {
    const button = document.querySelector('#learn-press-checkout-place-order');

    if ( ! button ) return;

    const submit = async ( btn ) =>{
        try{
            const response = await wp.apiFetch( {
                method: 'POST',
                path: 'lp/instamojo/v1/create-payment',
                data : {
                    'clientID' : LearnPressInstamojo.insta_client_id,
                    'clientSecret' : LearnPressInstamojo.insta_client_secret,
                    'testMode' : LearnPressInstamojo.insta_test_mode,
                },
            } );
            if(btn){
                btn.classList.remove('loading');
            }
            const { redirect , success } = response;

            if ( success === true ) {
                Instamojo.open(redirect);
            } else {
                console.log(response);
            }

        }catch (e) {
            console.log(e);
        }
    }
    button.addEventListener('click', (e) => {
        e.preventDefault();
        button.classList.add('loading');
        submit(button);
    });
}
document.addEventListener('DOMContentLoaded',function(){
    openPopupInstamojo();
});
