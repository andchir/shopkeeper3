
/**
 * tmFilters
 *
 * tagManager 2.0rc4 - http://modx-shopkeeper.ru/
 * Andchir
 * 
 */

/**
 * tmFilters
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
        slider_steps: [10, 0.1],//Число единиц шага слайдера
        sortby: 'pagetitle',//Имя поля сортировки по умолчанию
        sortdir: 'asc',//Направление сортировки по умолчанию
        numeric: ['price', 'weight'],//Имена доп. полей с числовыми значениями
        multitags: ['tag'],//Имена доп. полей с множественными значениями
        limit: 10, //Число товаров на странице по умолчанию
	price_field: 'price',//Название поля или TV цены товара
	multi_currency: true,//Мультивалютность включить / выключить (true/false)
        filter_mode: 'get' //hash | get (пока доступно только get)
    },
    /* ########################################### */
    
    flt_timer: null,
    
    /**
     * init
     */
    init: function( conf ){
        
        $.extend( this.config, conf );
        
        tmFilters.slidersInit();
        tmFilters.sortingInit();
        tmFilters.filterSelected();
        
    },
    
    /**
     * sortingInit
     * 
     * Инициализация сортировки
     */
    sortingInit: function(){
        
        $('select.f_sortby,select.f_sortdir,*.f_limit').on('change', tmFilters.sortingAction);
        
    },
    
    
    /**
     * sortingAction
     *
     * Действие при изменении параметров сортировки, если селекты находятся не внутри формы
     */
    sortingAction: function(event){
        
        clearTimeout(tmFilters.flt_timer);
        
        tmFilters.flt_timer = setTimeout(function(){
            
            var elem = event.currentTarget;
            
            if ($(elem).is('.f_limit') && !isNaN(elem.value)) {
                
                var limit_field = $('[name="limit"]',tmFilters.config.filters_cont);
                if (limit_field.length > 0) {
                    limit_field.val(elem.value);
                    $('form',tmFilters.config.filters_cont).submit();
                }
                
            }else{
                
                if ($('select.f_sortby').length > 0) {
                    var sortby = $('select.f_sortby').val();
                    var sortby_field = $('[name="sortby"]',tmFilters.config.filters_cont);
                    if (sortby_field.length > 0) {
                        sortby_field.val(sortby);
                    }
                }
                if ($('select.f_sortdir').length > 0) {
                    var sortdir = $('select.f_sortdir').val();
                    var sortdir_field = $('[name="sortdir"]',tmFilters.config.filters_cont);
                    if (sortdir_field.length > 0) {
                        sortdir_field.val(sortdir);
                    }
                }
                
                $('form',tmFilters.config.filters_cont).submit();
                
            }
            
        }, 1000);
        
    },
    
    
    /**
     * slidersInit
     *
     * Инициализация слайдеров
     */
    slidersInit: function(){
        
        if ( $('.range-slider').length > 0 ) {
            
            $('.range-slider').each(function(i){
                
                var this_slider = $(this);
                var slider_wrapper = this_slider.parent();
		var flt_name = tmFilters.getFilterName( $('input:text:eq(0)',slider_wrapper).attr('name') );
                var minValue = parseFloat($('input:text:eq(0)',slider_wrapper).val().replace(',','.'));
                var maxValue = parseFloat($('input:text:eq(1)',slider_wrapper).val().replace(',','.'));
                
		//при мультивалютности пересчитываем цену по курсу
		if ( tmFilters.config.multi_currency && flt_name == tmFilters.config['price_field'] ) {
		    minValue = tmFilters.currRate( flt_name, minValue );
		    maxValue = tmFilters.currRate( flt_name, maxValue );
		    $('input:text:eq(0)',slider_wrapper).val( minValue.toString() );
		    $('input:text:eq(1)',slider_wrapper).val( maxValue.toString() );
		}
		
		var slider_step = maxValue - minValue < 100 ? 0.1 : 10;
		
                this_slider.slider({
                    min: minValue,
                    max: maxValue,
                    step: slider_step,
                    values: [
                        minValue,
                        maxValue
                    ],
                    range: true,
                    stop: function(event, ui) {
                        $('input:text:eq(0)',slider_wrapper).val(this_slider.slider("values",0));
                        $('input:text:eq(1)',slider_wrapper).val(this_slider.slider("values",1));
                    },
                    slide: function(event, ui){
                        $('input:text:eq(0)',slider_wrapper).val(this_slider.slider("values",0));
                        $('input:text:eq(1)',slider_wrapper).val(this_slider.slider("values",1));
                    }
                });
                
            });
            
        }
        
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
     * currRate
     *
     */
    currRate: function( flt_name, value ){
	
	if ( flt_name == this.config['price_field'] && !!tmFiltersOptions && !!tmFiltersOptions.currency_rate ) {
	    
	    var curr_default = !!tmFiltersOptions.currency_default ? parseInt( tmFiltersOptions.currency_default ) : 1;
	    var curr_rates_arr = tmFiltersOptions.currency_rate.split('||');
	    
	    var shk_cindex = document.cookie.indexOf("shk_currency=") > -1 ? document.cookie.indexOf("shk_currency=") + new String("shk_currency=").length : -1;
            var shk_currency = shk_cindex > -1 ? parseInt( document.cookie.substring(shk_cindex,shk_cindex+1) ) : 1;
	    
	    if ( shk_currency != curr_default ) {
		
		var temp_arr = curr_rates_arr[shk_currency-1].split('==');
		var rate = !!temp_arr[1] && !isNaN(temp_arr[1]) ? parseFloat(temp_arr[1].replace(',','.')) : 1;
		var temp_arr = curr_rates_arr[curr_default-1].split('==');
		var temp_rate = !!temp_arr[1] && !isNaN(temp_arr[1]) ? parseFloat(temp_arr[1].replace(',','.')) : 1;//курс базовой валюты
		var rate_ratio = temp_rate / rate;
		value = value * rate_ratio;
		value = parseFloat( value.toFixed(2) );
		
	    }
	    
	}
	
	return value;
	
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
     * filterSelected
     *
     * Отмечает текущие фильтры и сортировки по параметрам в адресной строке
     */
    filterSelected: function(){
	
	var f_state = this.getUrlVars();
	if(this.isEmpty(f_state)) return;
        
	//Сброс чекбоксов
	$( 'input:checkbox', tmFilters.config.filters_cont ).removeAttr('checked');
	
	for(var i in f_state){
	    if(f_state[i].length == 0) continue;
	    var f_name = i.substr(0,2) == 'f_' ? i.substr(2) : i;
	    
	    if ( $.inArray( f_name, ['page'] ) > -1 ) continue;
            
	    //sort
	    if ( $.inArray( f_name, ['sortby','sortdir'] ) > -1 ) {
		
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
	
    }
    
};
