

<?php
/*
//Подключаем Google Recaptcha v3 
<script src="https://www.google.com/recaptcha/api.js?render=<?php f_echo_html($GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v3_public']); ?>"></script>


<script>
grecaptcha.ready(function() {
	grecaptcha.execute('<?php f_echo_html($GLOBALS['WEB_JSON']['api_json']['google_recaptcha_v3_public']); ?>', {action: 'submit'}).then(function(token) {
		$('[name=g_recaptcha]').val(token);
	});
});
</script>

*/
?>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>


<!-- Модальное окно для карты Leaflet -->
<div class="modal fade" id="modal_gps" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog  modal-lg  modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body  p-0">
				<!-- Контейнер для карты Leaflet -->
				<div id="map" style="height: 400px;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php f_translate_echo( "Отмена" ); ?></button>
				<button type="button" class="btn btn-primary  btn_ready"><?php f_translate_echo( "Готово" ); ?></button>
			</div>
		</div>
	</div>
</div>

<script>
let gl_modal_gps_coordinates; // Для хранения выбранных координат
let gl_modal_gps_marker; // Для хранения маркера на карте
let jq_input_modal_gps; // Для хранения активного input
let jq_modal_gps = $('#modal_gps');
let gl_modal_gps_coordinates_default = [49.272776184334845, 44.334048971828175];

// Инициализация карты Leaflet
let map = L.map('map').setView(gl_modal_gps_coordinates_default, 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: ''
}).addTo(map);

/*
// Добавление кнопки определения местоположения
L.control.locate({
	position: 'topright', // Вы можете изменить положение кнопки на карте
	locateOptions: {
		enableHighAccuracy: true
	}
}).addTo(map);


// Обработчик события определения местоположения
map.on('locationfound', function(e) {
	if (gl_modal_gps_marker) {
		map.removeLayer(gl_modal_gps_marker); // Удаляем предыдущий маркер, если он есть
	}
	gl_modal_gps_marker = L.marker(e.latlng).addTo(map);
	gl_modal_gps_coordinates = e.latlng; // Сохраняем выбранные координаты
	map.setView(e.latlng, 13); // Центрируем карту на местоположение пользователя
	map.stopLocate();
}).on('locationerror', function(e) {
	console.log(e);
	//alert("Location access denied.");
});
*/

function f_parse_int(num){
	num = parseFloat(num);
	return isNaN(num) ? 0 : num
}


// Обработчик клика по карте для установки маркера
map.on('click', function(e) {
	if (gl_modal_gps_marker) {
		map.removeLayer(gl_modal_gps_marker); // Удаляем предыдущий маркер, если он есть
	}
	gl_modal_gps_marker = L.marker(e.latlng).addTo(map).openPopup();
	gl_modal_gps_coordinates = e.latlng; // Сохраняем выбранные координаты
});

jq_modal_gps.on('shown.bs.modal', function () {
	map.invalidateSize();
});

// Показываем модальное окно при клике на input с атрибутом gps
$('[gps_input]').click(function() {
	jq_input_modal_gps = $(this); // Сохраняем активный input
	let title = jq_input_modal_gps.parent().find('label').text();
	jq_modal_gps.find('.modal-title').text( title );
	
	if (gl_modal_gps_marker) {
		map.removeLayer(gl_modal_gps_marker);
	}
	let value_coordinates = jq_input_modal_gps.val();
	let coords = value_coordinates.split(/\s*,\s*/g);
	if ((f_parse_int(coords[0]) !== 0 && f_parse_int(coords[1]) !== 0)) {
		
		let latLng = L.latLng(coords[0], coords[1]);
		map.setView(latLng, 13);
		gl_modal_gps_marker = L.marker(latLng).addTo(map).openPopup();
	} else {
		//map.setView(gl_modal_gps_coordinates_default, 2); // Стандартное местоположение
		//map.locate({setView: true, maxZoom: 16}); // Стандартное местоположение
	}
	jq_modal_gps.modal('show');
});

// Обработчик кнопки "Готово"
jq_modal_gps.find('.btn_ready').click(function() {
	if (gl_modal_gps_coordinates) {
		console.log(jq_input_modal_gps)
		jq_input_modal_gps.val(gl_modal_gps_coordinates.lat + ',' + gl_modal_gps_coordinates.lng);
	}
	jq_modal_gps.modal('hide');
});


