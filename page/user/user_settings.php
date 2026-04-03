<?php

$item_json = f_user_get();
$uid = (int)$item_json['_id'];

$avatar_rows = f_db_select(
	"SELECT * FROM `upload` WHERE `item_table` = 'user' AND `item_id` = " . intval($uid)
	. " AND (`_delete_on` IS NULL OR `_delete_on` = 0) ORDER BY `_id` DESC LIMIT 1"
);
$avatar_url = '';
if (!empty($avatar_rows[0]) && !empty($avatar_rows[0]['img_jpg_path'])) {
	$p = $GLOBALS['WEB_JSON']['dir_upload_img'] . $avatar_rows[0]['img_jpg_path'];
	$parts = explode('public_html', $p);
	$avatar_url = 'https://' . $_SERVER['HTTP_HOST'] . ($parts[1] ?? '');
}

f_page_title_set(f_translate('Account settings'));

?>

<div class="container">
	<?php f_template('user_info_box'); ?>

	<div class="row">
		<div class="col-md-6">
			<h2 class="h5 mb-3"><?php f_translate_echo('Profile photo'); ?></h2>
			<div class="mb-3">
				<?php if ($avatar_url !== '') { ?>
					<img src="<?php f_echo_html($avatar_url); ?>" alt="" class="rounded mb-3" style="max-width:200px;max-height:200px;object-fit:cover;">
				<?php } else { ?>
					<p class="text-muted"><?php f_translate_echo('No photo yet'); ?></p>
				<?php } ?>
			</div>
			<div class="mb-3">
				<input type="file" id="user_avatar_input" accept="image/jpeg,image/png" class="form-control">
			</div>
		</div>

		<div class="col-md-6" form_group="user_settings_pass">
			<h2 class="h5 mb-3"><?php f_translate_echo('Change password'); ?></h2>
			<input type="hidden" field_name="save_scope" value="password" />
			<div class="mb-3">
				<label class="form-label"><?php f_translate_echo('Current password'); ?></label>
				<input type="password" class="form-control" field_name="password_old" autocomplete="current-password" />
			</div>
			<div class="mb-3">
				<label class="form-label"><?php f_translate_echo('New password'); ?></label>
				<input type="password" class="form-control" field_name="password" id="user_new_pass" autocomplete="new-password" />
			</div>
			<div class="mb-3">
				<label class="form-label"><?php f_translate_echo('Confirm new password'); ?></label>
				<input type="password" class="form-control" id="user_new_pass2" autocomplete="new-password" />
			</div>
			<div class="btn btn-lg btn-dark" field_btn="save_pass"><?php f_translate_echo('Save password'); ?></div>
		</div>
	</div>

	<p class="mt-4 small text-muted">
		<a href="<?php f_page_link_echo('user'); ?>"><?php f_translate_echo('Back to profile'); ?></a>
	</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var jq_btn = $('[field_btn="save_pass"]');
	var jq_in = $('#user_avatar_input');

	jq_btn.on('click', function () {
		var p1 = $('#user_new_pass').val();
		var p2 = $('#user_new_pass2').val();
		if (p1.length < 2) {
			toastr.error(<?php echo json_encode(f_translate('Enter a new password')); ?>);
			return;
		}
		if (p1 !== p2) {
			toastr.error(<?php echo json_encode(f_translate('Passwords do not match')); ?>);
			return;
		}
		jq_btn.addClass('btn_sending');
		var form_json = f_form_get($('[form_group="user_settings_pass"]'));
		f_ajax('user', 'save', form_json, function (data) {
			jq_btn.removeClass('btn_sending');
			if (data['data'] && data['data']['error']) {
				toastr.error(data['data']['error']);
			} else {
				toastr.success(<?php echo json_encode(f_translate('Saved')); ?>);
				location.reload();
			}
		});
	});

	jq_in.on('change', function () {
		var file = this.files[0];
		if (!file) return;
		if (!['image/jpeg', 'image/png'].includes(file.type)) {
			toastr.error(<?php echo json_encode(f_translate('Only JPEG or PNG')); ?>);
			return;
		}
		var form_json = {
			item_id: <?php echo (int)$uid; ?>,
			item_table: 'user',
			item_type: 'avatar',
			file: file
		};
		f_ajax('upload', 'file', form_json, function (data) {
			jq_in.val('');
			if (data['data'] && data['data']['error']) {
				toastr.error(data['data']['error']);
			} else {
				toastr.success(<?php echo json_encode(f_translate('Photo uploaded')); ?>);
				location.reload();
			}
		});
	});
});
</script>
