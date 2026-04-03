

<!-- Модалка - Автоцон -->
<div class="modal fade" id="modal_autocone" tabindex="-1" aria-labelledby="modal_autocone_label" aria-hidden="true">
	<div class="modal-dialog  modal-dialog-centered  modal-lg">
		<div class="modal-content" style="height: 900px; max-height: 90dvh; overflow: hidden;">
			<div class="modal-header">
				<h1 class="modal-title" id="modal_autocone_label">
					<?php f_echo_html( f_translate('Бронирование очереди в АвтоЦОН') ); ?>
				</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body  p-0">
				<iframe loading="lazy" referrerpolicy="no-referrer" lazy_src="/redirect?url_js=https://booking.gov4c.kz/reservation" width="100%" height="100%"></iframe>
			</div>
			<div class="modal-footer  d-none">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
					<?php f_echo_html( f_translate('Закрыть') ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Модалка - Трансляция из АвтоЦОНа -->
<div class="modal fade" id="modal_stream" tabindex="-1" aria-labelledby="modal_autocone_label" aria-hidden="true">
	<div class="modal-dialog  modal-dialog-centered  modal-lg">
		<div class="modal-content" style="height: 900px; max-height: 90dvh; overflow: hidden;">
			<div class="modal-header">
				<h1 class="modal-title" id="modal_autocone_label">
					<?php f_echo_html( f_translate('Трансляция из АвтоЦОНа') ); ?>
				</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body  p-0">
				<iframe loading="lazy" referrerpolicy="no-referrer" lazy_src="https://booking.gov4c.kz/stream" width="100%" height="100%"></iframe>
			</div>
		</div>
	</div>
</div>




<script>

document.addEventListener("DOMContentLoaded", (event) => {
	
	$('[group="modal_etap"]').on('hidden.bs.modal', function (e) {
		$('#modal_autodrome').modal('show')
	});

	$('.modal').on('shown.bs.modal', function (e) {
		$(this).find('[lazy_src]').each(function(i, elem){
			$(elem).attr('src', $(elem).attr('lazy_src'))
			$(elem).removeAttr('lazy_src')
		})
	});

	$('#modal_autodrome').on('shown.bs.modal', function (e) {
		$('#modal_autodrome  .select_city').trigger('change')
	});

	$('#modal_autodrome  .select_city').on('change', function(){
		$('#modal_autodrome [city]').addClass('d-none');
		$('#modal_autodrome  [city="'+ $(this).val() + '"]').removeClass('d-none');
	})
	
})

</script>