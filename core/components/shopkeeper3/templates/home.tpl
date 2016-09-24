
{literal}

<div class="amod-wrapper app-container" ng-app="shkManagerApp">
    
    <div class="amod-container" ng-controller="homeController" ng-init="toppanel_fixed = false" ng-class="{'container-overflow-a':!toppanel_fixed,'container-overflow-b':toppanel_fixed}">
        <div class="amod-container-b" ng-cloak>
            
            <!-- top panel -->
            <div class="relative">
                <div class="panel panel-default panel-top">
                    <div class="panel-heading">
                        
                        <!-- top buttons -->
                        <div class="pull-right">
                            
                            <div class="dropdown pull-right panel-top-ddmenu" ng-include="'menu/main_menu.html'">
                                
                            </div>
                            
                            <button type="button" class="btn btn-primary" ng-disabled="selected_total == 0" ng-click="changeStatus()">
                                <span class="glyphicon glyphicon-retweet"></span>
                                <span ng-bind="'shk3.change_status' | translate"></span>
                                <span ng-hide="selected_total == 0">(<span ng-bind="selected_total"></span>)</span>
                            </button>
                            
                            <button type="button" class="btn btn-primary" ng-disabled="selected_total == 0" ng-click="deleteOrders()">
                                <span class="glyphicon glyphicon-remove"></span>
                                <span ng-bind="'shk3.delete' | translate"></span>
                                <span ng-hide="selected_total == 0">(<span ng-bind="selected_total"></span>)</span>
                            </button>
                            
                        </div>
                        <!-- /top buttons -->
                        
                        <!-- acom_titile -->
                        <div class="acom_titile">
                            <div>
                                <a class="glyphicon glyphicon-pushpin" ng-click="toppanel_fixed = !toppanel_fixed"></a>
                                <a class="glyphicon" href="{{new_window_url}}" target="_top" ng-class="new_window_icon"></a>
                            </div>
                            <h2 ng-bind="'shk3.shopkeeper' | translate"></h2>
                        </div>
                        <!-- /acom_titile -->
                        
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- /top panel -->
            
            <div class="amod-container-c">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        
                        <div class="tool-panel">
                            
                            <form name="ordersForm" id="ordersForm" action="" method="post">
                                
                                <div class="pull-right">
                                    
                                    <div class="tool-panel-item">
                                        <button type="button" class="btn btn-default" ng-click="gridReload()">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                        </button>
                                    </div>
                                    <!-- /tool-panel-item -->
                                    
                                    <div class="tool-panel-item">
                                        <a class="btn btn-default" ng-mousedown="csv.generate()" ng-href="{{ csv.link() }}" download="orders.csv">
                                            <span class="glyphicon glyphicon-export"></span>
                                            <span ng-bind="'shk3.csv_export' | translate"></span>
                                        </a>
                                    </div>
                                    <!-- /tool-panel-item -->
                                    
                                </div>
                                <!-- /pull-right -->
                                
                                <div class="tool-panel-item">
                                    
                                    <div class="control-group" style="width: 240px;">
                                        <div class="controls">
                                           <div class="input-prepend input-group">
                                                <span class="add-on input-group-addon">
                                                   <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                </span>
                                                <input type="text" name="daterange" id="daterange" class="form-control" ng-model="filters.date" options="daterangepickerOptions" daterangepicker /> 
                                           </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <!-- /tool-panel-item -->
                                
                                <div class="tool-panel-item">
                                    
                                    <select id="filterSelectStatus" class="multiselect" name="status"
                                        data-options="multiselectOptions"
                                        ng-model="filters.status" ng-options="item.id as item.label for item in statusesData"
                                        multiple="multiple" multiselect-dropdown>
                                    </select>
                                    
                                </div>
                                <!-- /tool-panel-item -->
                                
                                <div class="tool-panel-item">
                                    
                                    <button type="button" class="btn btn-default" ng-click="submitFilters()">
                                        <span class="glyphicon glyphicon-ok"></span>
                                        <span ng-bind="'shk3.apply' | translate"></span>
                                        <span ng-hide="filters_selected == 0">(<span ng-bind="filters_selected"></span>)</span>
                                    </button>
                                    
                                </div>
                                <!-- /tool-panel-item -->
                                
                            </form>
                            
                            <div class="clearfix"></div>
                        </div>
                        <!-- /tool-panel -->
                        
                    </div>
                    <!-- /panel-heading -->
                    
                    <div loading-container="tableParams.settings().$loading">
                        
                        <table ng-table="tableParams" class="table table-hover ng-table-responsive ng-table-rowselected" export-csv="csv">
                            <thead>
                                <tr>
                                    <th width="30" class="ng-table-check" ng-include="'ng-table/headers/checkbox.html'">
                                        
                                    </th>
                                    <th class="header sortable" ng-repeat="column in grid_columns"
                                        class="text-center sortable" ng-class="{
                                            'sort-asc': tableParams.isSortBy(column.name, 'asc'),
                                            'sort-desc': tableParams.isSortBy(column.name, 'desc')
                                          }"
                                        ng-click="tableParams.sorting(column.name, tableParams.isSortBy(column.name, 'asc') ? 'desc' : 'asc')">
                                        <div ng-bind="column.label"></div>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="order in $data"
                                    ng-class="{'active': order.$selected}"
                                    >
                                    <td class="ng-table-check" header="'ng-table/headers/checkbox.html'">
                                        <span class="glyphicon glyphicon-ok" ng-click="order.$selected = !order.$selected; changeSelection(order)"></span>
                                    </td>
                                    
                                    <td ng-repeat="column in grid_columns" data-title="column.label" sortable="column.name">
                                        <div id="{{order.id}}" userid="{{order.userid}}" fieldvalue="{{order[column.name]}}" fieldname="{{column.name}}" render-field-value></div>
                                    </td>
                                    
                                    <td data-title="'Действия'" class="text-right" style="width: 110px;">
                                        
                                        <span class="order-note" note="{{order.note}}" order-note></span>
                                        
                                        <button type="button" class="btn btn-default btn-sm" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.view' | translate }}" ng-click="viewOrder(order.id,'view')">
                                            <span class="glyphicon glyphicon-eye-open"></span>
                                        </button>
                                        
                                        <button type="button" class="btn btn-default btn-sm" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.edit' | translate }}" ng-click="viewOrder(order.id,'edit')">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                        </button>
                                        
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-center" ng-hide="tableParams.total() > 0" style="margin-top: -20px;">
                            <span ng-bind="'shk3.orders_empty' | translate"></span>
                        </div>
                        
                    </div>
                    <!-- /loading-container -->
                    
                    <div class="clearfix"></div>
                </div>
                <!-- /panel -->
                
            </div>
            <!-- /amod-container-c -->
            
        </div>
        <!-- /amod-container-b -->
        
    </div>
    <!-- /amod-container -->

    <div class="ajax_loader loader-transp" ng-show="loading"></div>
    
