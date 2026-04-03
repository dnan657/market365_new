<?php

$title_page = f_translate('Payments');
f_page_title_set($title_page);

$paid_ok = isset($_GET['paid']) && $_GET['paid'] === '1';

?>

<div class="container">
	<?php f_template('user_info_box'); ?>

	<?php if( $paid_ok ){ ?>
		<div class="alert alert-success mt-3"><?php f_translate_echo('Платёж принят. Если услуга не активировалась сразу, подождите несколько секунд.'); ?></div>
	<?php } ?>

	<h2 class="h4 mt-3 mb-3"><?php f_translate_echo('История платежей'); ?></h2>
	<p class="mb-3 d-flex flex-wrap gap-2">
		<a class="btn btn-primary btn-sm" href="<?php f_echo_html(f_page_link('user_ads')); ?>"><?php f_translate_echo('Promote an ad'); ?></a>
		<a class="btn btn-outline-primary btn-sm" href="<?php f_echo_html(f_page_link('user_ads')); ?>"><?php f_translate_echo('Мои объявления'); ?></a>
	</p>

	<div class="table-responsive">
		<table class="table table-sm table-striped align-middle" id="user_pays_table">
			<thead>
				<tr>
					<th><?php f_translate_echo('Дата'); ?></th>
					<th><?php f_translate_echo('Услуга'); ?></th>
					<th><?php f_translate_echo('Сумма'); ?></th>
					<th><?php f_translate_echo('Статус'); ?></th>
				</tr>
			</thead>
			<tbody id="user_pays_tbody"></tbody>
		</table>
	</div>
	<p class="text-muted d-none mt-2" id="user_pays_empty"><?php f_translate_echo('Пока нет платежей'); ?></p>
</div>

<script>
$(function () {
	f_ajax('pay', 'get_list', {}, function (res) {
		res = res.data || {};
		var $tb = $('#user_pays_tbody');
		$tb.empty();
		if (res.error) {
			$('#user_pays_empty').removeClass('d-none').text(res.error);
			return;
		}
		var arr = res.arr_txn || [];
		if (!arr.length) {
			$('#user_pays_empty').removeClass('d-none');
			$('#user_pays_table').addClass('d-none');
			return;
		}
		$('#user_pays_empty').addClass('d-none');
		var statusBadge = function (st) {
			var s = (st || '').toLowerCase();
			var map = {
				'success': ['bg-success', 'Active'],
				'pending': ['bg-warning text-dark', 'Pending'],
				'fail': ['bg-danger', 'Failed'],
				'failed': ['bg-danger', 'Failed']
			};
			var m = map[s] || ['bg-secondary', st || '—'];
			return $('<span class="badge ' + m[0] + '"></span>').text(m[1]);
		};
		arr.forEach(function (row) {
			var tr = $('<tr></tr>');
			tr.append($('<td></td>').text(row.html_date || ''));
			tr.append($('<td></td>').text(row.service_type || ''));
			tr.append($('<td></td>').text(row.html_amount || ''));
			tr.append($('<td></td>').append(statusBadge(row.status)));
			$tb.append(tr);
		});
	});
});
</script>