// Создаем кнопку для определения местоположения
let lf_locate_btn = L.control({position: 'topright'});
lf_locate_btn.onAdd = function (map) {
    let div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom  text-center  d-flex  justify-content-center  align-items-center  lh-1');
    div.innerHTML = '<i class="bi bi-geo-fill"></i>'; // Используйте подходящий символ или изображение для кнопки
    div.style.backgroundColor = 'white';
    div.style.width = '30px';
    div.style.fontSize = '20px';
    div.style.cursor = 'pointer';
    div.style.height = '30px';
    div.onclick = function(){
		event.stopPropagation(); // Предотвращаем всплытие события на карт
        navigator.geolocation.getCurrentPosition(function(position) {
            let latLng = L.latLng(position.coords.latitude, position.coords.longitude);
            if (gl_modal_gps_marker) {
                map.removeLayer(gl_modal_gps_marker); // Удаляем предыдущий маркер, если он есть
            }
            gl_modal_gps_marker = L.marker(latLng).addTo(map); // Создаем новый маркер
            gl_modal_gps_coordinates = latLng; // Сохраняем координаты
            map.setView(latLng, 15); // Центрируем карту
        }, function(error) {
            console.log(error);
        });
    };
    return div;
};
lf_locate_btn.addTo(map);

</script>




<script>

function f_form_get( jq_modal_form ){
	let data_json = {}
	jq_modal_form.find('[field_name]').each(function(i, elem){
		let jq_elem = $(elem);
		let field_name = jq_elem.attr('field_name');
		let field_value = jq_elem.val();
		
		if(jq_elem.attr('type') == 'checkbox'){
			field_value = jq_elem.prop('checked') ? 1 : 0;
		}
		
		data_json[ field_name ] = field_value;
	})
	return data_json;
}

function f_form_set(form_json, set_json){
	let jq_modal_form = $( '#' + form_json['name'] );
	for(let i_item in form_json['control_arr']){
		let json_item = form_json['control_arr'][i_item];
		let jq_control = jq_modal_form.find('[field_control="' + json_item['name'] + '"]');
		
		let jq_input = jq_control.find( json_item['tag'] );
		
		let field_name = jq_input.attr('field_name');
		
		let set_value = set_json[ field_name ];
		if( set_value !== undefined ){
			jq_input.val( set_value );
		}
	}
}

function f_form_reset(form_json){
	let jq_modal_form = $( '#' + form_json['name'] );
	for(let i_item in form_json['control_arr']){
		let json_item = form_json['control_arr'][i_item];
		
		if( json_item['tag'] == 'html' ){
			continue;
		}
		
		let jq_control = jq_modal_form.find('[field_control="' + json_item['name'] + '"]');
		
		let jq_input = jq_control.find( json_item['tag'] );
		let jq_label = jq_control.find('.form-label');
		let jq_text = jq_control.find('.form-text');
		
		jq_label.text( json_item['title'] );
		
		// Установка ТИП Даты
		json_item = f_form_m_value(json_item, jq_input)
		
		jq_input.val( json_item['value_new'] )
		
		if( json_item['disabled'] ){
			jq_input.attr( 'disabled', 'disabled' )
		}
		
		jq_text.html( json_item['text'] );
		
	}
}

</script>

<script>


