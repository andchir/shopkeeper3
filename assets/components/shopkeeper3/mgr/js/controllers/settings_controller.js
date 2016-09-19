
/*

settingsController

*/

app.controller('settingsController', function( $scope, $rootScope, $http, $templateCache, $uibModal, $filter, commonService, usSpinnerService ) {
    
    $scope.main_menu = app.conf.main_menu;
    $scope.menu_current = 3;
    $rootScope.loading = false;
    $scope.data = {};
    
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
     * getSettings
     *
     */
    var getSettings = function(){
        
        var params = {
            HTTP_MODAUTH: shk_config.auth_token,
            action: 'mgr/getSettings'
        };
        
        $scope.startSpin();
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
            
            if ( !!response && angular.isObject(response.object) ) {
                
                $scope.data = angular.copy( response.object );
                
            }
            
            $scope.stopSpin();
            
        });
        
    };
    
    /**
     * save
     *
     */
    $scope.save = function(){
        
        var params = {
            HTTP_MODAUTH: shk_config.auth_token,
            action: 'mgr/saveSettings',
            data: angular.copy($scope.data)
        };
        
        $scope.startSpin();
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
            
            if ( response && response.success ) {
                
                getSettings();
                
            }else{
                $scope.stopSpin();
            }
            
            if ( !!response.message ) {
                $scope.alert( ( !response.success ? shk_config.lang['shk3.error'] : shk_config.lang['shk3.message'] ), response.message );
            }
            
        })
        .error(function(response){
            $scope.stopSpin();
            $scope.alert( 'Сообщение', response.code == 401 ? 'Доступ запрещен.' : 'Error' );
        });
        
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
     * removeRow
     *
     */
    $scope.removeRow = function( index, d_name ){
        
        $scope.data[d_name].splice( index, 1 );
        
    };
    
    /**
     * addRow
     *
     */
    $scope.addRow = function( d_name ){
        
        if ( typeof $scope.data[d_name] == 'undefined' ) {
            $scope.data[d_name] = [];
        }
        $scope.data[d_name].push({});
        
    };
    
    $scope.layoutInit();
    getSettings();
    
});


app
.directive('ngInitial', function($parse) {
    return {
        restrict: "A",
        controller: ['$scope', '$element', '$attrs', '$parse', function($scope, $element, $attrs, $parse) {
            var name = $attrs.name;
            var initialValue = $scope.item[name] || $attrs.value || $element.val();
            initialValue = parseInt(initialValue);
            $parse($attrs.ngModel).assign($scope, initialValue);
        }]
    }
});


