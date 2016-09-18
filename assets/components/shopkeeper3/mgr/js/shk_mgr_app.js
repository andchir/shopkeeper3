
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

/* *************************************** */
/* app */

var app = angular.module('shkManagerApp', ['ui.bootstrap','app.tpls','ngTable','ngSanitize','ngTableExport','dateRangePicker','minicolors','angularSpinner'], function($httpProvider, $locationProvider, $uibTooltipProvider, minicolorsProvider, usSpinnerConfigProvider){
    
    $locationProvider.html5Mode(false).hashPrefix('');

    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    $httpProvider.defaults.transformRequest = ng_transformRequest;

    //minicolors
    angular.extend(minicolorsProvider.defaults, {
        control: 'hue',
        position: 'bottom right'
    });

    //spinner
    usSpinnerConfigProvider
    .setDefaults( { radius:30, width:8, length: 16, color: '#000' } );
    
});

/* *************************************** */
/* config */
app.conf = {

    connector_url: shk_config.assets_url + 'components/shopkeeper3/connector.php',
    main_menu: [
        {
            id: 1,
            name: 'shk3.orders',
            icon: 'glyphicon-shopping-cart',
            link: 'index.php?a=index&namespace=shopkeeper3'
        },{
            id: 2,
            name: 'shk3.stats',
            icon: 'glyphicon-stats',
            link: 'index.php?a=stats&namespace=shopkeeper3'
        },{
            id: 3,
            name: 'shk3.settings',
            icon: 'glyphicon-cog',
            link: 'index.php?a=settings&namespace=shopkeeper3'
        }
    ],
    new_window_url: window.location.href.indexOf(shk_config.manager_url) > - 1 ?
	shk_config.assets_url + 'components/shopkeeper3/' :
	shk_config.manager_url + 'index.php?a=index&namespace=shopkeeper3'
};

/* *************************************** */
/* commonService */
app
.factory('commonService', ['$rootScope','$uibModal','$http','$filter','$location',function($rootScope,$uibModal,$http,$filter,$location) {

    return {

	main_menu: app.conf.main_menu,
	new_window_url: app.conf.new_window_url,
	new_window_icon: app.conf.new_window_url.indexOf('namespace=') > -1 ? 'glyphicon-log-in' : 'glyphicon-new-window',

	onWindowResize: function(){

	    var wrapper = angular.element('.amod-wrapper');

	    wrapper.css( { height: ( angular.element(window).height() - angular.element('#modx-header').height() ) + 'px' } );

	},

	layoutInit: function(){

	    var _self = this;

	    _self.onWindowResize();
	    angular.element(window).bind( 'resize', _self.onWindowResize );

	},

	daterangepickerOptions: {
	    apply_txt: shk_config.lang['shk3.apply'],
	    reset_txt: shk_config.lang['shk3.reset'],
	    from_txt: shk_config.lang['shk3.from'],
	    to_txt: shk_config.lang['shk3.to'],
	    othet_period_txt: shk_config.lang['shk3.othet_period'],
	    today_txt: shk_config.lang['shk3.totay'],
	    yesterday_txt: shk_config.lang['shk3.yesterday'],
	    last_7_days_txt: shk_config.lang['shk3.last_7_days'],
	    last_30_days_txt: shk_config.lang['shk3.last_30_days'],
	    this_month_txt: shk_config.lang['shk3.this_month']
	},

	getFilters: function(){

	    var output = {};

	    for( var k in $rootScope.filters ){

		if ( !$rootScope.filters.hasOwnProperty(k) ) continue;

		if ( ( angular.isArray($rootScope.filters[k]) && $rootScope.filters[k].length > 0 ) || $rootScope.filters[k] != '' ) {

		    $f_value = angular.copy( $rootScope.filters[k] );

		    if ( k == 'date' && $f_value.indexOf(' - ') > -1 ) {
			$f_value = $f_value.split(' - ');
		    }

		    output[k] = angular.copy( $f_value );

		}

	    }

	    return output;

	}

    }

}]);

/* *************************************** */
/* filters */

app
.filter('renderFieldValue', function() {

    return function( input ) {

	//console.log( app );

	return input;

    };
})

.filter('translate', function() {

    return function( input ) {

	if ( !!shk_config.lang[input] ) {
	    input = shk_config.lang[input];
	}

	return input;

    };
});

/* *************************************** */
/* directives */
app
.directive('loadingContainer', function () {
    return {
        restrict: 'A',
        scope: false,
        link: function(scope, element, attrs) {
            var loadingLayer = angular.element('<div class="loading"></div>');
            element.append(loadingLayer);
            element.addClass('loading-container');
            scope.$watch(attrs.loadingContainer, function(value) {
                loadingLayer.toggleClass('ng-hide', !value);
            });
        }
    };
})

.directive('multiselectDropdown', [function() {
    return function(scope, element, attributes) {

	var optionsData = !!element.data('options') ? scope[element.data('options')] : element.data();

        element.multiselect({
            buttonClass : 'btn btn-default btn-small',
            buttonWidth : '220px',
            buttonContainer : '<div class="btn-group" />',
            maxHeight : 200,
            enableFiltering : false,
	    filterPlaceholder: 'Search',
	    includeSelectAllOption: true,
            selectAllText: ( !!optionsData['selectall_text'] ? optionsData['selectall_text'] : 'Select All' ),
            //enableCaseInsensitiveFiltering: true,
            buttonText : function(options) {
                if (options.length == 0) {
                    return optionsData['placeholder'] + ' <b class="caret"></b>';
                } else if (options.length > 1) {
                    return options.eq(0).text()
                    + ' + ' + (options.length - 1)
                    + ' ' + ( !!optionsData['moreselected_text'] ? optionsData['moreselected_text'] : 'more selected' ) + ' <b class="caret"></b>';
                } else {
                    return options.eq(0).text()
                    + ' <b class="caret"></b>';
                }
            },

            onChange: function (optionElement, checked) {

                if(!!optionElement){

                    optionElement.removeAttr('selected');
                    if (checked) {
                        optionElement.prop('selected', 'selected');
                    }
                    element.change();

                }

            }

        });

        scope.$watch(function () {
            return element[0].length;
        }, function () {
            element.multiselect('rebuild');
        });

        scope.$watch(attributes.ngModel, function () {
            element.multiselect('refresh');
        });
    }
}]);

