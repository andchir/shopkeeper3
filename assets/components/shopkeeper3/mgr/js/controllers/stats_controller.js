
/*

statsController

*/

app.controller('statsController', function( $scope, $rootScope, $http, $templateCache, $uibModal, $filter, commonService, ngTableParams, usSpinnerService ) {
    
    $scope.main_menu = app.conf.main_menu;
    $scope.menu_current = 2;
    $scope.message = '';
    
    angular.extend( $scope, commonService );
    
    $scope.startSpin = function(){
        usSpinnerService.spin('spinner-1');
    };
    $scope.stopSpin = function(){
        setTimeout( function(){
            usSpinnerService.stop('spinner-1');
        }, 500 );
    };
    
    /**
     * filters
     *
     */
    $scope.daterangepickerOptions.startDate = moment().subtract(90,'days');
    $scope.daterangepickerOptions.endDate = moment();
    
    $rootScope.filters = {
        date: $scope.daterangepickerOptions.startDate.format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'),
        status: ''
    };
    $scope.filters_selected = 0;
    
    /**
     * ajaxRequest
     *
     */
    var ajaxRequest = function( post_data, callback ){
        
        $scope.startSpin();
        
        $http.post( app.conf.connector_url, post_data)
        .success(function (response) {
            
            if ( typeof callback == 'function' ) {
                callback( response );
            }
            
            if ( response.message ) {
                $rootScope.alert( ( !response.success ? shk_config.lang['shk3.error'] : shk_config.lang['shk3.message'] ), response.message );
            }
            
            $scope.stopSpin();
            
        })
        .error(function (response) {
            $rootScope.loading = false;
            if( response.code == 401 && !response.message ) response.message = 'Forbidden';
            if ( response.message ) {
                $scope.alert( shk_config.lang['shk3.error'], response.message );
            }
            $scope.loading = false;
        });
        
    };
    
    /**
     * submitFilters
     *
     */
    $scope.submitFilters = function(){
        
        getData();
        
    };
    
    /**
     * getData
     *
     */
    var getData = function(){
        
        var post_data = {
            action: 'mgr/getStat',
            HTTP_MODAUTH: shk_config.auth_token,
        };
        
        //get filters
        post_data.filters = $scope.getFilters();
        
        var callback_func = function( response ){
            
            var stat_data = response.object ? response.object : {};
            
            var date = new Date();
            
            var chart = c3.generate({
                bindto: '#chart',
                data: stat_data,
                axis: {
                    y: {
                        label: {
                            text: shk_config.lang['shk3.orders_count'],
                            position: 'outer-middle'
                        }
                    },
                    x: {
                        label: {
                            text: shk_config.lang['shk3.months'],
                            position: 'outer-middle'
                        },
                        type : 'timeseries',
                        tick: {
                            format: function (x) {
                                return shk_config.lang[ 'shk3.month' + ( x.getMonth() + 1 ) ] + ( date.getFullYear() != x.getFullYear() ? ' ' + x.getFullYear() : '' );
                            }
                        }
                    }
                },
                zoom: {
                    enabled: false
                }
            });
            
        };
        
        ajaxRequest( post_data, callback_func );
        
    };
    
    /**
     * alert
     *
     */
    $rootScope.alert = function( title, message ){
        
        var modalInstance = $uibModal.open({
            templateUrl: 'modals/alert.html',
            controller: function ($scope, $uibModalInstance) {
                
                $scope.title = title;
                $scope.message = message;
                
                $scope.cancel = function () {
                    $uibModalInstance.close();
                };
                
            },
            resolve: {}
        });
        
    };
    
    /**
     * watches
     *
     */
    $scope.$watch('filters',function(newValue,oldValue){
        
        var count = 0;
        for( var k in $scope.filters ){
            
            if ( !$scope.filters.hasOwnProperty(k) ) continue;
            
            if ( ( angular.isArray($scope.filters[k]) && $scope.filters[k].length > 0 ) || $scope.filters[k] != '' ) {
                count++;
            }
            
        }
        
        $scope.filters_selected = count;
        
    },true);
    
    $scope.layoutInit();
    getData();
    
});

