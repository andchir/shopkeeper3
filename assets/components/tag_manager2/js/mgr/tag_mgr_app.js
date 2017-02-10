
// Override $http service's default transformRequest
var ng_transformRequest = [function(data)
{
    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */ 
    var param = function(obj)
    {
        var query = '';
        var name, value, fullSubName, subName, subValue, innerObj, i;
        
        for(name in obj)
        {
            value = obj[name];
            
            if(value instanceof Array)
            {
                for(i=0; i<value.length; ++i)
                {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value instanceof Object)
            {
                for(subName in value)
                {
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if(value !== undefined && value !== null)
            {
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }
        }
        
        return query.length ? query.substr(0, query.length - 1) : query;
    };
    
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
}];

/* app */
var app = angular.module('tagManagerApp', ['ui.bootstrap','angularBootstrapNavTree','ui.sortable'], function($httpProvider, $locationProvider, $tooltipProvider){
    
    $locationProvider.html5Mode(false).hashPrefix('');
    
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.transformRequest = ng_transformRequest;
    
    $tooltipProvider.options({
        placement: 'top',
        animation: false,
        popupDelay: 500,
        appendToBody: true
    });
    
});

/* *************************************** */
/* config */
app.conf = {
    
    connector_url: tm_config.assets_url + 'components/tag_manager2/connector.php',
    new_window_url: window.location.href.indexOf(tm_config.manager_url) > - 1 ?
	tm_config.assets_url + 'components/tag_manager2/' :
	tm_config.manager_url + 'index.php?a=index&namespace=tag_manager2'
    
};

/* *************************************** */
/* filters */

app
.filter('translate', function() {
    
    return function( input ) {
	
	if ( tm_config.lang[input] ) {
	    input = tm_config.lang[input];
	}
	
	return input;
	
    };
});

/* onready */
$(document).on('click',function(e){
    
    if(!$(e.target).is('button,a,input')){
        $('#tag_toolbar').fadeOut();
    }
    
});
