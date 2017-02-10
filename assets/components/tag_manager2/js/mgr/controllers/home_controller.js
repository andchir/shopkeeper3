
/*

homeController

*/

app.controller('homeController', function($scope, $rootScope, $http, $templateCache, $modal) {
    
    $scope.data = {};
    $rootScope.loading = true;
    $rootScope.tree_loading = false;
    $rootScope.showloader = true;
    $scope.saving = false;
    $scope.tree_data = [];
    $scope.filters_all = [];
    $scope.filters = [];
    $scope.alerts = [];
    $scope.filters_length = 0;
    $scope.active_branch = {
        id: tm_config['tag_mgr2.catalog_id'],
        selected: true,
        active: true
    };
    
    $scope.new_window_url = app.conf.new_window_url;
    $scope.new_window_icon = app.conf.new_window_url.indexOf('namespace=') > -1 ? 'glyphicon-log-in' : 'glyphicon-new-window';
    
    $scope.tagtoolbar = {
        outerIndex: 0,
        innerIndex: 0,
        fieldName: '',
        oldValue: '',
        edit: false,
        style: {
            left: '0',
            top: '0',
            display: 'none'
        }
    };
    
    /**
     * onWindowResize
     *
     */
    var onWindowResize = function(){

        var wrapper = angular.element('.tm-wrapper');

        wrapper.css( { height: ( angular.element(window).height() - angular.element('#modx-header').height() ) + 'px' } );

    };
    
    /**
     * layoutinit
     *
     */
    var layoutInit = function(){

        onWindowResize();
        angular.element(window).bind( 'resize', onWindowResize );

    };
    
    /**
     * popupAlert
     * 
     */
    var popupAlert = function(title, message){
        
        var modalInstance = $modal.open({
            templateUrl: 'modal_alert.html',
            controller: function ($scope, $modalInstance, title, message) {
                
                $scope.title = title;
                $scope.message = message;
                
                $scope.ok = function () {
                    $modalInstance.close();
                };
                
                $scope.cancel = function () {
                    $modalInstance.dismiss('cancel');
                };
                
            },
            resolve: {
                message: function () {
                    return message;
                },
                title: function () {
                    return title;
                }
            }
        });
        
    };
    
    /**
     * confirmAction
     *
     */
    var confirmAction = function(action){

        var modalOpts = {
            backdrop: true,
            keyboard: true,
            backdropClick: true,
            templateUrl: 'modal_confirm.html',
            controller: function($scope, $modalInstance){

            $scope.ok = function () {
                action();
                $modalInstance.close();
            };

            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };

            }
        };

        var modalInstance = $modal.open(modalOpts);

    };
    
    /* ####################################################
    * Tree
    */
    /**
     * loadDataTree
     *
     */
    var loadDataTree = function(reload){
        
        if (typeof reload == 'undefined') reload = false;
        
        var params = {
            'HTTP_MODAUTH': tm_config.auth_token,
            'action': 'mgr/getParentsList',
            'current_id': $scope.active_branch.id
        };
        
        $rootScope.tree_loading = true;
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
            
            if (!!response && angular.isArray(response.object)) {
                
                $scope.tree_data = response.object[1];
                
                if (!reload) {
                    $scope.active_branch.id = $scope.tree_data[0].id;
                    $scope.active_branch.active = $scope.tree_data[0].active;
                }
                
            }else{
                
                if(!!response.message) popupAlert('Ошибка', response.message);
                
            }
            
            $rootScope.loading = false;
            $rootScope.tree_loading = false;
            $rootScope.showloader = false;
            
        });
        
    };
    
    /**
     * treeBranchActive
     *
     *
     */
    var treeBranchActive = function(data, id, active){
        
        if (!data || !angular.isArray(data)) return undefined;
        
        for(var i in data){
            
            if( !data.hasOwnProperty(i) ){
                continue;
            }
            
            if (data[i].id == id) {
                
                data[i].active = active;
                
            }else{
                
                if (!!data[i].children && angular.isArray(data[i].children)) {
                    
                    treeBranchActive(data[i].children, id, active);
                    
                }
                
            }
            
        }
        
        return true;
        
    };
    
    /**
     * my_tree_handler
     *
     */
    $scope.my_tree_handler = function(branch) {
        
        //console.log(branch, $scope.tree_data);
        
        if (branch.id != $scope.active_branch.id) {
            $scope.active_branch = angular.extend({}, branch);
            
            if($scope.active_branch.id != $scope.tree_data[0].id){
                $scope.tree_data[0].selected = false;
            }
            
            loadDataFilters();
            
        }
        
    };
    
    
    /**
     * expandLevel
     *
     */
    var expandLevel = function(data, expand){
        
        if (!data) return undefined;
        
        for(var i in data){
            
            if( !data.hasOwnProperty(i) ){
                continue;
            }
            
            data[i].expanded = expand;
            if (!!data[i].children && angular.isArray(data[i].children)) {
                expandLevel(data[i].children, expand);
            }
        }
        
    };
    
    
    /**
     * expandAll
     *
     */
    $scope.expandAll = function(){
        
        var expand = !$scope.tree_data[0].expanded;
        
        expandLevel($scope.tree_data, expand);
        
    };
    
    
    /**
     * refreshTree
     *
     */
    $scope.refreshTree = function(){
        
        loadDataTree(true);
        
    };
    
    
    /* ####################################################
    * Filters
    */
    /**
     * loadDataFilters
     *
     */
    var loadDataFilters = function(){
        
        var params = {
            'HTTP_MODAUTH':	tm_config.auth_token,
            'action': 'mgr/getTags',
            'parent_id': $scope.active_branch.id
        };
        
        $scope.filters_all = [];
        $scope.filters = [];
        $rootScope.loading = true;
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
	    
            if (!!response && response.object) {
                
                if(angular.isArray(response.object[0])) $scope.filters_all = response.object[0];
                if(angular.isArray(response.object[1])) $scope.filters = response.object[1];
		
                /*
		var msg = 'Пожалуйста, выберите фильтры.';
		if ($scope.filters.length == 0 && $.inArray(msg,$scope.alerts) == -1) {
		    $scope.alerts.push(msg);
		}
		*/
		
                $rootScope.loading = false;
                $rootScope.showloader = false;
                
            }
            
        });
        
    };
    
    
    /**
     * sortFiltersSubmit
     * 
     * сортировка блоков фильтров
     */
    var sortFiltersSubmit = function(tvname){
        
        if (typeof tvname == 'undefined') tvname = '';
	
        var filters_all_tmp = [[],[]];
        var flt_names_tmp = [[],[]];
        for(var i in $scope.filters_all){
            
            if( !$scope.filters_all.hasOwnProperty(i) ){
                continue;
            }
            
            if($scope.filters_all[i].active){
                filters_all_tmp[0].push( $scope.filters_all[i] );
                flt_names_tmp[0].push( $scope.filters_all[i].tvname );
            }else{
                filters_all_tmp[1].push( $scope.filters_all[i] );
                flt_names_tmp[1].push( $scope.filters_all[i].tvname );
            }
            
        }
        
        var flt_names = flt_names_tmp[0].concat( flt_names_tmp[1] );
        $scope.filters_all = filters_all_tmp[0].concat( filters_all_tmp[1] );
        
        var filters_tmp = angular.extend([], $scope.filters);
        
        filters_tmp.sort(function(a,b){ return $.inArray(a.tvname,flt_names) - $.inArray(b.tvname,flt_names); } /* return a-b*/);
        
        $scope.filters = filters_tmp;
        
        return $.inArray(tvname, flt_names);
        
    };
    
    
    $scope.sortableFilterOptions = {
        helper : 'clone',
        placeholder: "drad-pl2",
        //items: "li:not(.label-default)",
        items: ".label-warning",
        cancel: '.label-default',
        stop: function(e, ui) {
            var localScope = angular.element(ui.item).scope();
            if (localScope.item.active) {
                sortFiltersSubmit();
            }
        }
    };
    
    
    /**
     * saveFilters
     *
     */
    $scope.saveFilters = function(){
        
        var filters_data = [];
        
        for(var i in $scope.filters){
            
            if( !$scope.filters.hasOwnProperty(i) ){
                continue;
            }
            
            var temp = {
                "id": $scope.filters[i].id,
                "tvname": $scope.filters[i].tvname,
                "tvcaption": $scope.filters[i].tvcaption,
                "tags": []
            };
            
            for(var ii in $scope.filters[i].tags){
                
                if( !$scope.filters[i].tags.hasOwnProperty(ii) ){
                    continue;
                }
                
                temp.tags.push({ "value": $scope.filters[i].tags[ii].value, "active": $scope.filters[i].tags[ii].active });
                
            }
            
            filters_data.push(temp);
            
        }
        
        if (filters_data.length > 0) {
            
            $scope.saving = true;
            
            var params = {
                'HTTP_MODAUTH':	tm_config.auth_token,
                'action': 'mgr/saveFilters',
                'parent_id': $scope.active_branch.id,
                'data': angular.toJson(filters_data)
            };
            
            $http.post( app.conf.connector_url, params )
            .success(function (response) {
                
                if (!!response && typeof response.success != 'undefined' && response.success) {
                    
                    treeBranchActive($scope.tree_data, $scope.active_branch.id, true);
                    $scope.active_branch.active = true;
                    //popupAlert('Информация', 'Data saved.');
                    
                }else{
                    
                    if(!!response.message) popupAlert('Ошибка', response.message);
                    
                }
                
                $scope.saving = false;
                
            });
            
        }
        
    };
    
    
    /**
     * updateFilters
     *
     */
    $scope.updateFilters = function(){
        
        var params = {
            'HTTP_MODAUTH': tm_config.auth_token,
            'action': 'mgr/updateFilters',
            'parent_id': $scope.active_branch.id
        };
        
        $scope.saving = true;
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
            
            if (!!response && response.object) {
                
                if(angular.isArray(response.object[0])) $scope.filters_all = response.object[0];
                if(angular.isArray(response.object[1])) $scope.filters = response.object[1];
                
                $scope.saving = false;
                
            }
            
        });
        
    };
    
    
    /**
     * filtersRemoveCategory
     *
     */
    $scope.filtersRemoveCategory = function(){
        
        var remove_action = function(){
            
            var params = {
                'HTTP_MODAUTH': tm_config.auth_token,
                'action': 'mgr/removeFilters',
                'parent_id': $scope.active_branch.id
            };
            
            $http.post( app.conf.connector_url, params )
            .success(function (response) {
                
                if (!!response && typeof response.success != 'undefined' && response.success) {
                    
                    treeBranchActive($scope.tree_data, $scope.active_branch.id, false);
                    $scope.active_branch.active = false;
                    loadDataFilters();
                    
                }else{
                    
                    if(!!response.message) popupAlert('Ошибка', response.message);
                    
                }
                
            });
            
        };
        
        confirmAction( remove_action );
        
    };
    
    
    /**
     * filtersRemoveAll
     *
     */
    $scope.filtersRemoveAll = function(){
        
        var remove_action = function(){
            
            var params = {
                'HTTP_MODAUTH': tm_config.auth_token,
                'action': 'mgr/removeFilters',
                'parent_id': 'all'
            };
            
            $http.post( app.conf.connector_url, params )
            .success(function (response) {
                
                if (!!response && typeof response.success != 'undefined' && response.success) {
                    
                    window.location.reload();
                    //loadDataTree();
                    //loadDataFilters();
                    
                }else{
                    
                    if(!!response.message) popupAlert('Ошибка', response.message);
                    
                }
                
            });
            
        };
        
        confirmAction( remove_action );
        
    };
    
    
    /**
     * filterGroupAdd
     *
     */
    $scope.filterGroupAdd = function(index){
	
        if ($scope.filters_all[index].loading) {
            return false;
        }
        
        $scope.filters_all[index].active = !$scope.filters_all[index].active;
        
        if ($scope.filters_all[index].active) {
            
            var f_index = sortFiltersSubmit($scope.filters_all[index].tvname);
            
            $scope.filters_all[f_index].loading = true;
            $scope.filters.push( angular.extend({loading: true}, $scope.filters_all[f_index]) );
            
            //get filter data
            var params = {
                'HTTP_MODAUTH': tm_config.auth_token,
                'action': 'mgr/getFilterData',
                'parent_id': $scope.active_branch.id,
                'tvname': $scope.filters_all[f_index].tvname
            };
            $http.post( app.conf.connector_url, params )
            .success(function (response) {
                
                if (response && angular.isArray(response.object)) {
                    
                    var last_ind = $scope.filters.length - 1;
                    
                    $scope.filters[last_ind].tags = response.object;
                    $scope.filters[last_ind].loading = false;
                    
                }else{
                    
                    if(!!response.message) popupAlert('Ошибка', response.message);
                    
                }
                
                $scope.filters_all[f_index].loading = false;
                
            });
            
            
        }else{
            
            $scope.filters.splice(index,1);
            
            sortFiltersSubmit();
            
        }
        
        return true;
        
    };
    
    
    /**
     * closeMsg
     *
     */
    $scope.closeMsg = function(index){

        $scope.alerts.splice(index, 1);

    };
    
    
    /* ####################################################
    * Tags
    */
    $scope.tagDelete = function(id){
        
        $scope.filters[$scope.tagtoolbar.outerIndex].tags.splice($scope.tagtoolbar.innerIndex,1);
        
        angular.element('#tag_toolbar').hide();
        
    };
    
    /**
     * tagEdit
     *
     */
    $scope.tagEdit = function(){
        
        var temp = {
            name: $scope.filters[$scope.tagtoolbar.outerIndex].tvname,
            value: $scope.filters[$scope.tagtoolbar.outerIndex].tags[$scope.tagtoolbar.innerIndex].value
        };
        
        $scope.tagtoolbar.edit = !$scope.tagtoolbar.edit;
        $scope.tagtoolbar.fieldName = temp.name;
        $scope.tagtoolbar.oldValue = temp.value;
        var top = Math.round(parseFloat($scope.tagtoolbar.style.top.replace(/[^\d.]/,'')));
        $scope.tagtoolbar.style.top = ($scope.tagtoolbar.edit ? (top - 49) : (top + 49)) + 'px';
    };
    
    /**
     * tagUpdate
     *
     */
    $scope.tagUpdate = function(){
        
        //$scope.filters[$scope.tagtoolbar.outerIndex].tags[$scope.tagtoolbar.innerIndex] = angular.extend({},$scope.tagtoolbar.tag);
        
        //get filter data
        var params = {
            'HTTP_MODAUTH': tm_config.auth_token,
            'action': 'mgr/replaceTagValue',
            'parent_id': $scope.active_branch.id,
            'field_name': $scope.tagtoolbar.fieldName,
            'old_value': $scope.tagtoolbar.oldValue,
            'field_value': angular.element('#tagtoolbarValue').val()
        };
        
        $scope.saving = true;
        
        $http.post( app.conf.connector_url, params )
        .success(function (response) {
            
            $scope.saving = false;
            
        });
        
    };
    
    /**
     * showTagToolbar
     *
     */
    $scope.showTagToolbar = function( e, tvid, tag_value ){

        var outerIndex = -1, innerIndex = -1;

        //get indexes
        for ( var i in $scope.filters ) {
            if ( !$scope.filters.hasOwnProperty(i) ) continue;

            if ( $scope.filters[i].tvid == tvid ) {
            outerIndex = i;
            for( var ii in $scope.filters[i].tags ){
                if ( !$scope.filters[i].tags.hasOwnProperty(ii) ) continue;

                if($scope.filters[i].tags[ii].value == tag_value){
                innerIndex = ii;
                break;
                }
            }
            break;
            }

        }

        e.preventDefault();
        e.stopPropagation();
        
        var pos = angular.element(e.target).position();//offset();
        var scrollTop = 0;//$scope.toppanel_fixed ? angular.element('.tm-container-c').scrollTop() : angular.element('.tm-container-b').scrollTop();
        pos.top -= scrollTop;
        var container_padding_top = parseInt( angular.element( '#modx-content .tm-container-c' ).css('padding-top').replace('px','') );
        var container_padding_left = parseInt( angular.element( '#modx-content .tm-container-c' ).css('padding-left').replace('px','') );
        if( container_padding_top ){
            pos.top += container_padding_top;
        }
        if( container_padding_left ){
            pos.left += container_padding_left;
        }

        if ($scope.tagtoolbar.outerIndex == outerIndex && $scope.tagtoolbar.innerIndex == innerIndex && $scope.tagtoolbar.style.display != 'none') {
            $scope.tagtoolbar.style.display = 'none';
        }else{
            $scope.tagtoolbar = {
                //tag: angular.extend({}, $scope.filters[outerIndex].tags[innerIndex]),
                outerIndex: outerIndex,
                innerIndex: innerIndex,
                edit: false,
                style: {
                    left: pos.left+'px',
                    top: pos.top+'px',
                    display: 'block'
                }
            };
        }
        
        angular.element('#tag_toolbar button').blur();
        
    };
    
    $scope.sortableOptions = {
        //axis: 'x',
        placeholder: "drad-pl",
        //items: "li:not(.label-default)",
        start: function(e, ui) {
            angular.element('#tag_toolbar').hide();
        },
        stop: function(e, ui) {
            var localScope = angular.element(ui.item).scope();
            //console.log(localScope);
        }
    };
    
    /**
     * watches
     *
     */
    
    $scope.$watch('filters',function(newValue, oldValue){
        $scope.filters_length = newValue.length;
    },true);
    
    /*
    $scope.$watch('tagtoolbar.edit',function(newValue, oldValue){
        //console.log(newValue, oldValue);
    });
    */
    
    /* #################################################### */
    
    layoutInit();
    loadDataTree();
    loadDataFilters();
    
});