$('select[select2]:not([disabled])').each(function() {
	let jq_select = $(this);
	let jq_parent = ( $(this).parent() );
	let placeholder = jq_select.attr('placeholder');
	
	let select2_api = jq_select.attr('select2_api') || false;
	
	/*
	if(jq_select.find('option[value=""]').length == 0 ){
		jq_select.prepend('<option value="" disabled><?php f_echo_html( f_translate('Не указано') ); ?></option>')
	}
	*/
	
	let select2_multy = jq_select.attr('select2_multy') === '';
	let select2_search = jq_select.attr('select2_search') === '';
	let select2_parent = jq_select.attr('select2_parent') || '';;
	let value_id = jq_select.attr('value') || false;
	
	let jq_box = $('<div class="form-select  p-0  flex-shrink-0  w-100"  style="height: 37.6px;" ></div>');
	
	jq_box.insertBefore( jq_select );
	
	let classes = jq_select.attr('class');
	
	jq_select.removeAttr('class');
	jq_select.addClass('d-none');
	
	jq_select.appendTo( jq_box )
	
	let option_json = {
		placeholder: placeholder,
		minimumInputLength: 0
	}
	
	if( select2_multy ){
		jq_select.attr('multiple', 'multiple');
		option_json['maximumSelectionLength'] = 100; // Лимит на количество выбранных элементов]
	}
	
	if( !select2_search ){
		option_json['minimumResultsForSearch'] = Infinity;
	}
	
	if( select2_parent ){
		option_json['dropdownParent'] = $(select2_parent);
	}else{
		//option_json['dropdownParent'] = jq_parent; // не работает jq_parent - пустой почему то
	}
	
	//console.log('select2_parent', select2_parent, $(select2_parent));
	//console.log('dropdownParent', option_json['dropdownParent']);
	
	if( select2_api ){
		//console.log('select2_api', select2_api)
		option_json['ajax'] = {
			url: select2_api, // Замените на ваш API URL
			type: "POST",
			dataType: 'json',
			delay: 250, // задержка перед запросом к API
			cache: true,
			data: function (params) {
				//console.log( 'params.term', params.term )
				return {
					search: params.term // поисковый запрос
				};
			},
			processResults: function (json_response, params) {
				console.log( 'processResults', json_response, params )
				// преобразование полученных данных в формат, который ожидается select2
				return {
					results: $.map(json_response.data, function (item) {
						//console.log('item', item)
						return {
							text: item.title,
							id: item._id
						};
					})
				};
			},
		}
		//console.log('option_json', option_json)
	}
	
	if( jq_select.find('option[value=""]').length == 0 && jq_select.find('option[selected]').length == 0 ){
		jq_select.val('').trigger("change");
	}
	
	
	if( value_id ){
		let value_arr = value_id.split(',');
		jq_select.val( value_arr ).trigger("change");
	}
	
	jq_select.select2( option_json );
	
	jq_select.parent().addClass(classes)
	
	jq_select.trigger("change");
	
	// Если у select есть атрибут value и API URL
    if (value_id && select2_api) {
		$.ajax({
            url: select2_api,
            type: "POST",
            dataType: 'json',
            data: { _id: value_id }, // Отправляем ID для поиска
            success: function(response) {
                // Проверяем, получили ли мы данные
                if (response && response.data && response.data.length > 0) {
                    let item = response.data[0]; // Берем первый элемент из ответа
                    // Создаем новый option и устанавливаем его как выбранный
                    let newOption = new Option(item.title, item._id, true, true);
                    jq_select.append(newOption).trigger('change');
                }
            }
        });
    }
});


$(document).on('change input', '[select2_sub]', function () {
    let jq_this       = $(this);
    let value         = jq_this.val();
    let title         = jq_this.find('option:selected')
                                 .map(function () { return $(this).text().trim(); })
                                 .get();

    let jq_sub        = $(jq_this.attr('select2_sub'));
    let jq_sub_other  = $(jq_this.attr('select2_sub_other'));

    // Скрываем зависимые select-элементы по умолчанию
    jq_sub.parent().parent().addClass('d-none');
    jq_sub_other.parent().parent().addClass('d-none');

    // Если нет значений — очищаем и выходим
    if (!value && !title) {
        if (jq_sub.length) {
            jq_sub.empty().trigger('change.select2');
        }
        return;
    }

    // Если нет подчинённого селекта — ничего не делаем
    if (!jq_sub.length) return;

    // Сохраняем оригинальные опции при первом заходе
    if (!jq_sub.data('original-options')) {
        jq_sub.data('original-options', jq_sub.find('option').clone());
    }

    // **1. Запоминаем текущее значение перед очисткой**
    let prevValue = jq_sub.val();  // строка или массив

    // **2. Фильтруем опции**
    let originalOptions = jq_sub.data('original-options');
    let arrValue  = Array.isArray(value) ? value : [value];
    let arrTitle  = Array.isArray(title) ? title : [title];
    let jq_need_option = originalOptions.filter(function () {
        let opt = $(this);
        return arrValue.includes(opt.attr('parent_id'))
            || arrTitle.includes(opt.attr('parent_domain'));
    });

    // Если ничего не подошло — скрываем и выходим
    if (!jq_need_option.length) {
        jq_sub.empty().trigger('change.select2');
        return;
    }

    // **3. Очищаем и вставляем отфильтрованные опции**
    jq_sub.empty().append(jq_need_option);

    // **4. Восстанавливаем предыдущее значение, если оно ещё есть**
    if (prevValue) {
        jq_sub.val(prevValue);
    }

    // **5. Обновляем Select2 и показываем контейнер**
    jq_sub.trigger('change.select2');
    jq_sub.parent().parent().removeClass('d-none');
});

// При загрузке страницы «прокидываем» начальное состояние
$('.param_group [select2]').trigger('change');


	
</script>



<div template="ads_item_line" class="item_ad">
	<a href="#" class="body_item_ad">
		<img class="img_item_ad" src="/public/ad_default.jpg">
		<div class="text_item_ad">
			<div class="d-flex  justify-content-between">
				<div class="title_item_ad">
					I will sell a new Luxury segment car directly from the salon
				</div>
				<div class="btn_favorite_item_ad   bi"></div>
			</div>
			<div class="price_item_ad">
				20 000 $
			</div>
			<div class="city_item_ad">
				London
			</div>
			<div class="date_item_ad">
				Today
			</div>
		</div>
	</a>
