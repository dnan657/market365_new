<?php

f_page_title_set(f_translate('Пополнить баланс'));

$GLOBALS['WEB_JSON']['page_json']['html_head'] .= '<script src="https://js.stripe.com/v3/"></script>';

$ads_id = intval($_GET['ads_id'] ?? 0);
$service_type = preg_replace('/[^a-z]/', '', strtolower((string)($_GET['service_type'] ?? '')));
if( $service_type !== 'top' && $service_type !== 'vip' ){
	$service_type = '';
}

$pay_order_ad_title = '';
if( $ads_id > 0 ){
	$atr = f_db_select('SELECT `title` FROM `ads` WHERE `_id` = ' . $ads_id . ' AND `delete_on` = 0 LIMIT 1');
	if( !empty($atr[0]['title']) ){
		$pay_order_ad_title = (string)$atr[0]['title'];
	}
}
$pay_order_service_label = '';
if( $service_type === 'top' ){
	$pay_order_service_label = f_translate('TOP listing (7 days)') . ' — £4.99';
}elseif( $service_type === 'vip' ){
	$pay_order_service_label = f_translate('VIP listing (30 days)') . ' — £9.99';
}

$stripe_pk = trim((string)($GLOBALS['WEB_JSON']['api_json']['stripe_public'] ?? ''));
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$return_base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '');
$return_url = $return_base . f_page_link('user_pays');

?>

<div class="container">
	<div class="head_page">
		<a class="back_head_page btn btn-outline-dark" href="<?php f_echo_html(f_page_link('user_pays')); ?>">
			<i class="bi bi-chevron-left"></i>
		</a>
		<h1 class="title_head_page"><?php f_translate_echo('Оплата'); ?></h1>
	</div>

	<?php if( $ads_id <= 0 || $service_type === '' ){ ?>
		<div class="alert alert-warning mt-3"><?php f_translate_echo('Выберите услугу продвижения на странице объявления.'); ?></div>
		<p><a class="btn btn-outline-primary" href="<?php f_echo_html(f_page_link('user_ads')); ?>"><?php f_translate_echo('Мои объявления'); ?></a></p>
	<?php } elseif( $stripe_pk === '' ){ ?>
		<div class="alert alert-danger mt-3"><?php f_translate_echo('Платежи временно недоступны (не настроен Stripe).'); ?></div>
	<?php } else { ?>
		<div class="alert alert-light border mt-3 mb-0">
			<div class="small text-muted mb-1"><?php f_translate_echo('You are paying for'); ?></div>
			<div class="fw-medium"><?php f_echo_html($pay_order_service_label); ?></div>
			<?php if( $pay_order_ad_title !== '' ){ ?>
				<div class="small mt-2 text-muted"><?php f_translate_echo('Ad'); ?>: <?php f_echo_html($pay_order_ad_title); ?></div>
			<?php } ?>
		</div>
		<div class="row justify-content-center mt-4">
			<div class="col-md-6">
				<div class="card shadow-sm">
					<div class="card-body">
						<div id="pay-loading" class="text-muted"><?php f_translate_echo('Подготовка формы оплаты…'); ?></div>
						<div id="pay-form-wrap" class="d-none">
							<form id="payment-form">
								<div id="payment-element" class="mb-3"></div>
								<button type="submit" id="submit-pay" class="btn btn-primary w-100">
									<span id="button-text"><?php f_translate_echo('Оплатить'); ?></span>
									<span id="spinner-pay" class="d-none spinner-border spinner-border-sm"></span>
								</button>
								<div id="payment-message" class="text-danger small mt-2 d-none"></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
		(function () {
			var adsId = <?php echo (int)$ads_id; ?>;
			var serviceType = <?php echo json_encode($service_type, JSON_UNESCAPED_UNICODE); ?>;
			var stripePk = <?php echo json_encode($stripe_pk, JSON_UNESCAPED_UNICODE); ?>;
			var returnUrl = <?php echo json_encode($return_url, JSON_UNESCAPED_UNICODE); ?>;

			document.addEventListener('DOMContentLoaded', function () {
				var stripe = Stripe(stripePk);
				var elements;
				var clientSecret = '';

				function showMsg(t) {
					var el = document.getElementById('payment-message');
					el.textContent = t || '';
					el.classList.toggle('d-none', !t);
				}

				function setLoading(on) {
					var btn = document.getElementById('submit-pay');
					var sp = document.getElementById('spinner-pay');
					var tx = document.getElementById('button-text');
					btn.disabled = on;
					sp.classList.toggle('d-none', !on);
					tx.classList.toggle('d-none', on);
				}

				f_ajax('pay', 'create_intent', { ads_id: adsId, service_type: serviceType }, function (res) {
					res = res.data || {};
					if (res.error) {
						showMsg(res.error);
						document.getElementById('pay-loading').textContent = '';
						return;
					}
					clientSecret = res.client_secret;
					if (!clientSecret) {
						showMsg('No client secret');
						return;
					}
					elements = stripe.elements({ clientSecret: clientSecret });
					var paymentElement = elements.create('payment');
					paymentElement.mount('#payment-element');
					document.getElementById('pay-loading').classList.add('d-none');
					document.getElementById('pay-form-wrap').classList.remove('d-none');

					document.getElementById('payment-form').addEventListener('submit', function (e) {
						e.preventDefault();
						setLoading(true);
						showMsg('');
						stripe.confirmPayment({
							elements: elements,
							confirmParams: { return_url: returnUrl + '?paid=1' },
							redirect: 'if_required'
						}).then(function (result) {
							setLoading(false);
							if (result.error) {
								showMsg(result.error.message || 'Payment error');
							} else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
								window.location.href = returnUrl + '?paid=1';
							}
						});
					});
				});
			});
		})();
		</script>
	<?php } ?>
</div>
