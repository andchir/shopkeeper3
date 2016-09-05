
/*

tagManager JS loader

*/

;(function () {

function loadCss(url) {
    var link = document.createElement("link");
    link.type = "text/css";
    link.rel = "stylesheet";
    link.href = url;
    document.getElementsByTagName("head")[0].appendChild(link);
}

/*
if ( typeof tmFiltersOptions == 'undefined' ) {
    var tmFiltersOptions = { type: 'filters' };
}
*/

//require configure
var conf = {
    //urlArgs: "bust=" + (new Date()).getTime(),
    baseUrl: "/assets/components/tag_manager2/js/",
    cssBaseUrl: "/assets/components/tag_manager2/css/web/",
    paths: {
        "jquery-ui": "web/jquery-ui-1.10.3.custom.min",
        "history": "web/jquery.history",
        "tm-filters": "web/" + tmFiltersOptions.type
    },
    shim: {
        "jquery-ui": ["jquery"],
        "history": ["jquery"],
        "tm-filters": ( tmFiltersOptions.type == 'filters' ? ["jquery","jquery-ui","history"] : ["jquery","jquery-ui"] )
    }
};

//detect jQuery
if (!window.jQuery) {
    conf.paths.jquery = "jquery-1.11.0.min";
}else{
    define("jquery", [], function() { return window.jQuery; });
}

//requirejs
requirejs( conf, [ "jquery" ],
    function( jQuery ) {
        
        requirejs( conf, [ "tm-filters" ],
            function( ) {
                
                tmFilters.init();
                
            }
        );
        
    }
);

})();
