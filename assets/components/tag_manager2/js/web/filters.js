
/**
 * tmFilters
 *
 * tagManager 2.3.1pl2
 * Andchir
 * http://modx-shopkeeper.ru/
 * 
 */

var tmFilters = {
    
    /* ########################################### */
    /**
     * config
     *
     * Настройки
     * 
     */
    config: {
        filters_cont: '#filters',//Селектор оберточного элемента блоков с фильтрами
        sortby: 'pagetitle',//Имя поля сортировки по умолчанию
        sortdir: 'asc',//Направление сортировки по умолчанию
        numeric: ['price', 'weight'],//Имена доп. полей с числовыми значениями
        multitags: ['tags'],//Имена доп. полей с множественными значениями
        guard_key: '#',//Разделитель для множественных значений
        products_cont: '#products',//Селектор контейнера с выводом товаров
        filter_slider: 'div.range-slider',//Селектор слайдеров (ползунок для числовых значений)
        filter_slider_cont: 'div.filter_slider',//Селектор контейнера со слайдером
        pages_cont1: '#pages',//Селектор контейнера с постраничной навигацией
        pages_cont2: '#pages2',//Селектор второго контейнера с постраничной навигацией. Если нет, оставить пустым.
        active_page_selector: '.current',//селектор номера текущей страницы внутри контейнера (pages_cont)
        filters_type: 'default',//Тип фильтрации. Возможные значения:
            // default (показ числа товаров по каждому фильтру и блокирование пустых вариантов),
            // only_block (только блокирование пустых париантов),
            // none (не показывать цифры и не блокировать)
        filter_delay: 700,//Задержка до отправления запроса на сервер (сбрасывается после каждой отметки фильтра)
        price_field: 'price',//Название поля или TV цены товара
        multi_currency: true,//Мультивалютность включить / выключить (true/false)
        base_url: '/',
        ajax_url: 'assets/components/tag_manager2/connector_fe.php',
        ajax_loader: 'assets/components/tag_manager2/img/ajax-loader2.gif'
    },
    /* ########################################### */
    
    result_ids: [],
    filtersActive: false,
    filtered: false,
    sorted: false,
    keyCtrl: false,
    timer: null,
    
    /**
     * init
     *
     */
    init: function( conf ){
        
        $.extend( this.config, conf );
        
        this.slidersInit();
        this.ajaxPagesInit();
	    this.filterSelected();
        
        $( 'input,select', this.config.filters_cont )
        .on('change', function( event ){
            
            tmFilters.filtersPreSubmit( event );

            clearTimeout(tmFilters.timer);
            tmFilters.timer = setTimeout( function(){
                tmFilters.filtersSubmit();
            }, tmFilters.config.filter_delay );

        });
        
        var filters = tmFilters.getFilters();
        tmFilters.countAllFIlters( filters );
        
    },
    
    
    /**
     * slidersInit
     *
     * Инициализация слайдеров
     */
    slidersInit: function(){
        
        if ( $( tmFilters.config.filter_slider, tmFilters.config.filters_cont).length > 0 ) {
            
            $( tmFilters.config.filter_slider, tmFilters.config.filters_cont).each(function(i){
                
                var this_slider = $(this),
                    slider_wrapper = this_slider.parent(),
                    flt_name = tmFilters.getFilterName( $('input:text:eq(0)',slider_wrapper).attr('name') ),
                    minField = $('input:text:eq(0)',slider_wrapper),
                    maxField = $('input:text:eq(1)',slider_wrapper),
                    minValue = parseFloat(tmFilters.getNumber(minField.val())),
                    maxValue = parseFloat(tmFilters.getNumber(maxField.val()));

                //при мультивалютности пересчитываем цену по курсу
                if ( tmFilters.config.multi_currency && flt_name == tmFilters.config['price_field'] ) {
                    minValue = tmFilters.currRate( flt_name, minValue, 'min' );
                    maxValue = tmFilters.currRate( flt_name, maxValue, 'max' );
                    minField.val( minValue.toString() );
                    maxField.val( maxValue.toString() );
                }

		        var slider_step = maxValue - minValue < 100 ? 0.1 : 1;
		
                this_slider.slider({
                    min: minValue,
                    max: maxValue,
                    step: slider_step,
                    values: [
                        minValue,
                        maxValue
                    ],
                    range: true,
                    start: function(event, ui) {
                        //Сохраняем значение чтобы потом иметь возможность вернуть предыдущее
                        var activeHandler = $(ui.handle);
                        var order = activeHandler.prevAll('.ui-slider-handle').length;
                        var value = $(event.target).slider("values", order);
                        $(ui.handle).data('value', value);
                    },
                    stop: function(event, ui) {

                        minField.val(this_slider.slider("values",0));
                        maxField.val(this_slider.slider("values",1));
                        
                        tmFilters.filtersPreSubmit( event );

                        clearTimeout(tmFilters.timer);
                        tmFilters.timer = setTimeout( function(){
                            tmFilters.filtersSubmit();
                        }, tmFilters.config.filter_delay );

                    },
                    slide: function(event, ui){
                        minField.val(this_slider.slider("values",0));
                        maxField.val(this_slider.slider("values",1));
                    }
                });

                $('input:text', slider_wrapper).on('keyup', function(e){
                    clearTimeout(tmFilters.timer2);
                    tmFilters.timer2 = setTimeout(function(){
                        var curMinValue = parseFloat(tmFilters.getNumber(minField.val())),
                            curMaxValue = parseFloat(tmFilters.getNumber(maxField.val()));
                        if(curMinValue < minValue){
                            curMinValue = minValue;
                        }
                        if(curMinValue > maxValue){
                            curMinValue = maxValue;
                        }
                        if(curMaxValue > maxValue){
                            curMaxValue = maxValue;
                        }
                        if(curMaxValue < minValue){
                            curMaxValue = minValue;
                        }
                        if(curMinValue > curMaxValue){
                            if(curMinValue < maxValue) curMaxValue = curMinValue;
                            else if(curMaxValue > minValue) curMinValue = curMaxValue;
                        }
                        this_slider.slider( 'values', 0, curMinValue );
                        this_slider.slider( 'values', 1, curMaxValue );
                        this_slider.slider('option', 'stop').call(this_slider);
                    }, tmFilters.config.filter_delay);
                });
                
            });
            
        }
        
    },
    
    
    filtersPreSubmit: function( event ){
        
        var filters = tmFilters.getFilters();
	    var is_select = typeof event != 'undefined' && $(event.target).is('select');
	
        tmFilters.filtered = !$.isEmptyObject( filters );
        
        if ( typeof window.flt_data != 'undefined' ) {
            
            tmFilters.result_ids = [];
            
            for (var i in window.flt_data.products) {
                tmFilters.result_ids.push( parseInt( window.flt_data.products[i].id ) );
            }
            
            tmFilters.search( filters );
            var result_ids = $.extend( [], tmFilters.result_ids );
            
            tmFilters.countAllFIlters( filters );
            
        }else{
            
            tmFilters.config['filters_type'] = 'none';
            var result_ids = [];
            
        }
        
        if ( !is_select && tmFilters.config['filters_type'] != 'none' && result_ids.length == 0 ) {
            
            //если товаров не найдено, изменяем значение на ближайшее
            if ( typeof event != 'undefined' && $(event.target).is( tmFilters.config.filter_slider ) ) {
                
                tmFilters.setbackSlider( event );
                
            }
            
        }
        
    },
    
    /**
     * filtersSubmit
     *
     */
    filtersSubmit: function(){
        
        tmFilters.switchPage(1, false);
        tmFilters.pushState();
        
    },
    
    /**
     * pushState
     *
     */
    pushState: function(){
        
        $('input[name="page_id"]',tmFilters.config.filters_cont).prop('disabled', false);
        
        var form_data = $('form',tmFilters.config.filters_cont).serializeArray();
	    var form_data_push = [];
        var search_uri = '';
        
        if ( !tmFilters.filtered && !tmFilters.sorted ) {
            
            for( var i in form_data ){
                if ( !form_data.hasOwnProperty(i) ) continue;
                if ( $.inArray( form_data[i].name, ['page'] ) > -1 ) {
                    if( form_data[i].name != 'page' || form_data[i].value != 1 ) {
                        search_uri += '&' + form_data[i].name + '=' + form_data[i].value;
                        form_data_push.push( form_data[i] );
                    }
                }
                if ( $.inArray( form_data[i].name, ['page_id'] ) > -1 ) {
                    form_data_push.push( form_data[i] );
                }
            }
            
        }else if ( tmFilters.sorted && !tmFilters.filtered ) {
            
            for( var i in form_data ){
		        if ( !form_data.hasOwnProperty(i) ) continue;
                if ( $.inArray( form_data[i].name, ['page','sortby','sortdir','limit'] ) > -1 ) {
                    if( form_data[i].name != 'page' || form_data[i].value != 1 ) {
                        search_uri += '&' + form_data[i].name + '=' + form_data[i].value;
                        form_data_push.push( form_data[i] );
                    }
                }
                if ( $.inArray( form_data[i].name, ['page_id'] ) > -1 ) {
                    form_data_push.push( form_data[i] );
                }
            }
            
        }else{
            
            for( var i in form_data ){
		        if ( !form_data.hasOwnProperty(i) ) continue;
                if ( $.inArray( form_data[i].name, ['page_id'] ) == -1 ) {
                    if( form_data[i].name != 'page' || form_data[i].value != 1 ){
                        search_uri += '&' + form_data[i].name + '=' + form_data[i].value;
                        form_data_push.push( form_data[i] );
                    }
                }
                if ( $.inArray( form_data[i].name, ['page_id'] ) > -1 ) {
                    form_data_push.push( form_data[i] );
                }
            }
            
        }
        
        if(search_uri) search_uri = '?' + search_uri.substring(1);
        
        tmFilters.filtersActive = true;
        var loc_path = window.location.pathname;
        
        window.History.pushState( form_data_push, $('title').text(), loc_path + search_uri );
        
    },
    
    
    /**
     * reload
     *
     */
    reload: function(){

        tmFilters.filtersActive = true;
        this.pushState();

        return true;

    },
    
    
    /**
     * getFilters
     *
     */
    getFilters: function(){
        
        var filters = {};
        
        $( 'input:checkbox,select', tmFilters.config.filters_cont ).each(function(){
	    
            if ( $(this).attr('name').substr(0,2) == 'f_' ) {
		
		if ( $(this).is(':checked') || ( $(this).is('select') && !!$(this).val() ) ) {
		    
		    var flt_name = tmFilters.getFilterName( $(this).attr('name') );
		    var flt_value = $(this).val();
		    
		    if (!!filters[flt_name]) {
			    filters[flt_name].push( flt_value );
		    }else{
			    filters[flt_name] = [ flt_value ];
		    }
		    
		}
                
            }
            
        });
        
        $( tmFilters.config.filter_slider ).each(function(i){
            
            var flt_name = $(this).attr('id').replace('range_','');
            var flt_values = $(this).slider("values");
            var slider_limits = [ $(this).slider("option","min"), $(this).slider("option","max") ];
            
            if(flt_values[0] !== slider_limits[0] || flt_values[1] !== slider_limits[1]){
                filters[flt_name] = flt_values;
            }
            
        });
        
        return filters;
        
    },
    
    
    /**
     * currRate
     *
     */
    currRate: function( flt_name, value, type ){

        if ( typeof type == 'undefined' ) type = 'min';
        if ( flt_name == this.config['price_field'] && typeof tmFiltersOptions != 'undefined' && tmFiltersOptions.currency_rate ) {

            var curr_default = tmFiltersOptions.currency_default ? parseInt( tmFiltersOptions.currency_default ) : 1;
            var curr_rates_arr = tmFiltersOptions.currency_rate || [];

            var shk_cindex = document.cookie.indexOf("shk_currency=") > -1
                ? document.cookie.indexOf("shk_currency=") + new String("shk_currency=").length
                : -1;
            var shk_currency = shk_cindex > -1
                ? parseInt( document.cookie.substring(shk_cindex, shk_cindex + 1) )
                : 1;

            if ( shk_currency !== curr_default ) {

                var temp_arr = curr_rates_arr[shk_currency-1];
                var rate = temp_arr.value && !isNaN(temp_arr.value)
                    ? parseFloat(temp_arr.value.replace(',','.'))
                    : 1;
                temp_arr = curr_rates_arr[curr_default-1];
                var temp_rate = temp_arr.value && !isNaN(temp_arr.value)
                    ? parseFloat(temp_arr.value.replace(',','.'))
                    : 1;//курс базовой валюты
                var rate_ratio = temp_rate / rate;
                value = value * rate_ratio;

                if( rate_ratio < 1 ){
                    value = type == 'min' ? value - 1 : value + 1;
                }
                value = parseFloat( value.toFixed(2) );

            }

        }

        return value;

    },
    
    /**
     * getFilterNmae
     *
     */
    getFilterName: function( name ){

        if ( !name ) return '';

        name = name.substr(2);
        if ( name.indexOf('[') > -1 ) {
            name = name.substr(0,name.indexOf('['));
        }

        return name;
    },
    
    
    /**
     * search
     *
     */
    search: function(filters){
        
        for(key in filters){
            
            var flt_key = 'eq';
            
            //numeric values
            if( $.inArray( key, tmFilters.config.numeric ) > -1 ){
                
                flt_key = 'between';
                
            //multitags
            }else if( $.inArray( key, tmFilters.config.multitags ) > -1 ){
                
                flt_key = 'like';
                
            //text values
            }else{
                
                flt_key = 'eq';
                
            }
            
            tmFilters.filter( key+':'+flt_key, filters[key] );
            
        }
        
    },
    
    
    /**
     * filter
     *
     */
    filter: function(flt_name, flt_value){
        
        var flt = flt_name.split(':');
        
        //filter
        for (var i in window.flt_data.products) {
            
            if ( $.inArray( parseInt( window.flt_data.products[i].id ), tmFilters.result_ids ) == -1 ) {
                continue;
            }
            
            var remove = true;
            
            if (!!window.flt_data.products[i][flt[0]]) {
                
                switch (flt[1]) {
                    case 'between':
                        
                        var p_value = parseFloat( window.flt_data.products[i][flt[0]].replace(',','.') );

                        //если включена мультивалютность, пересчитываем цену по курсу
                        if ( tmFilters.config.multi_currency ) {
                            p_value = tmFilters.currRate( flt[0], p_value );
                        }

                        if ( p_value >= flt_value[0] && p_value <= flt_value[1] ) {
                            remove = false;
                        }
                        
                    break;
                    case 'like':
                        
                        for( var ii in flt_value ){
                            
                            if ( window.flt_data.products[i][flt[0]].indexOf( tmFilters.config.guard_key+flt_value[ii]+tmFilters.config.guard_key ) > -1 ) {
                                remove = false;
                                break;
                            }
                        }
                        
                    break;
                    case 'eq':
                        
                        if ( $.inArray( window.flt_data.products[i][flt[0]],  flt_value) > -1 ) {
                            remove = false;
                        }
                        
                    break;
                }
                
            }
            
            if ( remove ) {
                
                var index = $.inArray( parseInt( window.flt_data.products[i].id ), tmFilters.result_ids );
                tmFilters.result_ids.splice( index, 1 );
                
            }
            
        }
        
    },
    
    /**
     * countAllFIlters
     *
     */
    countAllFIlters: function( filters ){
        
        if ( typeof window.flt_data == 'undefined' ) return;
        
        var temp_ids = [];
        for (var i in window.flt_data.products) {
            temp_ids.push( parseInt( window.flt_data.products[i].id ) );
        }
        
        $( 'input:checkbox', tmFilters.config.filters_cont ).each(function(){
	    
            if ( $(this).attr('name').substr(0,2) == 'f_' ) {
                
                tmFilters.result_ids = $.extend([], temp_ids);
                
                var elem = $(this);
                var name = elem.attr('name');
                var flt_name = name.substr(2);
                flt_name = flt_name.substr(0,flt_name.indexOf('['));
                var flt_value = elem.val();
                
                var temp_filters = $.extend(true,{},filters);
		
                if ( $(this).is('input:checked') ) {
                    
                    var index = $.inArray( flt_value, temp_filters[flt_name] );
                    temp_filters[flt_name].splice( index, 1 );
                    if (temp_filters[flt_name].length == 0) {
                        delete temp_filters[flt_name];
                    }
                    
                }{
                    
                    temp_filters[flt_name] = [ flt_value ];
                    
                }
		
                tmFilters.search( temp_filters );
                
                tmFilters.updateMarkerCount( elem, tmFilters.result_ids.length );
                
            }
            
        });
        
    },
    
    
    /**
     * updateMarkerCount
     *
     */
    updateMarkerCount: function(elem, count){
        
        var elem_parent = elem.parent();
        
        if ( tmFilters.config['filters_type'] != 'only_block' ) {
	    
            if ( elem.is('input:checked') ) {
                
                $('label',elem_parent).next('sup').remove();
                
            }else{
                
		if ( $('label',elem_parent).next('sup').length == 0 ) {
		    $('label',elem_parent).after('<sup>'+count+'</sup>');
		}else{
		    $('label',elem_parent).next('sup').text(count);
		}
		
            }
        }
        
        if (count == 0) {
            
            elem_parent.addClass('unactive');
            if ( tmFilters.config['filters_type'] != 'only_block' ){
                $('label',elem_parent).next('sup').remove();
            }
            $('input:checkbox,select',elem_parent).prop('disabled','disabled');
            
        }else{
            
            elem_parent.removeClass('unactive');
            $('input:checkbox,select',elem_parent).prop('disabled', false);
            
        }
        
    },
    
    
    /**
     * setbackSlider
     *
     */
    setbackSlider: function( event ){
        
        var this_slider = $( event.target );
        var activeHandler = $( event.currentTarget.activeElement );
        var order = activeHandler.prevAll('.ui-slider-handle').length;
        var filters = tmFilters.getFilters();
        var flt_name = this_slider.attr('id').replace('range_','');
        var slider_limits = [ this_slider.slider("option","min"), this_slider.slider("option","max") ];
        var prevValue = activeHandler.data('value');
        
        if (typeof prevValue != 'undefined') {
            
            this_slider.slider( "values", order, prevValue );
            $('#'+flt_name+( order == 0 ? 'Min' : 'Max' )).val( prevValue );
            
            tmFilters.filtersPreSubmit( event );
            
        }
        
    },
    
    
    /**
     * filterSelected
     *
     * Отмечает текущие фильтры и сортировки по параметрам в адресной строке
     */
    filterSelected: function(){

        var f_state = this.getUrlVars();
        if(this.isEmpty(f_state)) return;
        var f_count = 0, fsort_count = 0;

        //Сброс чекбоксов
        $( 'input:checkbox', tmFilters.config.filters_cont ).prop('checked', false);

        for(var i in f_state){
            if(f_state[i].length == 0) continue;
            var f_name = i.substr(0,2) == 'f_' ? i.substr(2) : i;

            if ( $.inArray( f_name, ['page'] ) > -1 ) continue;

            //sort
            if ( $.inArray( f_name, ['sortby','sortdir'] ) > -1 ) {

                fsort_count++;

                var select_sortby = $('select.f_sortby');
                var select_sortdir = $('select.f_sortdir');

                //sortby
                if( f_name == 'sortby' && select_sortby.length > 0 ){
                    select_sortby.val(f_state[i]);
                    if( !!select_sortby.data("selectBoxSelectBoxIt") ){
                        select_sortby.data("selectBoxSelectBoxIt").refresh();
                    }
                }
                //sortdir
                else if( f_name == 'sortdir' && select_sortdir.length > 0 ){
                    select_sortdir.val(f_state[i]);
                    if( !!select_sortby.data("selectBoxSelectBoxIt") ){
                        select_sortdir.data("selectBoxSelectBoxIt").refresh();
                    }
                }

            }
            //limit
            else if( f_name == 'limit' &&  !isNaN(f_state[i]) ){

                var select_limit = $('select.f_limit');
                if ( select_limit.length > 0 ) {
                    select_limit.val(f_state[i]);
                    if( !!select_limit.data("selectBoxSelectBoxIt") ){
                        select_limit.data("selectBoxSelectBoxIt").refresh();
                    }
                }

            }
            //slider
            else if( $.inArray( f_name, tmFilters.config.numeric ) > -1 ){

                if(f_state[i].from && f_state[i].to){
                    $('#'+f_name+'Min').val(f_state[i].from);
                    $('#'+'range_'+f_name).slider("values",0,f_state[i].from);
                    $('#'+f_name+'Max').val(f_state[i].to);
                    $('#'+'range_'+f_name).slider("values",1,f_state[i].to);
                }

            //filter
            }else{

                f_count++;

                //checkboxes
                if( $( 'input:checkbox', tmFilters.config.filters_cont ).length > 0 ){

                    var input_name = $.inArray( f_name, tmFilters.config.multitags ) > -1 ? 'f_'+f_name+'[like][]' : 'f_'+f_name+'[]';
                    var f_input = $( 'input[name="'+input_name+'"]:checkbox', tmFilters.config.filters_cont );

                    if ( f_input.length > 0 ) {

                        f_input.each( function(){
                            if( $.inArray($(this).val(), f_state[i] ) > -1){
                            $(this).prop('checked','checked');
                            $(this).parent().addClass('active');
                            }
                        } );

                    }

                //selects
                }else{

                    var f_select = $( 'select[name="f_'+f_name+'"]', tmFilters.config.filters_cont );

                    f_select.val( f_state[i] );
                    if( !!f_select.data("selectBoxSelectBoxIt") ){
                        f_select.data("selectBoxSelectBoxIt").refresh();
                    }

                }

            }

        }

        if ( f_count > 0 ) { tmFilters.filtered = true; }
        if ( fsort_count > 0 ) { tmFilters.sorted = true; }

    },
    
    
    /**
     * getUrlVars
     *
     */
    getUrlVars: function(){

        var vars = {}, hash, is_arr = false;

        var location_hash = document.location.search.length >= 3 ? document.location.search.substr(1) : window.location.hash.substr(1);
        location_hash = decodeURIComponent(location_hash.replace(/\+/g, ' '));
        if (location_hash.indexOf('?') > -1) location_hash = location_hash.substr(location_hash.indexOf('?')+1);
        if (location_hash.length == 0) return vars;

        var hashes = location_hash.split('&');

        for(var i = 0; i < hashes.length; i++){

            hash = hashes[i].split('=');
            if (hash[0].indexOf('[from]') > -1 || hash[0].indexOf('[to]') > -1) {
                var f_name = hash[0].replace(/\[.*\]/,'');
                if (typeof vars[f_name] == 'undefined') {
                    vars[f_name] = {};
                }
                if (hash[0].indexOf('[from]') > -1) {
                    vars[f_name].from = parseFloat(hash[1]);
                }else{
                    vars[f_name].to = parseFloat(hash[1]);
                }
            }else if (hash[0].indexOf('[') > -1) {
                f_name = hash[0].substr(0,(hash[0].indexOf('[')));
                if (typeof vars[f_name] == 'undefined') {
                    vars[f_name] = [];
                }
                vars[f_name].push(hash[1]);
            }else{
                vars[hash[0]] = hash[1];
            }

        }

        return vars;

    },
    
    
    /**
     * isEmpty
     *
     */
    isEmpty: function(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop))
            return false;
        }
        return true;
    },
    
    
    /**
     * ajaxRequest
     *
     */
    ajaxRequest: function( state_data ){
        
        if ( typeof state_data == 'undefined' ) {
            $('input[name="page_id"]',tmFilters.config.filters_cont).prop('disabled', false);
            state_data = $('form',tmFilters.config.filters_cont).serializeArray();
        }
        
        tmFilters.ajaxPreload( $(tmFilters.config.products_cont), true );

        //tm_onFilterBefore
        if ( typeof tm_onFilterBefore == 'function' ) {
            tm_onFilterBefore( state_data );
        }

        jQuery.ajax({
            url: tmFilters.config.base_url + tmFilters.config.ajax_url,
            type: "GET",
            cache: false,
            data: state_data,
            dataType: 'json',
            success: function(response) {
                
                if (typeof response.prod_list != 'undefined') {
		    
                    $(tmFilters.config.products_cont).html( response.prod_list );

                    $('html,body').animate({
                        scrollTop: Math.round($(tmFilters.config.products_cont).position().top)
                    });
		    
                }
		
                if (typeof response.pages != 'undefined'){
                    $(tmFilters.config.pages_cont1).html( response.pages );
                    if ( tmFilters.config.pages_cont2.length > 0 ){
                        $(tmFilters.config.pages_cont2).html( response.pages );
                    }
                }
                
                tmFilters.ajaxPreload( $(tmFilters.config.products_cont), false );

                //tm_onFilterAfter
                if ( typeof tm_onFilterAfter == 'function' ) {
                    tm_onFilterAfter( response.total, response.pageCount, response.onPageLimit );
                }

            },
            error: function(jqXHR,textStatus,errorThrown){
                if(typeof(console)!='undefined') console.log(jqXHR,textStatus,errorThrown);
            }
        });
        
    },
    
    /**
     * resetFilters
     *
     */
    resetFilters: function(){
        
        $('input:checkbox', tmFilters.config.filters_cont)
            .prop('checked', false);

        $('select', tmFilters.config.filters_cont).each(function(){
            if ( $(this).attr('name').indexOf('f_') == 0 ) {
                $(this).val('');
            }
        });

        if( tmFilters.config.filter_slider.length > 0 ){
            $( tmFilters.config.filter_slider ).each(function(){
                var this_slider = $(this);
                var flt_field_cont = $(this).next(tmFilters.config.filter_slider_cont);
                var flt_min_field = $("input[type='text']:eq(0)",flt_field_cont);
                var flt_max_field = $("input[type='text']:eq(1)",flt_field_cont);
                flt_min_field.val(this_slider.slider("option",'min'));
                this_slider.slider("values",0,this_slider.slider("option",'min'));
                flt_max_field.val(this_slider.slider("option",'max'));
                this_slider.slider("values",1,this_slider.slider("option",'max'));
            });
        }
        
        tmFilters.filtersPreSubmit();
        
        tmFilters.switchPage(1, false);
        tmFilters.filtersSubmit();
        
    },
    
    /**
     * ajaxPreload
     *
     */
    ajaxPreload: function(target, action){
        
        if(action==true){
            
            $('#ajax_loader').remove();
            
            target
            .css({'position':'relative','opacity':0.4})
            .prepend('<div id="ajax_loader"></div>');
            
            $('#ajax_loader')
            .css({
                'width': target.width()+'px',
                'height': target.height()+'px',
                'background': "url('" + tmFilters.config.base_url + tmFilters.config.ajax_loader + "') center center no-repeat transparent"
            });
            
        }else{
            target.css({'opacity':1});
            $('#ajax_loader').remove();
        }
        
    },
    
    /**
     * pagesInit
     *
     */
    ajaxPagesInit: function(){
        
        $(document)
        .keydown(function(event){
            if(event.which==17) tmFilters.keyCtrl = true;
        })
        .keyup(function(event){
            if(event.which==17) tmFilters.keyCtrl = false;
            if($('#ajax_loader').length > 0) return;
            
            if(tmFilters.keyCtrl && event.which==37){
                var next_link = $(tmFilters.config.active_page_selector, tmFilters.config.pages_cont1).prev('a');
                if( next_link.length == 0 ) next_link = $(tmFilters.config.active_page_selector, tmFilters.config.pages_cont1).parent().prev().children('a');
                var next_page = next_link.length > 0 ? parseInt(next_link.text()) : 0;
            }
            if(tmFilters.keyCtrl && event.which==39){
                var next_link = $(tmFilters.config.active_page_selector, tmFilters.config.pages_cont1).next('a');
                if(next_link.length == 0) next_link = $(tmFilters.config.active_page_selector, tmFilters.config.pages_cont1).parent().next().children('a');
                var next_page = next_link.length > 0 ? parseInt(next_link.text()) : 0;
            }
            
            if(!!next_page && next_page > 0){
                
                tmFilters.switchPage(next_page);
                
            }
        });
        
        var pages_selector = tmFilters.config.pages_cont1+' a';
        if (tmFilters.config.pages_cont2.length > 0) pages_selector += ','+tmFilters.config.pages_cont2+' a';
        
        $(document).on('click', pages_selector, function(){

            if ( !$(this).is(tmFilters.config.active_page_selector) ) {

                var href = $(this).attr('href');
                var temp_arr = href.split('page=');
                var page = temp_arr.length>=2 ? parseInt(temp_arr[1]) : 1;

                tmFilters.switchPage(page);

            }
            return false;
        });
        
    },
    
    /**
     * switchPage
     *
     */
    switchPage: function(page, go){
        
        if (typeof go == 'undefined') go = true;
        
        if (!!page && !isNaN(page)) {

	        tmFilters.setFormValue( 'page', page );
            
            if(go) tmFilters.pushState();
            
        }
    },
    
    /**
     * changeOrder
     *
     */
    changeOrder: function(elem){
        
        var name = $(elem).attr('name');
        var value = $(elem).val();
        
        if ( $.inArray( name, ['sortby','sortdir','limit'] ) > -1 ) {
            
	        this.setFormValue( name, value );

            this.sorted = true;
            this.switchPage(1, false);
            this.pushState();
            
        }
        
    },
    
    /**
     * setFormValue
     *
     */
    setFormValue: function( name, value ){

        if ( $('input[name="'+name+'"],select[name="'+name+'"]', tmFilters.config.filters_cont).length == 0 ) {
            $('form', tmFilters.config.filters_cont).append( '<input type="hidden" name="'+name+'" />' );
        }

        $('input[name="'+name+'"],select[name="'+name+'"]', tmFilters.config.filters_cont).first().val(value);

        return true;

    },

    getNumber: function(string){
        string = string.replace(/,/g, '.');
        string = string.replace(/[^\d\.]/g, '');
        if(!string) string = '0';
        if(string.indexOf('.') > -1 && string.indexOf('.') != string.lastIndexOf('.')){
            var arr = string.split('.');
            string = arr.shift() + '.';
            string += arr.join('');
        }
        return parseFloat(string).toFixed(2);
    }
    
};

History.Adapter.bind(window,'statechange',function(){
    
    //HTML 5 replaceState supported?
    var html5History_support = window.history && window.history.pushState && !window.History.options.html4Mode;
    var f_state = window.History.getState();
    
    //При фильтрации сразу отправляем запрос
    if ( tmFilters.filtersActive ) {
        
        tmFilters.filtersActive = false;
        tmFilters.ajaxRequest( f_state.data );
        
    //Если это переходы по истории
    }else{
        
        if ( !$.isArray( f_state.data ) ) {
            f_state.data = [ { name: "page_id", value: $('input[name="page_id"]',tmFilters.config.filters_cont).val() } ];
        }
        
        tmFilters.ajaxRequest( f_state.data );
        tmFilters.filterSelected();
	
    }
    
});
