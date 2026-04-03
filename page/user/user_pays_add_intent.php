<?php

f_page_title_set( f_translate('Платежи') );


$input = json_decode(file_get_contents('php://input'), true);
$amount = $input['amount'] ?? 1000; // Default to $10.00
$currency = $input['currency'] ?? 'usd';

$intent = create_payment_intent($amount, $currency);

if (isset($intent['client_secret'])) {
    echo json_encode(['clientSecret' => $intent['client_secret']]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create PaymentIntent']);
}

function create_payment_intent($amount, $currency) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'amount' => $amount * 100, // Stripe expects amount in cents
        'currency' => $currency,
        'payment_method_types[]' => 'card',
        'payment_method_types[]' => 'google_pay',
        // Add other payment methods as needed
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, $GLOBALS['WEB_JSON']['api_json']['stripe_secret'] . ":");
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

?>


<div class="container">
	
	<div class="head_page">
	
		<a class="back_head_page  btn btn-outline-dark"  back_page_link  href="<?php f_echo_html( $is_admin ? f_page_link('admin_pays_list') : '/');  ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		
		<h1 class="title_head_page">
			<?php f_translate_echo('Платежи'); ?>
			<?php f_echo_html( $is_admin ? ' - #' . f_num_encode( $item_json['_id'] ) : '') ?>
		</h1>
		
	</div>
	
	
	<div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Payment Status</h3>
                        <div id="payment-status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
    const stripe = Stripe('<?php echo $GLOBALS['WEB_JSON']['api_json']['stripe_public']; ?>');

    const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );

    stripe.retrievePaymentIntent(clientSecret).then(({paymentIntent}) => {
        const message = document.querySelector('#payment-status');

        switch (paymentIntent.status) {
            case "succeeded":
                message.innerHTML = '<div class="alert alert-success">Payment succeeded!</div>';
                break;
            case "processing":
                message.innerHTML = '<div class="alert alert-info">Your payment is processing.</div>';
                break;
            case "requires_payment_method":
                message.innerHTML = '<div class="alert alert-warning">Your payment was not successful, please try again.</div>';
                break;
            default:
                message.innerHTML = '<div class="alert alert-danger">Something went wrong.</div>';
                break;
        }
    });
    </script>
	
</div>