</div>

<script>


let jq_ads_list_param_filter = $('.list_param_filter_box_split_list_ads');


$('[ads_list_type]').each(function(i, jq_item){
	//ads_list_type="line" ads_list_query="recomendation" ads_list_category_id
	f_ads_item_list_scroll_load( $(jq_item) )
})

function f_ads_item_list_scroll_load(jq_ads_list){
	
	let gl_ads_items_is_end = false;
	let gl_ads_items_is_loading = false; // Флаг, предотвращающий повторные загрузки
    let gl_ads_items_page_num = 1;       // Текущая страница (или номер запроса)
	//let jq_ads_list = $('.list_item');
	
	let list_type = jq_ads_list.attr('ads_list_type');
	let list_query = jq_ads_list.attr('ads_list_query');
	let category_id = jq_ads_list.attr('ads_list_category_id');
	
    function f_ads_items_load() {
        if (gl_ads_items_is_loading) return; // Если уже идет загрузка, ничего не делаем
        if (gl_ads_items_is_end) return; // Если уже идет загрузка, ничего не делаем

		let json_query = f_ads_filter_param_get();
		let url_q = (typeof f_url_query_to_json === 'function') ? f_url_query_to_json() : {};

        gl_ads_items_is_loading = true; // Устанавливаем флаг загрузки
		jq_ads_list.addClass('loading');

        // Выполнение AJAX-запроса
		f_ajax(
			module="ads",
			query="get_list",
			data_json={
				'page_num': gl_ads_items_page_num,
				'category_id': category_id,
				'list_type': list_type,
				'list_query': list_query,
				'json_url_query': json_query,
				'ads_search_title': url_q['ads_search_title'] || '',
				'ads_search_city_id': url_q['ads_search_city_id'] || '',
				'sort': url_q['sort'] || ''
			},
			func_callback=function(json_data, status, xhr){
				json_data = json_data['data'];
				
				if (json_data['arr_item'] && json_data['arr_item'].length > 0) {
                    // Добавляем новые карточки в список
                    json_data['arr_item'].forEach(json_item => {
                        jq_ads_list.append( f_ads_item_line_make(json_item) );
                    });
                    gl_ads_items_page_num++; // Увеличиваем номер страницы для следующего запроса
                } else {
					if( list_type == 'line' ){
						// Если больше нет данных, отключаем загрузку
						gl_ads_items_is_end = true;
						//$(window).off('scroll', f_scroll_list_loader);
					}
                }
			},
			func_callback_error=function(){
				console.error("<?php f_echo_html( f_translate( "Data upload error" ) ); ?>");
			},
			func_callback_complete=function(){
				jq_ads_list.removeClass('loading');
                gl_ads_items_is_loading = false;   // Сбрасываем флаг загрузки
			}
		)
    }
	
	if( list_type == 'line' ){
		// Привязываем обработчик скролла
		$(window).on('scroll', function(){
			f_scroll_list_loader(
				jq_ads_list,
				f_ads_items_load
			);
		});
		// Первая порция без ожидания скролла
		f_ads_items_load();
	}

	jq_ads_list.off('ads_list_reload').on('ads_list_reload', function(){
		gl_ads_items_page_num = 1;
		gl_ads_items_is_end = false;
		jq_ads_list.children('.item_ad').remove();
		f_ads_items_load();
	});
}



function f_scroll_list_loader(jq_list, f_callback=function(){}, before_px=400) {
	// Запуск при достижения до 400px до конца jq_ads_list
	let loader_offset = jq_list.offset().top + jq_list.outerHeight() - before_px;

	if ($(window).scrollTop() + $(window).height() >= loader_offset) {
		f_callback();
	}
}


function f_ads_item_line_make(json_item){
	let jq_item = f_template_get('ads_item_line');
	jq_item.find('.body_item_ad').attr( 'href', json_item['html_link_ad'] )
	jq_item.find('.img_item_ad').attr( 'src', json_item['html_img_src'] )
	jq_item.find('.title_item_ad').text( json_item['title'] )
	jq_item.find('.price_item_ad').text( json_item['html_price'] )
	jq_item.find('.city_item_ad').text( json_item['html_city'] )
	jq_item.find('.date_item_ad').text( json_item['html_date'] )
	jq_item.find('.btn_favorite_item_ad').addClass( 'bi-heart' + (json_item['html_favorite_on'] ? '-fill' : '') )
	return jq_item
	
}








