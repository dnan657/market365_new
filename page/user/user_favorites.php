<?php

$title_page = f_translate('Favorites');
f_page_title_set($title_page);

?>

<div class="container">
	<?php f_template('user_info_box'); ?>

	<h2 class="h4 mt-3 mb-3"><?php f_translate_echo('Saved ads'); ?></h2>
	<div class="row g-3" id="user_favorites_grid"></div>
	<p class="text-muted d-none mt-3" id="user_favorites_empty"><?php f_translate_echo('No favorites yet'); ?></p>
</div>

<script>
var gl_fav_labels = {
	open: <?php echo json_encode(f_translate('Open'), JSON_UNESCAPED_UNICODE); ?>,
	remove: <?php echo json_encode(f_translate('Remove'), JSON_UNESCAPED_UNICODE); ?>
};
$(function () {
	f_ajax('favorite', 'get_list', {}, function (res) {
		res = res.data;
		if (res.error) {
			return;
		}
		var arr = res.arr_item || [];
		var $g = $('#user_favorites_grid');
		$g.empty();
		if (!arr.length) {
			$('#user_favorites_empty').removeClass('d-none');
			return;
		}
		$('#user_favorites_empty').addClass('d-none');
		arr.forEach(function (item) {
			var adId = item.ads_id || 0;
			var card = $('<div class="col-md-6 col-lg-4"></div>');
			var inner = $('<div class="card h-100 shadow-sm"></div>');
			var img = $('<img class="card-img-top" style="height:180px;object-fit:cover;" alt=""/>').attr('src', item.html_img_src || '/public/ad_default.jpg');
			var body = $('<div class="card-body d-flex flex-column"></div>');
			body.append($('<h3 class="h6 card-title"></h3>').text(item.title || ''));
			body.append($('<div class="text-muted small mb-2"></div>').text(item.html_price || ''));
			body.append($('<div class="text-muted small mb-2"></div>').text(item.html_city || ''));
			var btnRow = $('<div class="mt-auto d-flex gap-2 flex-wrap"></div>');
			btnRow.append($('<a class="btn btn-outline-primary btn-sm"></a>').attr('href', item.html_link_ad || '#').text(gl_fav_labels.open));
			var $remove = $('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text(gl_fav_labels.remove);
			$remove.on('click', function () {
				if (!adId) {
					return;
				}
				f_ajax('favorite', 'toggle', { ads_id: adId }, function (r2) {
					r2 = r2.data;
					if (r2.error) {
						return;
					}
					card.remove();
					if (!$g.children().length) {
						$('#user_favorites_empty').removeClass('d-none');
					}
				});
			});
			btnRow.append($remove);
			body.append(btnRow);
			inner.append(img).append(body);
			card.append(inner);
			$g.append(card);
		});
	});
});
</script>
