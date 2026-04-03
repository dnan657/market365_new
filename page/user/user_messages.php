<?php

$title_page = f_translate('Messages');
f_page_title_set($title_page);

?>

<style>
.user_messages_layout { min-height: 55vh; }
.user_messages_list { max-height: 60vh; overflow-y: auto; border: 1px solid var(--v_c_border); border-radius: var(--v_radius); }
.user_messages_list .list-group-item { cursor: pointer; }
.user_messages_list .list-group-item.active { background: var(--v_c_dark); border-color: var(--v_c_dark); }
.user_messages_thread { max-height: 45vh; overflow-y: auto; border: 1px solid var(--v_c_border); border-radius: var(--v_radius); padding: var(--v_p_15); background: var(--v_c_white); }
.msg_bubble { max-width: 85%; padding: 8px 12px; border-radius: 12px; margin-bottom: 8px; clear: both; }
.msg_bubble.mine { background: #fff3cd; float: right; text-align: right; }
.msg_bubble.theirs { background: #f1f3f5; float: left; }
.user_messages_thread:after { content: ""; display: table; clear: both; }
.chat_thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
</style>

<div class="container user_messages_layout">
	<?php f_template('user_info_box'); ?>

	<div class="row g-3 mt-1">
		<div class="col-lg-4">
			<h2 class="h5 mb-3"><?php f_translate_echo('Dialogs'); ?></h2>
			<div class="user_messages_list list-group" id="user_messages_chat_list"></div>
			<div class="text-muted small mt-2 d-none" id="user_messages_empty"><?php f_translate_echo('No messages yet'); ?></div>
		</div>
		<div class="col-lg-8">
			<div id="user_messages_placeholder" class="text-muted py-5 text-center">
				<?php f_translate_echo('Select a chat'); ?>
			</div>
			<div id="user_messages_panel" class="d-none">
				<div class="card mb-3">
					<div class="card-body d-flex align-items-center gap-3 flex-wrap">
						<img src="/public/ad_default.jpg" alt="" class="chat_thumb" id="user_messages_ad_thumb" />
						<div class="flex-grow-1">
							<div class="fw-semibold" id="user_messages_ad_title"></div>
							<a href="#" class="small" id="user_messages_ad_link"><?php f_translate_echo('Open ad'); ?></a>
						</div>
					</div>
				</div>
				<div class="user_messages_thread" id="user_messages_thread"></div>
				<div class="mt-3">
					<label class="form-label"><?php f_translate_echo('Your message'); ?></label>
					<textarea class="form-control" id="user_messages_input" rows="3"></textarea>
					<button type="button" class="btn btn-warning mt-2" id="user_messages_send"><?php f_translate_echo('Send'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function () {
	var gl_chat_id = 0;
	var gl_poll_timer = null;

	function esc(s) {
		return $('<div>').text(s == null ? '' : String(s)).html();
	}

	function load_list() {
		f_ajax('chat', 'get_list', {}, function (res) {
			res = res.data;
			if (res.error) {
				return;
			}
			var $list = $('#user_messages_chat_list');
			$list.empty();
			var arr = res.arr_chat || [];
			if (!arr.length) {
				$('#user_messages_empty').removeClass('d-none');
				return;
			}
			$('#user_messages_empty').addClass('d-none');
			arr.forEach(function (c) {
				var active = gl_chat_id === c.chat_id;
				var un = c.unread_count > 0 ? $('<span class="badge bg-danger ms-1"></span>').text(c.unread_count) : null;
				var $it = $('<div class="list-group-item list-group-item-action"></div>');
				if (active) {
					$it.addClass('active');
				}
				$it.data('chat-id', c.chat_id).data('ad-thumb', c.ads_thumb || '');
				var $title = $('<div class="fw-semibold text-truncate"></div>').text(c.ads_title || '');
				if (un) {
					$title.append(un);
				}
				var $row = $('<div class="d-flex gap-2 align-items-center"></div>');
				$row.append($('<img class="chat_thumb" alt=""/>').attr('src', c.ads_thumb || '/public/ad_default.jpg'));
				$row.append($('<div class="flex-grow-1 overflow-hidden"></div>').append($title).append(
					$('<div class="small text-muted text-truncate"></div>').text((c.peer_name || '') + ': ' + (c.last_message_text || ''))
				));
				$it.append($row);
				$list.append($it);
			});
		});
	}

	function render_messages(arr) {
		var $th = $('#user_messages_thread');
		$th.empty();
		(arr || []).forEach(function (m) {
			var cl = m.is_mine ? 'mine' : 'theirs';
			var row = '<div class="msg_bubble ' + cl + '"><div class="small text-muted">' + esc(m._create_date) + '</div><div>' + esc(m.message_text) + '</div></div>';
			$th.append(row);
		});
		$th.scrollTop($th[0].scrollHeight);
	}

	function open_chat(chat_id) {
		gl_chat_id = chat_id;
		f_ajax('chat', 'get_messages', { chat_id: chat_id }, function (res) {
			res = res.data;
			if (res.error) {
				return;
			}
			$('#user_messages_placeholder').addClass('d-none');
			$('#user_messages_panel').removeClass('d-none');
			var ch = res.chat || {};
			$('#user_messages_ad_title').text(ch.ads_title || '');
			$('#user_messages_ad_link').attr('href', ch.html_link_ad || '#');
			if (ch.ads_thumb) {
				$('#user_messages_ad_thumb').attr('src', ch.ads_thumb);
			}
			$('#user_messages_send').data('chat-id', chat_id);
			render_messages(res.arr_message);
			load_list();
			if (typeof window.f_nav_sync_chat_unread === 'function') {
				window.f_nav_sync_chat_unread();
			}
		});
	}

	function poll_messages() {
		if (!gl_chat_id) {
			return;
		}
		f_ajax('chat', 'get_messages', { chat_id: gl_chat_id }, function (res) {
			res = res.data;
			if (!res.error && res.arr_message) {
				render_messages(res.arr_message);
			}
		});
	}

	$(document).on('click', '#user_messages_chat_list .list-group-item', function () {
		var id = parseInt($(this).data('chat-id'), 10);
		if (!id) {
			return;
		}
		var th = $(this).data('ad-thumb') || '';
		if (th) {
			$('#user_messages_ad_thumb').attr('src', th);
		}
		$('#user_messages_chat_list .list-group-item').removeClass('active');
		$(this).addClass('active');
		open_chat(id);
	});

	$('#user_messages_send').on('click', function () {
		var cid = gl_chat_id;
		var text = $('#user_messages_input').val().trim();
		if (!cid || !text) {
			return;
		}
		f_ajax('chat', 'send', { chat_id: cid, message_text: text }, function (res) {
			res = res.data;
			if (res.error) {
				return;
			}
			$('#user_messages_input').val('');
			poll_messages();
			load_list();
		});
	});

	$(function () {
		load_list();
		gl_poll_timer = setInterval(poll_messages, 8000);
	});
})();
</script>