function f_ads_filter_param_get(){
	let json_query = {};
	
	// INPUT FILTER
	jq_ads_list_param_filter.find('input[filter_type]').each(function(i, jq_input){
		jq_input = $(jq_input);
		let filter_val = jq_input.val();
		if( filter_val.trim() === '' ){
			return;
		}
		let filter_type = jq_input.attr('filter_type')
		let filter_id = jq_input.attr('filter_id')
		if( json_query[filter_id] == undefined ){
			json_query[filter_id] = {}
		}
		json_query[filter_id][filter_type] = filter_val;
	})
	
	// CHECKBOX FILTER
	jq_ads_list_param_filter.find('input[type="checkbox"]:checked').each(function(i, jq_input){
		jq_input = $(jq_input);
		let filter_val = jq_input.val();
		if( filter_val.trim() === '' ){
			return;
		}
		let filter_id = jq_input.attr('filter_id')
		if( json_query[filter_id] == undefined ){
			json_query[filter_id] = []
		}
		json_query[filter_id].push( jq_input.val() );
	})
	
	// SELECT FILTER
	jq_ads_list_param_filter.find('select').each(function(i, jq_select){
		jq_select = $(jq_select);
		let filter_val = jq_select.val();
		let filter_id = jq_select.attr('filter_id');
		if( filter_val.length > 0 ){
			json_query[filter_id] = jq_select.val();
		}
	})
	
	return json_query
}

jq_ads_list_param_filter.find('input, select').on('change input', function(){
	let json_query_filter = {'ads_list_filter': f_ads_filter_param_get()}
	
	/*
	Object.keys(json_query_filter).forEach(function(key){
		json_query_filter[ 'ads_filter_' + key ] = json_query_filter[ key ]
		delete json_query_filter[ key ];
	})
	*/
	
	let json_query = f_url_query_to_json()
	let json_new_query = Object.assign({}, json_query, json_query_filter);
	let new_query_url = f_url_json_to_query( json_new_query )
	history.replaceState(null, "", new_query_url);
})

jq_ads_list_param_filter.find('input, select').on('change', function(){
	$('[ads_list_type]').trigger('ads_list_reload');
})


/*

$(window).on('load', function(){
		
	let json_url_query_param = f_url_query_to_json()['ads_list_filter'];
	
	try{
		json_url_query_param = JSON.parse( json_url_query_param );
	}catch{
		
	}
	
	console.log( json_url_query_param );
	
	Object.keys( json_url_query_param ).forEach(function(filter_id){
		
		let val_param = json_url_query_param[ filter_id ]
		
		jq_ads_list_param_filter.find('[filter_id="'+ filter_id +'"]').each(function(i, jq_input_checkbox){
			jq_input_checkbox = $(jq_input_checkbox);
			let tagname = jq_input_checkbox.prop("tagName").toLowerCase();
			
			if( tagname == 'select' ){
				jq_input_checkbox.val( val_param ).trigger('change')
			}else if( tagname == 'input' && jq_input_checkbox.attr('type') == 'checkbox' ){
				if( val_param.indexOf( jq_input_checkbox.val() ) ){
					jq_input_checkbox.prop('checked', true);
				}
			}else if( tagname == 'input' ){
				if( val_param['min'] !== undefined && jq_input_checkbox.attr("filter_type") == "min" ){
					jq_input_checkbox.val( val_param['min'] )
				}
				if( val_param['max'] !== undefined && jq_input_checkbox.attr("filter_type") == "max" ){
					jq_input_checkbox.val( val_param['max'] )
				}
			}
		})
		
	})
	
})

*/

</script>




<script>

let class_show = 'show_filter_box_split_list_ads';
let jq_btn_close_filter_box_split_list_ads = $('.btn_close_filter_box_split_list_ads');
let jq_page = $('.page');

function f_gl_show_filter_box_split_list_ads(){
	if( jq_page.hasClass(class_show) ){
		jq_page.removeClass(class_show)
	}else{
		jq_page.addClass(class_show)
	}
}

// Отслеживаем изменение хэша в URL
$(window).on('hashchange', function () {
	if (window.location.hash === '#search') {
		
		// Убираем #search из URL без добавления истории в браузере
		history.replaceState(null, null, window.location.pathname + window.location.search);
		
		f_gl_show_filter_box_split_list_ads()
	}
}).trigger('hashchange');


jq_btn_close_filter_box_split_list_ads.on('click', function(){
	jq_page.removeClass(class_show);
})

	
</script>



<script>
$(function () {
	$('[data-bs-toggle="tooltip"]').tooltip();
});

