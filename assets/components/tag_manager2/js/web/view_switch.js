
/*

Переключение шаблонов вывода

<span id="viewSwitch">
    Показывать:
    &nbsp;
    <a rel="nofollow" href="#" class="active">картинками</a>
    &nbsp;
    <a rel="nofollow" href="#">списком</a>
</span>

*/

(function() {

'use strict';

$(document).ready(function(){
    
    var tm_view = 1;
    
    //get cookie
    var cookie_arr = document.cookie.split(';');
    for(var i=0;i < cookie_arr.length;i++){
        var c = cookie_arr[i];
        var name = "tm_view=";
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(name) == 0){
            tm_view = c.substring(name.length,c.length);
        }
    }
    
    $('a','#viewSwitch')
    .removeClass('active')
    .each(function(i){
        
        $(this)
            .data({'view':i+1})
            .on('click',function(){

                if (!$(this).is('.active')) {

                    var view = $(this).data('view');

                    var date = new Date();
                    date.setTime(date.getTime() + (7*24*60*60*1000));//7 days
                    var expires = "; expires=" + date.toGMTString();

                    document.cookie = "tm_view="+view+expires+"; path=/";//set cookie

                    //reload view
                    if( typeof tmFilters != 'undefined' && !!tmFilters.ajaxRequest ){

                        $('a','#viewSwitch')
                        .removeClass('active')
                        .eq(view-1)
                        .addClass('active');

                        if ( typeof tm_onSwitchViewAfter == 'function' ) {
                            tm_onSwitchViewAfter();
                        } else {
                            tmFilters.ajaxRequest();
                        }

                    }else{
                        window.location.reload();
                    }

                }

                return false;

            });
        
        if (tm_view == i+1) {
            $(this).addClass('active');
        }
        
    });
    
});


}());