<!-- ######################## templates ######################## -->

<!-- order_view -->
<script type="text/ng-template" id="modals/order_view.html">
    <div class="modal-header">
        <h3>{{ 'shk3.order_view' | translate }} - <small>{{ 'shk3.order' | translate }} #{{data.order.id}}</small></h3>
    </div>
    <div class="modal-body">
        
        <uib-tabset ng-show="data.order.purchases">
            <uib-tab heading="{{ 'shk3.order_data' | translate }}">
                
                <table class="table">
                    <colgroup>
                        <col width="25%" span="4">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>{{ 'shk3.order_time' | translate }}:</th>
                            <th>{{ 'shk3.order_delivery' | translate }}:</th>
                            <th>{{ 'shk3.delivery_price' | translate }}:</th>
                            <th>{{ 'shk3.order_payment' | translate }}:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{data.order.date}}</td>
                            <td>{{data.order.delivery}}</td>
                            <td>{{data.order.delivery_price}}</td>
                            <td>{{data.order.payment}}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="alert alert-sm alert-warning" role="alert" ng-show="data.order.note">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    &nbsp;
                    {{data.order.note}}
                </div>
                
                <h3>{{ 'shk3.order_composition' | translate }}</h3>
                <table class="table table-hover">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="25%">
                        <col width="30%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>{{ 'shk3.product' | translate }}</th>
                            <th>{{ 'shk3.parameters' | translate }}</th>
                            <th>{{ 'shk3.count' | translate }}</th>
                            <th>{{ 'shk3.price' | translate }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in data.order.purchases">
                            <td>{{$index+1}}</td>
                            <td>{{item.p_id}}</td>
                            <td><a href="{{item.url}}" target="_blank">{{item.name}}</a></td>
                            <td>
                                <div ng-repeat="opt in item.options">{{ opt[0] }}</div>
                            </td>
                            <td>{{item.count}}</td>
                            <td>{{item.price | number: 2}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <td class="text-right" colspan="4">
                                <b>{{ 'shk3.total' | translate }}:</b>
                            </td>
                            <td>
                                <b>{{data.total_items}} {{ 'shk3.pcs' | translate }}</b>
                            </td>
                            <td>
                                <b>{{data.total_price | number: 2}} {{data.order.currency}}</b>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                
            </uib-tab>
            <uib-tab heading="{{ 'shk3.contacts_data' | translate }}">
                
                <table class="table table-hover noborder-top">
                    <tbody>
                        <tr ng-repeat="item in data.order.contacts">
                            <td><b>{{item.label}}:</b></td>
                            <td>{{item.value}}</td>
                        </tr>
                    </tbody>
                </table>
                
            </uib-tab>
        </uib-tabset>
        
        <div class="loader-line" ng-show="modal_loading"></div>
        
        <div class="alert alert-warning" role="alert" ng-show="data.message">
            <span class="glyphicon glyphicon-warning-sign"></span>
            {{data.message}}
        </div>
        
    </div>
    <div class="modal-footer">
        
        <button type="button" class="btn btn-info" ng-click="edit(data.order.id)">
            <span class="glyphicon glyphicon-pencil"></span>
            {{ 'shk3.edit' | translate }}
        </button>
        
        <button type="button" class="btn btn-warning" ng-click="cancel()">{{ 'shk3.close' | translate }}</button>
        
    </div>
</script>
<!-- /order_view -->

<!-- order_edit -->
<script type="text/ng-template" id="modals/order_edit.html">
    <div class="modal-header">
        <h3>{{ 'shk3.order_edit' | translate }} - <small>{{ 'shk3.order' | translate }} #{{data.order.id}}</small></h3>
    </div>
    <div class="modal-body">
        
        <form name="orderEditForm" id="orderEditForm" action="" method="">
        
            <uib-tabset ng-show="data.order.purchases">
                <uib-tab heading="{{ 'shk3.order_data' | translate }}">
                    
                    <table class="table">
                        <colgroup>
                            <col width="25%" span="4">
                        </colgroup>
                        <thead>
                            <tr>
                            <th>{{ 'shk3.order_time' | translate }}:</th>
                            <th>{{ 'shk3.order_delivery' | translate }}:</th>
                            <th>{{ 'shk3.delivery_price' | translate }}:</th>
                            <th>{{ 'shk3.order_payment' | translate }}:</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{data.order.date}}</td>
                                <td>
                                    <select class="form-control input-sm" ng-model="data.order.delivery">
                                        <option ng-repeat="item in settings.delivery" ng-value="item.label" ng-selected="item.label == data.order.delivery">{{item.label}}</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control input-sm" type="number" name="delivery_price" ng-model="data.order.delivery_price" ng-class="{'error':orderEditForm.delivery_price.$error.number}">
                                </td>
                                <td>
                                    <select class="form-control input-sm" ng-model="data.order.payment">
                                        <option ng-repeat="item in settings.payments" ng-value="item.value" ng-selected="item.value == data.order.payment">{{item.label}}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="alert alert-sm alert-warning" role="alert">
                        <table style="width:100%;">
                            <tr>
                                <td style="width:20px;">
                                    <span class="glyphicon glyphicon-info-sign"></span>
                                </td>
                                <td style="padding-left:10px;">
                                    <input type="text" class="form-control input-sm" ng-model="data.order.note">
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <h3>{{ 'shk3.order_composition' | translate }}</h3>
                    <table class="table table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="10%">
                            <col width="20%">
                            <col width="30%">
                            <col width="15%">
                            <col width="15%">
                            <col width="5%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>{{ 'shk3.product' | translate }}</th>
                                <th>{{ 'shk3.parameters' | translate }}</th>
                                <th>{{ 'shk3.count' | translate }}</th>
                                <th>{{ 'shk3.price' | translate }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in data.order.purchases" ng-form="form">
                                <td>{{$index+1}}</td>
                                <td>
                                    <input type="number" class="form-control input-sm" name="p_id" ng-model="item.p_id" ng-readonly="item.id" ng-class="{'error':form.p_id.$error.number}">
                                </td>
                                <td><input type="text" class="form-control input-sm" ng-model="item.name"></td>
                                <td>
                                    <div>
                                        <table class="table table-bordered">
                                            <colgroup>
                                                <col width="60%">
                                                <col width="40%">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="small">{{ 'shk3.value' | translate }}</th>
                                                    <th class="small">{{ 'shk3.price' | translate }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="opt in item.options">
                                                    <td>
                                                        <input type="text" class="form-control input-sm" ng-model="opt[0]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control input-sm" ng-model="opt[1]">
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="active">
                                                    <td colspan="2">
                                                        
                                                        <button type="button" class="btn btn-default btn-xs" ng-click="addOption($index)">
                                                            <span class="glyphicon glyphicon-plus"></span>
                                                            {{ 'shk3.add' | translate }}
                                                        </button>
                                                        
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </td>
                                <td><input type="number" class="form-control input-sm" name="count" ng-model="item.count" ng-class="{'error':form.count.$error.number}"></td>
                                <td>
                                    <input type="number" class="form-control input-sm" name="price" ng-model="item.price" ng-class="{'error':form.price.$error.number}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'purchases')">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <td colspan="3">
                                    
                                    <div>
                                        <button type="button" class="btn btn-default btn-sm" ng-click="addRow('purchases')">
                                            <span class="glyphicon glyphicon-plus"></span>
                                            {{ 'shk3.add' | translate }}
                                        </button>
                                    </div>
                                    
                                </td>
                                <td class="text-right">
                                    <b>{{ 'shk3.total' | translate }}:</b>
                                </td>
                                <td>
                                    <b>{{data.total_items}} {{ 'shk3.pcs' | translate }}</b>
                                </td>
                                <td>
                                    <b>{{data.total_price | number: 2}} {{data.order.currency}}</b>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                </uib-tab>
                <uib-tab heading="{{ 'shk3.contacts_data' | translate }}">
                    
                    <table class="table table-hover noborder-top">
                        <colgroup>
                            <col width="40%">
                            <col width="50%">
                            <col width="10%">
                        </colgroup>
                        <tbody>
                            <tr ng-repeat="item in data.order.contacts">
                                <td><input type="text" class="form-control input-sm" ng-model="item.label"></td>
                                <td>
                                    <input type="text" class="form-control input-sm" ng-model="item.value">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'contacts')">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <td colspan="3">
                                    
                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('contacts')">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        {{ 'shk3.add' | translate }}
                                    </button>
                                    
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    
                </uib-tab>
            </uib-tabset>
            
        </form>
        
        <div class="loader-line" ng-show="modal_loading"></div>
        
        <div class="alert alert-warning" role="alert" ng-show="data.message">
            <span class="glyphicon glyphicon-warning-sign"></span>
            {{data.message}}
        </div>
        
    </div>
    <div class="modal-footer">
        
        <button type="button" class="btn btn-info" ng-click="view(data.order.id)">
            <span class="glyphicon glyphicon-eye-open"></span>
            {{ 'shk3.order_preview' | translate }}
        </button>
        
        <button type="button" class="btn btn-primary" ng-click="save()" ng-disabled="orderEditForm.$invalid || modal_loading">
            <span class="glyphicon glyphicon-ok"></span>
            {{ 'shk3.save' | translate }}
        </button>
        
        <button type="button" class="btn btn-warning" ng-click="cancel()">{{ 'shk3.close' | translate }}</button>
        
    </div>
</script>
<!-- /order_edit -->
    
    <div us-spinner spinner-start-active="true" spinner-key="spinner-1"></div>
</div>

{/literal}