Fancybox.bind('[data-fancybox]', {});

toastr.options = {
	"closeButton": true,
	"debug": false,
	"newestOnTop": true,
	"progressBar": true,
	"positionClass": "toast-top-right",
	"preventDuplicates": false,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "5000",
	"extendedTimeOut": "1000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "fadeIn",
	"hideMethod": "fadeOut"
}


function f_format_number(number){
	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}


// Общие настройки для всех Select2
$.fn.select2.defaults.set('language', {
    noResults: function() {
        return "<?php f_echo_html( f_translate( "Nothing was found" ) ); ?>";
    },
    inputTooShort: function() {
        return "<?php f_echo_html( f_translate( "Enter more characters" ) ); ?>...";
    },
    errorLoading: function() {
        return "<?php f_echo_html( f_translate( "Error when uploading data" ) ); ?>";
    },
    loadingMore: function() {
        return "<?php f_echo_html( f_translate( "Loading" ) ); ?>...";
    },
    searching: function() {
        return "<?php f_echo_html( f_translate( "Search" ) ); ?>...";
    },
    maximumSelected: function() {
        return "<?php f_echo_html( f_translate( "You have already selected the maximum number of items" ) ); ?>";
    },
    inputTooLong: function() {
        return "<?php f_echo_html( f_translate( "The request is too long" ) ); ?>";
    },
    formatNoMatches: function() {
        return "<?php f_echo_html( f_translate( "Nothing was found" ) ); ?>";
    }
});




let gl_hash_json = decodeURIComponent( location.hash.substr(1) );
try{
	gl_hash_json = JSON.parse(gl_hash_json);
}catch{
	gl_hash_json = {};
}



function f_url_remove_hash() {
	history.replaceState(null, document.title, window.location.pathname + window.location.search);
}


/*
function f_back_page_link() {
    // Получаем предыдущую ссылку из истории переходов
    const previousUrl = document.referrer;

    // Получаем текущий домен
    const currentDomain = window.location.hostname;

	if( previousUrl == location.href ){
		return false;
	}

    // Если предыдущая ссылка не пуста и домены совпадают, возвращаем предыдущую ссылку
    if (previousUrl && previousUrl.includes(currentDomain)) {
        return previousUrl;
    } else {
        return false;
    }
}

let back_page_link = f_back_page_link();
if( back_page_link != '' ){
	$('[back_page_link]').attr('href', back_page_link)
}
*/


// Получаем предыдущую ссылку из истории переходов
const previousUrl = document.referrer;

// Получаем текущий домен
const currentDomain = window.location.hostname;

if( previousUrl != location.href ){
	if (previousUrl && previousUrl.includes(currentDomain)) {
        $('[back_page_link]').attr('href', '#back').on('click', function(e){
			e.preventDefault();
			history.back();
		})
    }
}


function f_copy_text(text) {
	let jq_tmp = $("<textarea>");
	$("body").append( jq_tmp );
	jq_tmp.css({'position': 'fixed', 'left': '-1000px', 'top': '-1000px', 'width': '40px', 'height': '10px', 'opacity': '0'})
	jq_tmp.val(text).select();
	document.execCommand("copy");
	jq_tmp.remove();
}


$(document).on('click', '[btn_href]', function(){
	let jq_el = $(this);
	let href = ( jq_el.attr('btn_href') || '' ).trim();
	let target = ( jq_el.attr('btn_href_target') || '' ).trim();
	if( href ){
		if( target ){
			window.open(href, target)
		} else {
			window.location.href = href;
		}
    }
})

$(document).on('click', '[for]', function(event) {
	// Проверяем, был ли клик по самому элементу, на который ссылается атрибут for
	let target_id = $(this).attr('for'); // Получаем значение атрибута for
	let target_element = $('#' + target_id); // Находим элемент по ID
	
	//console.log(target_element, event.target)

	if (target_element.length && event.target !== target_element[0] && event.target.tagName.toLowerCase() != 'label') {
		// Если цель существует и это не клик на самом элементе
		target_element.trigger('click'); // Триггерим событие клика
	}
});


$('input[inputmask="phone"]').inputmask({
    //mask: '+9{1,3} 999 999 9999', // Маска с динамическим кодом страны и форматом номера
	mask: '+44 7[9]99 999 999',
    greedy: true,                     // Разрешить разное количество цифр в коде страны
    placeholder: '_',                 // Символ заполнения
    clearIncomplete: false             // Очищать незаполненные поля
});

