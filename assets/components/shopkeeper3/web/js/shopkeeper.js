
/**************************
* 
* http://modx-shopkeeper.ru/
* Shopkeeper 3.2.7pl3
* Shopping cart for MODX 2.x Revolution
* 
**************************/

/**
 * Shopkeeper
 */
var SHK = {
    
    timer: null,
    settings_qs: '',
    data: { price_total: 0, items_total: 0, items_unique_total: 0, delivery_price: 0, delivery_name: '' },
    options: {
        site_base_url: $('base').length > 0 ? $('base:first').attr('href') : window.location.protocol+'//'+window.location.host+'/',
        prodCont: 'div.shk-item',
        counterField: false,
        tocartImage_width: 70,
        tocartImage_height: 70,
        counterFieldCart: true,
        changePrice: true,
        flyToCart: 'helper',
        buttons_class: '',
        noLoader: false,
        allowFloatCount: false,
        animCart: true,
        goToOrderFormPage: false,
        orderFormPageUrl: '/',
        debug: false,
        is_first: false,
        propertySetNum: 0,
        paramsSelector: 'select.shk_param,input.shk_param:checked,input.shk_param:text',
        helperHtml: '<div id="shk_prodHelper"><div id="shk_prodHelperName"></div> \
        <div class="shs-count" id="shk_prodCount"><input type="text" size="2" name="count" value="1" maxlength="5" title="'+langTxt['count']+'" /> \
        </div><div id="shk_buttons"><button class="shk-but {buttons_class}" id="shk_confirmButton">'+langTxt['continue']+'</button> \
        <button class="shk-but {buttons_class}" id="shk_cancelButton">'+langTxt['cancel']+'</button></div></div>'
    },
    
    init: function( opts ){

        var self = this;
        
        $.fn.reverse = function() {
            return this.pushStack(this.get().reverse(), arguments);
        };
        
        $.each(['prev', 'next'], function(unusedIndex, name) {
            $.fn[name + 'AllElem'] = function(parentSelector,matchExpr) {
                var $all = $(parentSelector).find('*').andSelf();
                $all = (name == 'prev') ? $all.slice(0, $all.index(this)).reverse() : $all.slice($all.index(this) + 1);
                if (matchExpr) $all = $all.filter(matchExpr);
                return $all;
            };
        });
        
        if( typeof SHKbeforeInitCallback == 'function') SHKbeforeInitCallback();
        
        if ( typeof opts != 'undefined' ) {
            $.extend( self.options, opts );
        }
        if( this.options.changePrice === '0' ){ this.options.changePrice = false; }
        
        this.options.helperHtml = this.options.helperHtml.replace( /\{buttons_class\}/g, this.options.buttons_class );
        
        //функция на событие отправки (submit) формы товара
        $(document).on( 'submit', this.options.prodCont + ' form', function(){
            self.toCart(this);
            return false;
        });
        
        //функция на событие клик по кнопку "Удалить товар из корзины"
        $(document).on( 'click', '[data-shopcart] .shk-del', function(){
            
            var parent = $(this).closest('tr,li');
            var index;
            if ( parent.length > 0 ) {
                index = parent.prevAll().length;
            }else{
                var href = $(this).attr('href');
                var regex = /n=(\d+)/;
                var results = regex.exec( href );
                index = ( results && results.length > 1 ) ? parseInt( results[1] ) : 0;
            }
            self.deleteItem( index, this );
            
            return false;
        });
        
        //функция на событие клик по ссылке "Очистить корзину"
        $(document).on('click', '#shk_butEmptyCart', function(){
            self.deleteItem( 'all', this );
            return false;
        });
        
        //Кнопки "больше" и "меньше" для всех полей с именем "shk-count"
        if( this.options.counterField ){
            this.setCounterToField( $('input[name="shk-count"]') );
        }
        
        //вызов инициализации добавления кнопок "больше" и "меньше" к полям кол-ва в корзине
        if( this.options.counterFieldCart ) this.counterFieldCartInit();

        this.additOpt();
        
        if( typeof( SHKafterInitCallback )=='function' ) SHKafterInitCallback();
        if( typeof( SHKloadCartCallback )=='function' ) SHKloadCartCallback(true);
        
    },
    
    round: function round (value, precision, mode) {
        var m, f, isHalf, sgn;
        precision |= 0;
        m = Math.pow(10, precision);
        value *= m;
        sgn = (value > 0) | -(value < 0);
        isHalf = value % 1 === 0.5 * sgn;
        f = Math.floor(value);
      
        if (isHalf) {
            switch (mode) {
            case 'PHP_ROUND_HALF_DOWN':
                value = f + (sgn < 0);
            break;
            case 'PHP_ROUND_HALF_EVEN':
                value = f + (f % 2 * sgn);
            break;
            case 'PHP_ROUND_HALF_ODD':
                value = f + !(f % 2);
            break;
            default:
                value = f + (sgn > 0);
            }
        }
      
        return (isHalf ? value : Math.round(value)) / m;
    },
    
    isIE: function(){
        return !!window.ActiveXObject;
    },
    
    number_format: function( number, decimals, dec_point, thousands_sep ) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        var out = s.join(dec);
        return out;
    },
    
    numFormat: function(n){
        return this.number_format(n, (Math.floor(n)===n ? 0 : 2), '.', ' ');
    },
    
    /**
     * Показывает блок с подтверждением действий
     */
    showHelper: function( elem, name, noCounter, func ){
        
        if( $(elem).length == 0 ) return;
        if( typeof(func) == 'undefined' || !func ) func = function(){};
        
        $('#shk_prodHelper').remove();
        $('body').append( SHK.options.helperHtml );
        
        $('#shk_cancelButton').click(function(){
            $('#shk_prodHelper').fadeOut( 300, function(){ $(this).remove() } );
            return false;
        });
        $('#shk_confirmButton').click(function(){
            func();
            return false;
        });
        if( noCounter ){
            $('#shk_prodCount').remove();
        }else{
            this.setCounterToField( $('input:text','#shk_prodCount') );
        }
        var elHelper = $('#shk_prodHelper');
        var btPos = this.getCenterPos( elHelper, elem );
        if( name ){
            $('#shk_prodHelperName').html( name );
        }else{
            $('#shk_prodHelperName').remove();
        }
        $('#shk_prodHelper').css( { 'top': btPos.y, 'left': btPos.x } ).fadeIn(500);
        
    },
    
    /**
     * getShopCartWrapper
     *
     */
    getShopCartWrapper: function(){
        var shopCart;
        if ( SHK.options.propertySetNum > 0 ) {
            shopCart = $('[data-shopcart="' + SHK.options.propertySetNum + '"]');
        }else{
            shopCart = $('[data-shopcart]').eq(0);
        }
        return shopCart;
    },
    
    /**
     * Показывает прелоадер
     */
    showLoading: function( show, shopCart ){
        
        if( !SHK.options.noLoader ){
            
            if( typeof shopCart == 'undefined' ) { shopCart = SHK.getShopCartWrapper(); }
            var propertySetNum = shopCart.data('shopcart');
            
            if(show==true){
                
                $('body').append( '<div class="shk_loading shk' + propertySetNum + '"></div>' );
                var loader = $('div.shk_loading:last' );
                var btPos = this.getCenterPos( loader, shopCart );
                loader.css({'top':btPos.y+'px','left':btPos.x+'px'}).fadeIn(300);
                
            }else{
                $( 'div.shk_loading' ).filter( '.shk' + propertySetNum ).fadeOut(300,function(){
                    $(this).remove();
                });
            }
        }
    },
    
    /**
     * Определение координат (позиции) элемента
     */
    getPosition: function(elem){
        var pos = $(elem).offset();
        return { x: pos.left, y: pos.top };
    },
    
    /**
     * Определяет позицию для блока отностительно центра другого блока
     */
    getCenterPos: function( elA, elB, Awidth, Aheight ) {
        Awidth = Awidth || $(elA).outerWidth();
        Aheight = Aheight || $(elA).outerHeight();
        var posB = {},
            cntPos = {};
        posB = this.getPosition( elB );
        cntPos.x = Math.round( ( $(elB).outerWidth() - Awidth ) / 2 ) + posB.x;
        cntPos.y = Math.round( ( $(elB).outerHeight() - Aheight ) / 2 ) + posB.y;
        if( cntPos.x + Awidth > $(window).width() ) {
            cntPos.x = Math.round( $(window).width() - $(elA).outerWidth() ) - 2;
        }
        if( cntPos.x < 0 ) {
            cntPos.x = 2;
        }
        return cntPos;
    },
    
    /**
     * Аякс-запрос для отправки данных и получения HTML-кода обновленной корзины
     */
    ajaxRequest: function( params, refresh ){
        
        if(typeof(refresh)=='undefined') refresh = true;
        
        var shopCart = SHK.getShopCartWrapper();
        var propertySetNum;
        if ( SHK.options.propertySetNum > 0 ) {
            propertySetNum = SHK.options.propertySetNum;
        }else{
            propertySetNum = shopCart.length > 0 ? shopCart.data('shopcart') : '';
        }
        
        params = params || {};
        params.psn = propertySetNum;
        
        SHK.showLoading( true );
        
        $.ajax({
            type: "POST",
            cache: false,
            dataType: 'json',
            url: SHK.options.site_base_url + 'assets/components/shopkeeper3/connector_fe.php',
            data: params,
            success: function(data){
                
                SHK.showLoading( false, shopCart );
                
                if(typeof(data.price_total) != 'undefined') SHK.data.price_total = data.price_total;
                if(typeof(data.items_total) != 'undefined') SHK.data.items_total = data.items_total;
                if(typeof(data.items_unique_total) != 'undefined') SHK.data.items_unique_total = data.items_unique_total;
                if(typeof(data.ids) != 'undefined') SHK.data.ids = data.ids;
                if(typeof(data.delivery_price) != 'undefined') SHK.data.delivery_price = data.delivery_price;
                if(typeof(data.delivery_name) != 'undefined') SHK.data.delivery_name = data.delivery_name;
                SHK.options.propertySetNum = 0;
                
                if(SHK.options.goToOrderFormPage && window.location.href.indexOf(SHK.options.orderFormPageUrl)==-1 && data.items_total>0){
                    window.location.href = SHK.options.orderFormPageUrl;
                    return;
                }
                
                if(refresh){
                    
                    if( window.location.href.indexOf( '/'+SHK.options.orderFormPageUrl ) > -1 ){
                        $('#shk_butOrder').hide();
                    }
                    
                    if ( shopCart.length > 0 ) {
                        
                        var cartHeight = shopCart.height();
                        if(typeof(data.html)!='undefined') shopCart.replaceWith(data.html);
                        if(window.location.href.indexOf('/'+SHK.options.orderFormPageUrl)>-1){
                            $('#shk_butOrder').hide();
                        }
                        var cartheightNew = shopCart.height();
                        if(SHK.options.animCart) SHK.animCartHeight( cartHeight, cartheightNew, shopCart );
                        
                    }
                    
                    if ( SHK.options.is_first ) {
                        
                        if ( $('[data-shopcart]').length > 1 ) {
                            
                            var propertySetNum = parseInt( shopCart.data('shopcart') );
                            $('[data-shopcart]').each( function(){
                                
                                if ( $(this).data('shopcart') != propertySetNum ) {
                                    SHK.options.propertySetNum = $(this).data('shopcart');
                                    SHK.refreshCart( false );
                                }
                                
                            } );
                            
                        } else {
                            if( typeof( SHKloadCartCallback ) == 'function' ) SHKloadCartCallback();
                        }
                        
                    } else {
                        if( typeof( SHKloadCartCallback ) == 'function' ) SHKloadCartCallback();
                    }
                    
                    if( SHK.options.counterFieldCart ) SHK.counterFieldCartInit();
                    
                }
                
                SHK.options.is_first = false;
                
            }
            ,error: function(jqXHR, textStatus, errorThrown){
                if(typeof(console)!='undefined') console.log(textStatus+' '+errorThrown);
            }
        });
    },
    
    /**
     * Удаление товара из корзины
     */
    deleteItem: function( n, el, refresh ){
        
        if(typeof(refresh)=='undefined') refresh = true;
        
        var shopCart = $(el).parents('[data-shopcart]');
        if ( shopCart.length > 0 ) {
            SHK.options.propertySetNum = parseInt( shopCart.data('shopcart') );
        }
        
        SHK.options.is_first = true;
        
        var thisAction = function(){
            if( n != 'all' ){
                var params = { shk_action: 'remove' };
                if ( typeof n == 'object' && n.id ) {
                    params.id = n.id;
                }else{
                    params.n = n;
                }
                SHK.ajaxRequest( params, refresh );
            }else{
                SHK.emptyCart();
            }
            $('#shk_prodHelper').fadeOut( 500,function(){
                $(this).remove();
            });
        };
        
        if( el != null ){
            this.showHelper( el, langTxt['confirm'], true, thisAction );
            $('#shk_confirmButton').text( langTxt['yes'] );
        }else{
            thisAction();
        }
        
        return true;
    },
    
    /**
     * Пересчет кол-ва товара в корзине
     */
    recountItem: function(num,el){
        
        var thisAction = function(){
            var count = $('input:text','#shk_prodCount').val();
            $('#shk_prodHelper').fadeOut(500,function(){
                $(this).remove();
            });
            if( typeof( SHKrecountItemCallback)=='function'){
                if(!SHKrecountItemCallback(count,el)) return;
            }
            var params = { shk_action: 'recount', index: num, count: count };
            SHK.ajaxRequest( params );
        };
        this.showHelper(el,false,false,thisAction);
        el.blur();
        var thisCount = parseFloat($(el).val().replace(',','.'));
        $('input:text','#shk_prodCount').val(thisCount);
        
        return true;
    },
    
    /**
     * Пересчет всех товаров в корзине
     */
    recountItemAll: function(){
        
        var shopCart = SHK.getShopCartWrapper();
        var cartData = $( "form", shopCart ).serializeObject();
        if( typeof(SHKrecountItemCallback) == 'function' ){
            if( !SHKrecountItemCallback( count, el ) ) return false;
        }
        var params = { shk_action: 'recount' };
        if( cartData ) { $.extend( params, cartData ); }
        
        SHK.options.is_first = true;
        
        SHK.ajaxRequest( params );
        
        return true;
    },
    
    /**
     * Добавляет кнопки управления счетчиком для поля
     *
     */
    setCounterToField: function( fields, callback ){
        
        if ( fields.length > 0 ) {
            
            fields.filter(':text').each( function( i ){
                
                var el = $(this);
                
                if ( el.next('button.shk_count_button').length == 0 ) {
                    
                    el.css( { display: 'inline-block' } );
                    el.before( '<button class="shk_count_button ' + SHK.options.buttons_class + '" type="button">-</button>' );
                    el.after( '<button class="shk_count_button ' + SHK.options.buttons_class + '" type="button">+</button>' );
                    
                    var buttons = el.parent().find('button.shk_count_button');
                    
                    buttons.each( function( ii ){
                            
                            $(this)
                            .click( function(){
                                    var cnt = parseInt( el.val() );
                                    if( ii == 0 && cnt > 1 ) el.val( cnt - 1 );
                                    if( ii == 1 ) el.val( cnt + 1 );
                                    if ( typeof callback == 'function' ) { callback( $(this) ); }
                                }
                            );
                            
                        }
                    );
                    
                    el.on('keyup',function(e){
                        var value = $(e.target).val();
                        value = value.replace( /[^\d,\.]+/g, '' );
                        if ( !value ) { value = 1; }
                        $(e.target).val( value );
                        if ( typeof callback == 'function' ) { callback( $(this) ); }
                    });
                    
                }
                
            });
            
        }
        
    },
    
    /**
     * Инициализация добавления кнопок "больше/меньше" к полям кол-ва в корзине
     */
    counterFieldCartInit: function(){
        
        var callback = function(el){
            
            var shopCart = $(el).parents('[data-shopcart]');
            if ( shopCart.length > 0 ) {
                SHK.options.propertySetNum = parseInt( shopCart.data('shopcart') );
            }
            SHK.changeCartItemsCount();
            
        };
        
        SHK.setCounterToField( $('[data-shopcart] input.shk-count'), callback );
        
    },
    
    /**
     * Вызов функции пересчёта общей цены товаров в корзине при изменении кол-ва
     */
    changeCartItemsCount: function(){
        clearTimeout(SHK.timer);
        SHK.timer = setTimeout(function(){
            SHK.recountItemAll();
        },1000);
    },
    
    /**
     * Добавление товара в корзину
     */
    fillCart: function( thisForm, count, refresh ){
        
        if(typeof(refresh)=='undefined') refresh = true;
        
        SHK.options.is_first = true;
        
        var params = { shk_action: 'fill_cart' };
        if ( typeof(count) != 'undefined' && count != '' ) {
            params.count = count;
        }
        var formData = typeof(thisForm)=='object' ? $(thisForm).serializeObject() : { "shk-id": thisForm };
        if( typeof( SHKfillCartCallback)=='function') SHKfillCartCallback(thisForm);
        
        $.extend( params, formData );
        
        this.ajaxRequest( params, refresh );
        
    },
    
    /**
     * Визуальный эффект перед добавлением товара в корзину
     */
    toCart: function(thisForm){
        
        var self = this;
        var el = $(':submit,input[type="image"]',thisForm).eq(0);
        var name = '';
        if( $('input[name="shk-name"]',thisForm).length > 0 ){
            name = $("input[name='shk-name']",thisForm).val();
        }else if( $( "h3", thisForm ).length > 0 ){
            name = $( "h3", thisForm ).text();
        }
        
        switch(SHK.options.flyToCart){
            
            //&flyToCart=`helper`
            case 'helper':
                
                var thisAction = function(){
                    if( typeof( SHKtoCartCallback) == 'function' ){
                        if(!SHKtoCartCallback(thisForm)) return false;
                    }
                    var cartPos = self.getCenterPos( $('#shk_prodHelper'), SHK.getShopCartWrapper() );
                    var count = $('#shk_prodCount').length > 0 && $('input:text','#shk_prodCount').val().length > 0
                        ? parseFloat( $('input:text','#shk_prodCount').val().replace(',','.') )
                        : '';
                    $('#shk_prodHelper').animate({
                        top: cartPos.y,
                        left: cartPos.x
                    },700).fadeOut(500,function(){
                        $(this).remove();
                        SHK.fillCart(thisForm,count);
                    });
                }
                this.showHelper( el, name, SHK.options.noCounter, thisAction );
                
            break;
            
            //&flyToCart=`image`
            case 'image':
                
                if( typeof( SHKtoCartCallback) == 'function' ){
                    if(!SHKtoCartCallback(thisForm)) return false;
                }
                var parent = $(thisForm).parents( SHK.options.prodCont );
                var image = $( 'img.shk-image:first', parent );
                if( $(image).length > 0 ){
                    var cart = SHK.getShopCartWrapper();
                    var btPos = this.getPosition(image);
                    var cartPos = this.getCenterPos( image, cart, SHK.options.tocartImage_width, SHK.options.tocartImage_height );
                    $('img.shk-image:first',parent)
                    .clone(false)
                    .appendTo('body')
                    .css({'top':btPos.y+'px','position':'absolute','left':btPos.x+'px','opacity':0.75})
                    .animate({
                        top: cartPos.y+'px',
                        left: cartPos.x+'px',
                        width: SHK.options.tocartImage_width + 'px',
                        height: SHK.options.tocartImage_height + 'px'
                    },700).fadeOut(500,function(){
                        $(this).remove();
                        SHK.fillCart( thisForm, 0 );
                    });
                }else{
                    this.fillCart( thisForm, 0 );
                }
                
                this.showHelper( el, '<div class="msg">' + langTxt['addedToCart'] + '</div>', true );
                
                $('#shk_buttons').hide();
                clearTimeout( SHK.timer );
                SHK.timer = setTimeout(function(){
                    $('#shk_prodHelper').fadeOut(500,function(){
                        $('#shk_prodHelper').remove();
                    });
                },1000);
                
            break;
            
            //&flyToCart=`scrollimage`
            case 'scrollimage':
                
                if( typeof( SHKtoCartCallback) == 'function' ){
                    if(!SHKtoCartCallback(thisForm)) return false;
                }
                var parent = $(thisForm).parents(SHK.options.prodCont);
                var image = $('img.shk-image:first',parent);
                
                if( $(image).length > 0 ){
                    
                    var yScroll = SHK.isIE ? document.documentElement.scrollTop : self.pageYOffset;
                    var cart = SHK.getShopCartWrapper();
                    var btPos = this.getPosition( image );
                    var cartPos = this.getCenterPos( image, cart, SHK.options.tocartImage_width, SHK.options.tocartImage_height );
                    var cartPosTop = cart.position().top;
                    if( yScroll < cartPosTop ){
                        cartPosTop -= ( $(window).height()-cart.height() );
                        if( cartPosTop < yScroll ) cartPosTop = yScroll;
                    }
                    var baseDuration = 400;
                    var flyDuration = baseDuration + Math.round((Math.abs(btPos.y - cartPosTop)/baseDuration)*100);
                    var docBody = $('html');//$('html').scrollTop()>0 ? $('html') : $('body');
                    
                    docBody.animate({
                            scrollTop: cartPosTop
                        },
                        flyDuration
                    );
                    
                    image
                    .clone(false)
                    .appendTo('body')
                    .css( { top:btPos.y+'px', position:'absolute', left:btPos.x+'px', opacity:0.75, 'z-index':500 } )
                    .animate({
                        top: cartPos.y+'px',
                        left: cartPos.x+'px',
                        width: 60+'px',
                        height: 60+'px'
                    },flyDuration)
                    .fadeOut(500,function(){
                        $(this).remove();
                        SHK.fillCart(thisForm,0);
                        docBody.animate( { scrollTop:yScroll }, flyDuration );
                    });
                    
                }else{
                    this.fillCart( thisForm, 0 );
                }
                
                this.showHelper( el, '<div class="msg">' + langTxt['addedToCart'] + '</div>', true );
                $('#shk_buttons').hide();
                clearTimeout( SHK.timer );
                SHK.timer = setTimeout(function(){
                    $('#shk_prodHelper').fadeOut(500,function(){
                        $('#shk_prodHelper').remove();
                    });
                },1000);
                
            break;
            
            //&flyToCart=`nofly`
            case 'nofly':
                
                if( typeof( SHKtoCartCallback)=='function'){
                    if(!SHKtoCartCallback(thisForm)) return false;
                }
                this.fillCart( thisForm, 0 );
                
                this.showHelper( el, '<div class="msg">' + langTxt['addedToCart'] + '</div>', true );
                $('#shk_buttons').hide();
                clearTimeout( SHK.timer );
                SHK.timer = setTimeout(function(){
                    $('#shk_prodHelper').fadeOut(500,function(){
                        $('#shk_prodHelper').remove();
                    });
                },1000);
                
            break;
            default:
                this.fillCart( thisForm, 0 );
            break;
        }
    },
    
    /**
     * Индикация изменения параметра товара
     */
    additOpt: function(elem){
        
        if ( typeof elem == 'undefined' ) {
            
            $( SHK.options.prodCont ).each( function(){
                if( $( SHK.options.paramsSelector, this ).length > 0 ){
                    SHK.additOpt( $( SHK.options.paramsSelector, this).first() );
                }
            });
            
            return;
            
        }else{
            
            var parent = $(elem).closest('form');
            
        }
        
        var additPriceSum = 0;
        var multiplication = [];
        var params_elems = $( SHK.options.paramsSelector, parent );
        var thisName = $(elem).attr('name');
        var thisNameArr = thisName.split('__');
        var productId = thisNameArr.length > 1 && thisNameArr[1] ? thisNameArr[1] : '';
        
        params_elems.each(function(i){
            
            var value = $(this).val();
            var valArr = value.split('__');
            if ( valArr.length >= 2 ) {
                
                var price = valArr[1]!='' && !isNaN(valArr[1]) ? parseFloat(valArr[1]) : 0;
                if(valArr[1] != '' && isNaN(valArr[1]) && valArr[1].indexOf('*') == 0){
                    multiplication[multiplication.length] = parseFloat(valArr[1].replace('*',''));
                }
                additPriceSum += price;
                
            }
            
        });
        
        $('#add_'+productId).remove();
        
        if( additPriceSum && !isNaN(additPriceSum) && !SHK.options.changePrice ){
            
            additPriceSum = this.round(additPriceSum, 2);
            $( '.shk-price:first', parent ).after('<sup id="add_'+productId+'" class="price-add">' + ( additPriceSum > 0 ? '+' : '' ) + additPriceSum + '</sup>');
            
        }else if( !isNaN(additPriceSum) && SHK.options.changePrice != false ){

            var priceTxt = $('.shk-price:first',parent);
            var curPrice = $(priceTxt).is(":has('span')")
                ? $('span', priceTxt).text()
                : $(priceTxt).text();
            var formated = curPrice.indexOf(' ') > 0;
            var newPrice;

            if(SHK.options.changePrice=='replace'){
                newPrice = additPriceSum > 0
                    ? additPriceSum
                    : parseFloat(curPrice.replace(/[^\d\.]/g,''));
            }else{
                newPrice = parseFloat(curPrice.replace(/[^\d\.]/g,'')) + additPriceSum;
                for(var i=0; i < multiplication.length; i++){
                    newPrice = newPrice * multiplication[i];
                }
            }
            newPrice = this.round(newPrice,2);
            if(formated){
                newPrice = this.numFormat( newPrice );
            }
            $(priceTxt).empty().append('<span style="display:none;">' + curPrice + '</span>' + newPrice);
            
        }
        
    },
    
    /**
     * Добавление товаров в корзину по массиву ID
     */
    toCartFromArray: function( ids_arr, count_arr, shk_catalog, refresh ){
        
        if(typeof(ids_arr)!='object') return false;
        if(typeof(count_arr)=='undefined') count_arr = [];
        if(typeof(shk_catalog)=='undefined') shk_catalog = 1;
        if(typeof(refresh)=='undefined') refresh = true;
        if( typeof( SHKfillCartCallback)=='function') SHKfillCartCallback();
        
        var params = { shk_action: 'add_from_array', ids: ids_arr.join(','), count: count_arr.join(','), "shk-catalog": shk_catalog };
        this.ajaxRequest( params, refresh );
        
        return true;
    },
    
    /**
     * Очищение корзины
     */
    emptyCart: function(refresh){
        
        if(typeof(refresh)=='undefined') refresh = true;
        if( typeof( SHKemptyCartCallback)=='function') SHKemptyCartCallback();
        this.ajaxRequest( { shk_action: 'empty' }, refresh );
        
    },
    
    /**
     * Обновление корзины
     */
    refreshCart: function( is_first ){
        
        if ( typeof is_first == 'undefined' ) SHK.options.is_first = true;
        
        var params = { shk_action: 'refresh_cart' };
        this.ajaxRequest( params );
        
    },
    
    /**
     * Применение способа доставки для обновления общей цены в корзине
     */
    selectDelivery: function( value ){
        
        var params = { shk_action: 'update_delivery', shk_delivery: value };
        SHK.options.is_first = true;
        this.ajaxRequest( params );
        
    },
    
    /**
     * Анимация изменения размера корзины
     */
    animCartHeight: function( curH, newH, shopCart ){
        
        if ( typeof shopCart == 'undefined' ) { shopCart = SHK.getShopCartWrapper(); }
        
        shopCart
        .css({'height':curH+'px','overflow':'hidden'})
        .animate({height:newH+'px'},500,function(){
            $(this).css({'overflow':'visible','height':'auto'});
        });
    }
    
};

jQuery.fn.serializeObject = function() {
    var arrayData, objectData;
    arrayData = this.serializeArray();
    objectData = {};
    $.each(arrayData, function() {
        var value;
        if (this.value != null) {
            value = this.value;
        } else {
            value = '';
        }
        if (objectData[this.name] != null) {
            if (!objectData[this.name].push) {
                objectData[this.name] = [objectData[this.name]];
            }
            objectData[this.name].push(value);
        } else {
            objectData[this.name] = value;
        }
    });
    return objectData;
};
