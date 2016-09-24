
/*

homeController

*/

app.controller('homeController', function( $scope, $rootScope, $http, $templateCache, $uibModal, $filter, commonService, ngTableParams, usSpinnerService ) {
    
    $scope.selected_total = 0;
    
    $scope.menu_current = 1;
    $scope.settings = {};
    $rootScope.loading = false;
    $rootScope.tableParams = {};
    $scope.grid_columns = shk_config['settings']['order_fields'];
    $rootScope.statusesData = shk_config['settings']['statuses'];
    
    angular.extend( $scope, commonService );
    
    $rootScope.startSpin = function(){
        usSpinnerService.spin('spinner-1');
    };
    $rootScope.stopSpin = function(){
        setTimeout( function(){
            usSpinnerService.stop('spinner-1');
        }, 500 );
    };
    
    /* multiselectOptions */
    $scope.multiselectOptions = {
        placeholder: shk_config.lang['shk3.select_status'],
        moreselected_text: shk_config.lang['shk3.selected'],
        selectall_text: shk_config.lang['shk3.select_all']
    };
    
    /**
     * filters
     *
     */
    $rootScope.filters = {
        date: '',
        status: ''
    };
    $scope.filters_selected = 0;
    
    /**
     * tableParams
     *
     */
    $rootScope.tableParams = new ngTableParams({
            page: 1,
            count: 15,
            sorting: {
                id: 'desc'
            }
        }, {
            total: 0,
            counts: [15,25,50],
            getData: function($defer, params) {
                
                var post_data = angular.copy( params.$params );
                post_data.action = 'mgr/getOrdersList';
                post_data.HTTP_MODAUTH = shk_config.auth_token;
                
                //get filters
                post_data.filters = $scope.getFilters();
                
                $rootScope.startSpin();
                
                $http.post( app.conf.connector_url, post_data )
                .success(function (response) {
                    
                    if ( !!response && angular.isArray(response.object) ) {
                        
                        $defer.resolve(response.object);
                        if ( response.object.length == 0 ) {
                            response.total = 0;
                        }
                        
                    }
                    
                    params.total(response.total);
                    
                    $rootScope.stopSpin();
                    
                })
                .error(function(response){
                    $scope.alert( shk_config.lang['shk3.message'], response.code == 401 ? 'Forbidden.' : 'Error' );
                });
                
            }
        }
    );
    
    
    /**
     *
     *
     *
     */
    $scope.submitFilters = function(){
        
        $rootScope.tableParams.reload();
        
    };
    
    
    /**
     * gridReload
     *
     */
    $scope.gridReload = function(){
        
        $rootScope.tableParams.reload();
        
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
        
        $rootScope.startSpin();
        
        $http.post( app.conf.connector_url, params)
        .success(function (response) {
            
            if ( !!response && !!response.object ) {
                
                $scope.settings = angular.copy( response.object );
                
            }
            
            $rootScope.stopSpin();
            
        });
        
    };
    
    /**
     * changeSelection
     *
     */
    $scope.changeSelection = function(order) {
        
        var total = 0;
        
        for( var i in $rootScope.tableParams.data ){
            
            if ( !$rootScope.tableParams.data.hasOwnProperty(i) ) continue;
            
            if( $rootScope.tableParams.data[i].$selected ) total++;
            
        }
        
        $scope.selected_total = total;
        
    };
    
    /**
     * selectAll
     *
     */
    $rootScope.selectAll = function( selected ){
        
        if ( typeof selected == 'undefined' ) {
            selected = !!$rootScope.tableParams.data[0] && !!$rootScope.tableParams.data[0].$selected;
            selected = !selected;
        }
        
        for( var i in $rootScope.tableParams.data ){
            
            if ( !$rootScope.tableParams.data.hasOwnProperty(i) ) continue;
            
            $rootScope.tableParams.data[i].$selected = selected;
            
        }
        
        $scope.changeSelection();
        
    };
    
    
    /**
     * alert
     *
     */
    $rootScope.alert = function( title, message ){
        
        var modalInstance = $uibModal.open({
            templateUrl: 'modals/alert.html',
            appendTo: angular.element('.app-container').eq(0),
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
     * changeStatus
     *
     */
    $rootScope.changeStatus = function( order_id ){
        
        var status = '';
        
        if ( typeof order_id != 'undefined' ) {
            
            for( var i in $rootScope.tableParams.data ){
                
                if ( !$rootScope.tableParams.data.hasOwnProperty(i) ) continue;
                
                if( $rootScope.tableParams.data[i].id == order_id ){
                    status = angular.copy( $rootScope.tableParams.data[i].status );
                }
                
            }
            
            order_id = [ order_id ];
            
        }else{
            
            var order_id = [];
            
            for( var i in $rootScope.tableParams.data ){
                
                if ( !$rootScope.tableParams.data.hasOwnProperty(i) ) continue;
                
                if( $rootScope.tableParams.data[i].$selected ) {
                    order_id.push( $rootScope.tableParams.data[i].id );
                }
                
            }
            
        }
        
        var modalInstance = $uibModal.open({
            templateUrl: 'modals/change_status.html',
            appendTo: angular.element('.app-container').eq(0),
            controller: function ($scope, $uibModalInstance) {
                
                $scope.data = {
                    status: status,
                    order_id: order_id,
                    order_id_str: order_id.join( ', ' ),
                    message: ''
                };
                
                $scope.save = function () {
                    
                    var post_data = {
                        action: 'mgr/updateOrderStatus',
                        HTTP_MODAUTH: shk_config.auth_token,
                        status: $scope.data.status,
                        order_id: $scope.data.order_id
                    };
                    
                    $scope.data.message = '';
                    $rootScope.startSpin();
                    $scope.modal_loading = true;
                    
                    $http.post( app.conf.connector_url, post_data )
                    .success(function (response) {
                        
                        if ( !!response && response.success ) {
                            
                            $rootScope.selectAll( false );
                            $rootScope.tableParams.reload();

                            $uibModalInstance.close();
                            
                        }
                        
                        if ( !!response.message ) {
                            $scope.data.message = response.message;
                        }
                        
                        $rootScope.stopSpin();
                        $scope.modal_loading = false;
                        
                    });
                    
                };
                
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                
            },
            resolve: {}
        });
        
    };
    
    
    /**
     * confirm
     *
     *
     */
    $scope.confirm = function( action, text, header ){
        
        var modalInstance = $uibModal.open({
            templateUrl: 'modals/confirm.html',
            appendTo: angular.element('.app-container').eq(0),
            controller: function ($scope, $uibModalInstance) {
                
                $scope.ok = function () {
                    action();
                    $uibModalInstance.close();
                };
                
                $scope.cancel = function () {
                    $uibModalInstance.dismiss('cancel');
                };
                
            },
            resolve: {}
        });
        
    };
    
    
    /**
     * deleteOrders
     *
     */
    $scope.deleteOrders = function( id ){
        
        var order_ids = [];
        
        for( var i in $rootScope.tableParams.data ){
            
            if ( !$rootScope.tableParams.data.hasOwnProperty(i) ) continue;
            
            if( $rootScope.tableParams.data[i].$selected ) {
                order_ids.push( $rootScope.tableParams.data[i].id );
            }
            
        }
        
        var delete_func = function(){
            
            var post_data = {
                action: 'mgr/removeOrders',
                HTTP_MODAUTH: shk_config.auth_token,
                order_id: order_ids
            };
            
            $rootScope.startSpin();
            $scope.modal_loading = true;
            
            $http.post( app.conf.connector_url, post_data )
            .success(function (response) {
                
                if ( !!response && response.success ) {
                    
                    $rootScope.selectAll( false );
                    $rootScope.tableParams.reload();
                    
                }
                
                if ( !!response.message ) {
                    $rootScope.alert( ( !response.success ? shk_config.lang['shk3.error'] : shk_config.lang['shk3.message'] ), response.message );
                }
                
                $rootScope.stopSpin();
                $scope.modal_loading = false;
                
            });
            
        };
        
        $scope.confirm( delete_func );
        
    };
    
    
    /**
     * viewOrder
     *
     */
    $rootScope.viewOrder = function( order_id, action ){
        
        if ( typeof action == 'undefined' ) {
            action = 'view';
        }
        
        /* modalController */
        var modalController = function( $scope, $uibModalInstance ){
            
            $scope.data = {};
            $scope.data.total_items = 0;
            $scope.data.total_price = 0;
            $scope.data.order = {};
            $scope.modal_loading = false;
            $scope.settings = {
                payments: shk_config.settings.payments,
                delivery: shk_config.settings.delivery
            };
            
            var ajaxRequest = function( post_data, callback ){
                
                $rootScope.startSpin();
                $scope.data.message = '';
                $scope.modal_loading = true;
                
                $http.post( app.conf.connector_url, post_data)
                .success(function (response) {
                    
                    if ( typeof callback == 'function' ) {
                        callback( response );
                    }
                    
                    if ( !!response.message ) {
                        $scope.data.message = response.message;
                    }
                    
                    $rootScope.stopSpin();
                    $scope.modal_loading = false;
                    
                })
                .error(function (response) {
                    if( response.code == 401 ) response.message = 'Forbidden.';
                    if ( !!response.message ) {
                        $scope.data.message = response.message;
                    }
                    $rootScope.stopSpin();
                    $scope.modal_loading = false;
                });
                
            };
            
            /* getOrder */
            var getOrder = function(){
                
                var post_data = {
                    HTTP_MODAUTH: shk_config.auth_token,
                    action: 'mgr/getOrder',
                    order_id: order_id
                };
                
                var callback_func = function(response){
                    
                    if ( !!response && !!response.object ) {
                        
                        $scope.data.order = angular.copy( response.object );
                        
                    }
                    
                };
                
                ajaxRequest( post_data, callback_func );
                
            };
            
            getOrder();
            
            /* addOption */
            $scope.addOption = function( index ){
                
                if ( !angular.element.isPlainObject( $scope.data.order.purchases[index].options ) ) {
                    $scope.data.order.purchases[index].options = {};
                }
                var opt_index = Object.keys($scope.data.order.purchases[index].options).length;
                var opt_name = 'shk_option' + ( opt_index + 1 );
                
                //если такой ключ для нового параметра уже есть, обновляем все ключи
                if ( $scope.data.order.purchases[index].options[ opt_name ] ) {
                    var options = {}, ind = 0;
                    for( var key in $scope.data.order.purchases[index].options ){
                        if ( $scope.data.order.purchases[index].options.hasOwnProperty(key) ) {
                            var key_name = key.indexOf('shk_option') > -1 ? 'shk_option' + ( ind + 1 ) : key;
                            options[key_name] = $scope.data.order.purchases[index].options[key];
                        }
                        ind++;
                    }
                    $scope.data.order.purchases[index].options = options;
                }
                $scope.data.order.purchases[index].options[ opt_name ] = [ '', 0 ];
                
            };
            
            /* edit */
            $scope.edit = function() {

                $uibModalInstance.close();
                $rootScope.viewOrder( order_id, 'edit' );
                
            };
            
            /* view */
            $scope.view = function() {

                $uibModalInstance.close();
                $rootScope.viewOrder( order_id, 'view' );
                
            };
            
            /* save */
            $scope.save = function() {
                
                var post_data = {
                    HTTP_MODAUTH: shk_config.auth_token,
                    action: 'mgr/saveOrder',
                    order: angular.copy( $scope.data.order )
                };
                
                var callback_func = function( response ){
                    
                    if ( !!response && response.success ) {
                        
                        $rootScope.tableParams.reload();
                        $uibModalInstance.close();
                        //$rootScope.viewOrder( order_id, 'view' );
                        
                    }
                    
                };
                
                ajaxRequest( post_data, callback_func );
                
            };
            
            /* removeRow */
            $scope.removeRow = function( index, d_name ){
                
                $scope.data.order[d_name].splice( index, 1 );
                
            };
            
            /* addRow */
            $scope.addRow = function( d_name ){
                
                if ( !angular.isArray( $scope.data.order[d_name] ) ) {
                    $scope.data.order[d_name] = [];
                }
                $scope.data.order[d_name].push({});
                
            };
            
            /* cancel */
            $scope.cancel = function () {
                $uibModalInstance.dismiss('cancel');
            };
            
            var getTotal = function(){
                
                var total_items = 0;
                var total_price = 0;
                var delivery_price = !!$scope.data.order.delivery_price && !isNaN( $scope.data.order.delivery_price ) ? $scope.data.order.delivery_price : 0;
                
                for( var i in $scope.data.order.purchases ){
                    
                    if ( !$scope.data.order.purchases.hasOwnProperty(i) ) continue;
                    
                    var temp_count = !!$scope.data.order.purchases[i].count ? $scope.data.order.purchases[i].count : 0;
                    var temp_price = !!$scope.data.order.purchases[i].price ? $scope.data.order.purchases[i].price : 0;
                    
                    total_items += temp_count;
                    total_price += ( temp_count * temp_price );
                    
                    if( $scope.data.order.purchases[i].options && angular.isObject( $scope.data.order.purchases[i].options ) ){
                        
                        //доп параметры товара
                        for( var ii in $scope.data.order.purchases[i].options ){
                            
                            $opt_price = $scope.data.order.purchases[i].options[ii][1] && !isNaN( $scope.data.order.purchases[i].options[ii][1] ) ? parseFloat( $scope.data.order.purchases[i].options[ii][1] ) : 0;
                            
                            if( $opt_price > 0 ){
                                total_price += $opt_price * temp_count;
                            }
                            
                        }
                        
                    }
                    
                }
                
                $scope.data.total_items = total_items;
                $scope.data.total_price = total_price + delivery_price;
                
            };
            
            /* watch */
            $scope.$watch( 'data.order.purchases', getTotal, true );
            
            $scope.$watch( 'data.order.delivery_price', getTotal );
            
        };
        
        /* modalInstance */
        var modalInstance = $uibModal.open({
            templateUrl: 'modals/order_'+action+'.html',
            appendTo: angular.element('.app-container').eq(0),
            size: 'lg',
            controller: modalController,
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
    
});


/* renderFieldValue */
app
.directive('renderFieldValue', ['$compile','$rootScope',function( $compile, $rootScope ) {
    return {
	restrict: 'A',
	scope: { },
	template: '{{output}}',
	link: function (scope, element, attrs) {
            
            scope.output = !!attrs.fieldvalue ? attrs.fieldvalue : 'N/a';
            
            //renderers
            var renderers = {
                //renderer username
                username: function(input){
                    
                    var template = '<a href="?a=security/user/update&id={{userid}}" target="_blank">\
                    <span class="glyphicon glyphicon-user"></span>\
                    {{username}}\
                    </a>';
                    scope.userid = !!attrs.userid ? attrs.userid : 0;
                    scope.username = !!attrs.fieldvalue ? attrs.fieldvalue : 'N/a';
                    
                    if ( scope.userid > 0 ) {
                        element.html(template);
                        $compile(element.contents())(scope);
                    }
                    
                    
                    return true;
                    
                },
                //renderer status
                status: function(){
                    
                    var template = '<span class="status-button" style="background-color:{{status_color}};">\
                    <span class="icon glyphicon glyphicon-retweet" ng-click="changeStatus(order_id)"></span>\
                    <span class="text">{{status_label}}</span>\
                    </span>';
                    
                    scope.status_color = '';
                    scope.status_label = 'N/a';
                    scope.order_id = !!attrs.id ? attrs.id : 0;
                    scope.changeStatus = $rootScope.changeStatus;
                    var index = -1;
                    
                    for( var i in shk_config.settings.statuses ){
                        
                        if ( shk_config.settings.statuses.hasOwnProperty(i) ) {
                            
                            if ( shk_config.settings.statuses[i].id == attrs.fieldvalue ) {
                                index = i;
                                break;
                            }
                            
                        }
                        
                    }
                    
                    if ( index > -1 ) {
                        scope.status_color = shk_config.settings.statuses[index].color;
                        scope.status_label = shk_config.settings.statuses[index].label;
                    }else{
                        scope.status_color = '#fff';
                        scope.status_label = 'N/a';
                    }
                    
                    element.html(template);
                    $compile(element.contents())(scope);
                    
                    return true;
                    
                },
                //renderer price
                price: function(){
                    
                    var template = "{{ output | number: 2 }}";
                    
                    element.html(template);
                    $compile(element.contents())(scope);
                    
                }
            };
            
            if( typeof renderers[attrs.fieldname] == 'function' ){
                
                renderers[attrs.fieldname]();
                
            }
            
	}
    };
}]);

/* orderNote */
app
.directive('orderNote', ['$compile',function($compile) {
    return {
        restrict: 'A',
        scope: { },
        template: '{{output}}',
        link: function (scope, element, attrs) {

            scope.output = '';

            if( typeof attrs.note != 'undefined' && attrs.note != '' ){

                    var template = '<span class="glyphicon glyphicon-info-sign" tooltip-placement="left" uib-tooltip="'+attrs.note+'"></span>';
                    element.html(template);
                    $compile(element.contents())(scope);

            }

        }
    };
}]);


