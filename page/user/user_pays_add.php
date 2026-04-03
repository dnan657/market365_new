<?php

f_page_title_set( f_translate('Пополнить баланс') );

$GLOBALS['WEB_JSON']['page_json']['html_head'] .= '<script src="https://js.stripe.com/v3/"></script>';

$pay_amount = 10;
$pay_currency = 'usd';






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


<style>
#form_payment {
	background-color: #fff;
	padding: 20px;
	border-radius: 5px;
	box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
	max-width: 500px;
	margin: 0 auto;
}
label {
	display: block;
	margin-bottom: 5px;
}
#card-element {
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 4px;
	margin-bottom: 15px;
}
button {
	background-color: #4CAF50;
	color: white;
	padding: 10px 15px;
	border: none;
	border-radius: 4px;
	cursor: pointer;
	font-size: 16px;
}
button:hover {
	background-color: #45a049;
}
</style>

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
                        <h3 class="card-title text-center mb-4">Payment Form</h3>
                        <form id="payment-form">
                            <div id="payment-element">
                                <!-- Stripe Payment Element will be inserted here -->
                            </div>
                            <button id="submit" class="btn btn-primary w-100">
                                <div class="spinner hidden" id="spinner"></div>
                                <span id="button-text">Pay now</span>
                            </button>
                            <div id="payment-message" class="hidden"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const stripe = Stripe('<?php echo htmlspecialchars($GLOBALS['WEB_JSON']['api_json']['stripe_public'], ENT_QUOTES, 'UTF-8'); ?>');

    let elements;

    initialize();
    checkStatus();

    document
      .querySelector("#payment-form")
      .addEventListener("submit", handleSubmit);

    async function initialize() {
      const { clientSecret } = await fetch("create_payment_intent.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount: 1000, currency: 'usd' }),
      }).then((r) => r.json());

      elements = stripe.elements({ clientSecret });

      const paymentElement = elements.create("payment");
      paymentElement.mount("#payment-element");
    }

    async function handleSubmit(e) {
      e.preventDefault();
      setLoading(true);

      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: "https://your-domain.com/payment_complete.php",
        },
      });

      if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
      } else {
        showMessage("An unexpected error occurred.");
      }

      setLoading(false);
    }

    async function checkStatus() {
      const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
      );

      if (!clientSecret) {
        return;
      }

      const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

      switch (paymentIntent.status) {
        case "succeeded":
          showMessage("Payment succeeded!");
          break;
        case "processing":
          showMessage("Your payment is processing.");
          break;
        case "requires_payment_method":
          showMessage("Your payment was not successful, please try again.");
          break;
        default:
          showMessage("Something went wrong.");
          break;
      }
    }

    function showMessage(messageText) {
      const messageContainer = document.querySelector("#payment-message");

      messageContainer.classList.remove("hidden");
      messageContainer.textContent = messageText;

      setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
      }, 4000);
    }

    function setLoading(isLoading) {
      if (isLoading) {
        document.querySelector("#submit").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
      } else {
        document.querySelector("#submit").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
      }
    }
    </script>
	
</div>

