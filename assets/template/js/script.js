
var cmpOnToCompareLinkMinimum = function(){
    siteLib.alert('В избранном пусто.', 'danger');
}

var cmpOnToCompareAdded = function(){
    siteLib.alert('Товар добавлен в избранное.');
};

var cmpOnToCompareRemoved = function(){
    siteLib.alert('Товар убран из избранного.');
};

var SHKbeforeInitCallback = function(){
    SHK.options.buttons_class = 'btn btn-info btn-sm';
};

/**
 * Site library
 *
 */
var siteLib = (function( $ ){

    this.init = function () {

        jQuery('body')
            .tooltip({
                selector: '[data-toggle="tooltip"]',
                container: 'body',
                trigger: 'hover',
                placement: function(){
                    return this.$element.data('placement')
                        ? this.$element.data('placement')
                        : 'bottom';
                }
            });

        jQuery('.slick-slider-one')
            .slick({
                dots: true,
                arrows: true,
                infinite: false,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1
            });

        $('.slick-slider-three')
            .slick({
                slide: '.product-list-item-image',
                dots: true,
                infinite: false,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 3,
                centerMode: false,
                responsive: [
                    {
                        breakpoint: 700,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

        var viewer;
        if( $('.images-zoom').length > 0 ){
            var $viewerContainer = $('<div/>', {
                id: 'viewerContainer'
            }).appendTo(document.body);
            viewer = new Viewer(document.querySelector('.images-zoom'), {
                url: 'data-original',
                targetElement: $viewerContainer.get(0),
                navbar: false
            });
        }

    };
    
    this.alert = function( msg, type, time ){
        
        type = type || 'success';
        time = time || 3000;
        var alertClass = 'alert-' + type;
        $('.alert-fixed').remove();
        
        $('<div/>',{
            'class': 'alert alert-fixed ' + alertClass + ' alert-dismissable',
            'text': msg,
            on: {
                mouseover: function(){
                    clearTimeout( window.alertTimer );
                }
            }
        })
            .css({
                position: 'fixed',
                zIndex: 999,
                bottom: 20,
                left: 20,
                opacity: 0.9
            })
            .append($('<button/>', {
                    'type': 'button',
                    'class': 'close',
                    'html': '&times;',
                    on: {
                        click: function(e){
                            e.preventDefault();
                            clearTimeout( window.alertTimer );
                            $(this).closest('.alert').remove();
                        }
                    }
                }
            ))
            .appendTo('body');
        
        clearTimeout( window.alertTimer );
        window.alertTimer = setTimeout(function(){
            $('.alert-fixed').remove();
        }, time);
        
    };
    
    return this;
    
}).call( {}, jQuery );

jQuery(document).ready(function(){
    siteLib.init();
});