$('input[inputmask="number"]').inputmask('currency', {
    prefix: '',                 // Убираем префикс
    groupSeparator: ',',        // Разделитель групп (тысяч)
    radixPoint: '.',            // Десятичный разделитель
    autoGroup: true,            // Включаем автоформатирование групп
    digits: 2,                  // Количество знаков после запятой
    digitsOptional: false,      // Десятичные разряды обязательны
	clearIncomplete: false,     // Очищать незаполненные поля
    placeholder: '0',           // Оставляем поле 0
    clearMaskOnLostFocus: true, // Убираем ноль при потере фокуса
    //allowMinus: true            // Разрешаем отрицательные числа
});


let gl_template_json = {}
function f_template_page(){
	$('[template]').each(function(i, elem){
		let jq_elem = $(elem);
		let name = jq_elem.attr('template')
		let jq_new = $(jq_elem[0].outerHTML);
		jq_elem.remove();
		jq_new.removeAttr('template')
		gl_template_json[name] = jq_new[0].outerHTML
	})
}

$(document).ready(function() {
	f_template_page();
})


function f_template_get(name){
	return $(gl_template_json[name]);
}



$(document).on('form  change', 'input[name][type="checkbox"]', function(){
	let jq_elem = $(this);
	jq_elem.attr('checked', jq_elem.attr('value') == 'true' ? '' : 'checked')
	jq_elem.attr('value', jq_elem.attr('value') == 'true' ? 'false' : 'true')
})


$('.icon_show_pass').on('click', function(){
	let jq_elem = $(this);
	let jq_input = jq_elem.closest('.box_show_pass').find('input');
	let class_1 = 'bi-eye-fill';
	let class_2 = 'bi-eye-slash-fill';
	
	if( jq_elem.hasClass(class_1) ){
		jq_elem.removeClass(class_1);
		jq_elem.addClass(class_2);
		jq_input.attr('type', 'text');
	}else{
		jq_elem.removeClass(class_2);
		jq_elem.addClass(class_1);
		jq_input.attr('type', 'password');
	}
	
})





	
function f_scroll_left_to_center(query_elem){
	let jq_elem = $(query_elem);
	if( jq_elem.length == 0 ){
		return;
	}
	let jq_parent = jq_elem.parent();
	let offset_elem = jq_elem[0].getBoundingClientRect();
	let offset_parent = jq_parent[0].getBoundingClientRect();
	jq_parent.scrollLeft( (offset_elem.x - offset_parent.x) - (offset_parent.width / 2) + (offset_elem.width / 2) );
}



// Сортировка таблицы по колонкам
let json_query_url = f_url_query_to_json();
if( json_query_url['desc'] !== undefined ){
	$('th[column]').removeAttr('desc');
	$('th[column="'+ json_query_url['column'] +'"]').attr('desc', json_query_url['desc']);
	f_scroll_left_to_center(".table_mobile  thead  [desc]")
}

$('.pagination  [page]').on('click', function(){
	let jq_elem = $(this);
	
	let search_json = f_url_query_to_json();
	
	search_json['page'] = parseInt( jq_elem.attr('page') );
	
	location.search = f_url_json_to_query( search_json );
})


$('table  th[column]').on('click', function(){
	let jq_elem = $(this);
	let jq_thead = jq_elem.closest('thead');
	let column = jq_elem.attr('column');
	let new_desc = jq_elem.attr('desc') == '1' ? '0' : '1';
	jq_thead.find('th[column]').removeAttr('desc')
	jq_elem.attr('desc', new_desc);
	
	let search_json = f_url_query_to_json();
	
	search_json['column'] = column;
	search_json['desc'] = new_desc;
	
	location.search = f_url_json_to_query( search_json );
})




function f_scroll_to_auto(){
	let arr_scroll_top = $('.scroll_to_top');
	let arr_scroll_left = $('.scroll_to_left');
	
	arr_scroll_top.each(function(i, jq_elem){
		jq_elem = $(jq_elem);
		$(jq_elem).parent().scrollTop(jq_elem.position().top);  // Скроллим вверх
	})
	
	arr_scroll_left.each(function(i, jq_elem){
		jq_elem = $(jq_elem);
		$(jq_elem).parent().scrollLeft(jq_elem.position().left);  // Скроллим влево
	})
	
}
f_scroll_to_auto()




function f_url_query_to_json(query=location.search) {
	// Удаляем начальный символ '?', если он есть
	if (query[0] === '?') {
		query = query.substring(1);
	}

	// Разделяем параметры
	const params = query.split('&');
	const result_json = {};

	// Преобразуем каждый параметр в ключ-значение
	params.forEach(param => {
		const [key, value] = param.split('=');
		if(key == ''){
			return;
		}
		// Декодируем URI компоненты и добавляем в результат
		result_json[decodeURIComponent(key)] = decodeURIComponent( (value+'').replace(/\+/g, ' '));
	});

	return result_json;
}

