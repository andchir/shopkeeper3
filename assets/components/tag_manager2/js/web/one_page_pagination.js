
/**
 * OnePagePagination for TagManager 2.x
 * @author: Andchir <andchir@gmail.com>
 * @version: 1.1
 */
var onePagePagination = {
    options: {
        currentPage: 1,
        onPageLimit: 1,
        totalPages: 1,
        totalItems: 1,
        buttonSelector: '#button-show-more',
        buttonDisabledClass: 'disabled'
    },
    init: function( options ){

        $.extend( this.options, options );

        $( onePagePagination.options.buttonSelector ).on('click', function(e){
            e.preventDefault();

            if( $(this).hasClass( onePagePagination.options.buttonDisabledClass ) ){
                return;
            }
            onePagePagination.options.currentPage++;
            tmFilters.switchPage( onePagePagination.options.currentPage );
        });

        if( onePagePagination.options.currentPage >= onePagePagination.options.totalPages ){
            $( onePagePagination.options.buttonSelector ).addClass( onePagePagination.options.buttonDisabledClass );
        }
    }
};
var tm_onFilterAfter = function( total, totalPages, onPageLimit ){

    onePagePagination.options.totalItems = total;
    onePagePagination.options.totalPages = totalPages;
    onePagePagination.options.onPageLimit = onPageLimit;

    if( onePagePagination.options.currentPage >= onePagePagination.options.totalPages ){
        $( onePagePagination.options.buttonSelector ).addClass( onePagePagination.options.buttonDisabledClass );
    }

    var currentTotal = Math.min( onePagePagination.options.totalItems, onePagePagination.options.onPageLimit * onePagePagination.options.currentPage );

    $( onePagePagination.options.buttonSelector ).find('span')
        .text( currentTotal + ' / ' + onePagePagination.options.totalItems );

};
var tm_onFilterBefore = function ( state_data ) {

    var pageNum = 1,
        onPageLimit = onePagePagination.options.onPageLimit;

    state_data.forEach(function(item, index){
        if( item.name == 'page' ){
            pageNum = parseInt( item.value );
        }
        if( item.name == 'limit' ){
            onPageLimit = parseInt( item.value );
        }
    });

    onePagePagination.options.currentPage = pageNum;
    onePagePagination.options.onPageLimit = onPageLimit;

    if( onePagePagination.options.currentPage == 1 ){

        $('#products')
            .prevAll('.products-container')
            .remove();

    } else {

        var $items_container = $('#products');
        var $items_container_new = $('<div/>', { 'class': 'row products-container', 'id': 'products' })
            .append( $('<div/>', { 'class': 'clearfix' }) );

        $items_container
            .removeAttr('id')
            .addClass('products-container')
            .css({ opacity: 1 })
            .after( $items_container_new );

    }
    if( onePagePagination.options.currentPage < onePagePagination.options.totalPages ){
        $( onePagePagination.options.buttonSelector ).removeClass( onePagePagination.options.buttonDisabledClass );
    }
};
var tm_onSwitchViewAfter = function () {
    if( onePagePagination.options.currentPage != 1 ){
        tmFilters.switchPage(1);
    } else {
        tmFilters.ajaxRequest();
    }
};