function f_url_json_to_query(json = {}) {
    return '?' + Object.keys(json)
        .filter(key => json[key] !== '') // Пропускаем ключи с пустыми значениями
        .map(key => {
            const value = typeof json[key] === 'object' ? JSON.stringify(json[key]) : json[key];
            return `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
        })
        .join('&');
}

function f_ajax(module="", query="", data_json={}, func_callback=function(data, status, xhr){}, func_callback_error=function(){}, func_callback_complete=function(){}){
	data_json['cur_time'] = new Date().toISOString();

    let form_data = new FormData();
    Object.keys(data_json).forEach(function (key) {
        const value = typeof data_json[key] === 'object' && data_json[key] !== null 
            ? JSON.stringify(data_json[key]) 
            : data_json[key];
        form_data.append(key, value);
    });
	
	$.ajax('https://'+location.host+'/api/'+module+'/'+query, {
		type: 'post',
		data: form_data,
        processData: false, // !!! Важно: отключаем обработку данных jQuery
        contentType: false, // !!! Важно: отключаем установку Content-Type jQuery
		success: func_callback,
		error: func_callback_error,
		complete: func_callback_complete,
	})
}



</script>


<script>

$( document.body ).on('click', '[click_delete_my_item_ad]', function(){
	let jq_this = $(this);
	let jq_item = jq_this.closest('.item_ad');
	
	f_confirm_modal({
		title: '<?php f_translate_echo("Delete Confirmation"); ?>',
		icon: 'trash3-fill  text-danger',
		body: '<?php f_translate_echo("Are you sure you want to delete this ad?\\nThis action cannot be undone"); ?>',
		btn: {
			title: '<?php f_translate_echo("Yes"); ?>',
			callback: function () {
				// AJAX
				jq_item.remove();
			}
		}
	});
})

</script>







<div template="alert_modal" class="modal fade" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog  modal-dialog-centered">
		<div class="modal-content  pt-4">
			<!--
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			-->
			<div class="modal-body text-center">
				<div class="modal-icon  mb-4"></div>
				<h3 class="modal-body-title"></h3>
				<div class="modal-body-content"  style="white-space: break-spaces;"></div>
			</div>
			<div class="modal-footer  justify-content-center  gap-3  mb-3  border-0"></div>
		</div>
	</div>
</div>


<script>

function f_confirm_modal(config_json) {
	
	let jq_modal = f_template_get('alert_modal')
	
	// Установить заголовок
	jq_modal.find('.modal-body-title').text(config_json['title'] || 'Alert');

	// Установить иконку
	if (config_json['icon']) {
		jq_modal.find('.modal-icon').html('<i class="bi bi-'+config_json['icon']+'" style="font-size: 5rem; line-height: 1"></i>');
		// question-circle-fill
	} else {
		jq_modal.find('.modal-icon').remove();
	}

	// Установить тело модального окна
	jq_modal.find('.modal-body-content').html(config_json['body'] || '');

	// Очистить кнопки

	// Добавить кнопки
	let arr_btn = []
	
	if ( typeof config_json['btn'] === 'object' ) {
		config_json['btn']['class'] = 'btn-dark';
		arr_btn.push(config_json['btn'])
	}
	
	arr_btn.push({
		title: '<?php f_translate_echo("Cancel"); ?>',
		class: 'btn-outline-dark',
		callback: function () {
			/*
			jq_modal.modal('hide');
			setTimeout(function(){
				jq_modal.remove()
			}, 150)
			*/
		}
	})
	
	arr_btn.forEach((btn_json) => {
		
		let jq_btn = $('<button>')
			.css({'minWidth': '100px'})
			.addClass( 'btn btn-lg ' + btn_json['class'] )
			.html( btn_json['html'] || btn_json['title'] )
			.on('click', function () {
				if (typeof btn_json['callback'] === 'function') {
					btn_json.callback();
				}
				jq_modal.modal('hide');
				jq_modal.on('hidden.bs.modal', function () {
					jq_modal.remove()
				});
			});

		jq_modal.find('.modal-footer').append( jq_btn );
	});
	
	$( document.body ).append( jq_modal );
	
	jq_modal.modal('show');
}

/*
f_confirm_modal({
	title: 'Confirmation',
	icon: 'question-circle-fill',
	body: 'Are you sure you want to proceed?',
	btn: {
		title: 'Yes',
		callback: function () {
			console.log('Yes clicked!');
		}
	}
});
*/



</script>