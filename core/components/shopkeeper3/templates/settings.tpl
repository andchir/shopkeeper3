
{literal}

<div class="amod-wrapper app-container" ng-app="shkManagerApp">
    
    <div class="amod-container" ng-controller="settingsController" ng-init="toppanel_fixed = false" ng-class="{'container-overflow-a':!toppanel_fixed,'container-overflow-b':toppanel_fixed}">
        <div class="amod-container-b">
            
            <!-- top panel -->
            <div class="relative">
                <div class="panel panel-default panel-top">
                    <div class="panel-heading">
                        
                        <!-- top buttons -->
                        <div class="pull-right">
                            
                            <div class="dropdown pull-right panel-top-ddmenu" ng-include="'menu/main_menu.html'">
                                
                            </div>
                            
                            <button type="button" class="btn btn-primary" ng-click="save()">
                                <span class="glyphicon glyphicon-save"></span>
                                {{ 'shk3.save' | translate }}
                            </button>
                            
                        </div>
                        <!-- /top buttons -->
                        
                        <!-- acom_titile -->
                        <div class="acom_titile">
                            <div>
                                <a class="glyphicon glyphicon-pushpin" ng-click="toppanel_fixed = !toppanel_fixed"></a>
                                <a class="glyphicon" href="{{new_window_url}}" target="_top" ng-class="new_window_icon"></a>
                            </div>
                            <h2>{{ 'shk3.shopkeeper' | translate }}</h2>
                        </div>
                        <!-- /acom_titile -->
                        
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- /top panel -->
            
            <div class="amod-container-c">
                
                <div class="panel panel-default">
                    
                    <div class="panel-body">
                        
                        <form name="settingsForm" id="settingsForm" action="" method="post">
                            
                            <div class="row">
                                
                                <div class="col-md-6">
                                    
                                    <!-- accordion -->
                                    <div class="panel-group" id="accordion">
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#settingOne">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.exchange_rates' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingOne" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="45%">
                                                            <col width="45%">
                                                            <col width="10%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>{{ 'shk3.name' | translate }}</th>
                                                                <th>{{ 'shk3.exchange_rate' | translate }}</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.currency_rate">
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.value">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'currency_rate')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="3">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('currency_rate')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /currency_rate -->
                                                    
                                                </div>
                                            </div>
                                            <!-- /panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#settingTwo">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.orders_table_fields' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingTwo" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="35%">
                                                            <col width="35%">
                                                            <col width="20%">
                                                            <col width="10%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    {{ 'shk3.field_name' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_fields' | translate }}"></span>
                                                                </th>
                                                                <th>{{ 'shk3.field_caption' | translate }}</th>
                                                                <th>
                                                                    {{ 'shk3.field_position' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_fields_pos' | translate }}"></span>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.order_fields">
                                                                <!--td>
                                                                    <input type="checkbox" ng-model="item.active">
                                                                </td-->
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.name">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control input-sm" name="rank" value="0" ng-model="item.rank" ng-initial>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'order_fields')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="4">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('order_fields')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /order_fields -->
                                                    
                                                    <div class="alert alert-info" role="alert">
                                                        
                                                        <div id="accordionHelp1">
                                                            <div>
                                                                <a data-toggle="collapse" data-parent="#accordionHelp1" href="#collapseHelp1">
                                                                    <span class="glyphicon glyphicon-info-sign"></span>
                                                                    {{ 'shk3.help' | translate }}
                                                                </a>
                                                            </div>
                                                            <div id="collapseHelp1" class="collapse">
                                                                
                                                                <table class="table no-border">
                                                                    <colgroup>
                                                                        <col width="60%">
                                                                        <col width="40%">
                                                                    </colgroup>
                                                                    <tr>
                                                                        <td>
                                                                            <b>{{ 'shk3.order_fields' | translate }}:</b>
                                                                            <br>
                                                                            id, price, date, sentdate, note, email, delivery, delivery_price, payment, tracking_num, status, userid
                                                                        </td>
                                                                        <td>
                                                                            <b>{{ 'shk3.additional_fields' | translate }}:</b>
                                                                            <br>
                                                                            username, count_total
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <!-- panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#settingSix">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.contact_details' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingSix" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="35%">
                                                            <col width="35%">
                                                            <col width="20%">
                                                            <col width="10%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>{{ 'shk3.field_name' | translate }}</th>
                                                                <th>{{ 'shk3.field_caption' | translate }}</th>
                                                                <th>
                                                                    {{ 'shk3.field_position' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_contacts_fields_pos' | translate }}"></span>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.contacts_fields">
                                                                <!--td>
                                                                    <input type="checkbox" ng-model="item.active">
                                                                </td-->
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" ng-model="item.name">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control input-sm" name="rank" value="0" ng-model="item.rank" ng-initial>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'contacts_fields')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="4">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('contacts_fields')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /contacts_fields -->
                                                    
                                                </div>
                                            </div>
                                            <!-- panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                    </div>
                                    <!-- accordion -->
                                    
                                </div>
                                <!-- /col-md-6 -->
                                
                                <div class="col-md-6">
                                    
                                    <!-- accordion -->
                                    <div class="panel-group" id="accordion2">
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion2" href="#settingThree">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.statuses' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingThree" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="50%">
                                                            <col width="40%">
                                                            <col width="5%">
                                                            <col width="5%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>{{ 'shk3.name' | translate }}</th>
                                                                <th>
                                                                    {{ 'shk3.mail_template' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_mail_template' | translate }}"></span>
                                                                </th>
                                                                <th>{{ 'shk3.color' | translate }}</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.statuses">
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.tpl">
                                                                </td>
                                                                <td>
                                                                    
                                                                    <div>
                                                                        <input type="hidden" class="form-control input-sm" name="color" ng-model="item.color" minicolors>
                                                                    </div>
                                                                    
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'statuses')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="4">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('statuses')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /statuses -->
                                                    
                                                </div>
                                            </div>
                                            <!-- /panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion2" href="#settingFour">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.delivery_methods' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingFour" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="50%">
                                                            <col width="20%">
                                                            <col width="20%">
                                                            <col width="10%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>{{ 'shk3.name' | translate }}</th>
                                                                <th>{{ 'shk3.price' | translate }}</th>
                                                                <th>
                                                                    {{ 'shk3.max_order_price' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_max_order_price' | translate }}"></span>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.delivery">
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.price">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.free_start">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'delivery')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="4">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('delivery')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /delivery -->
                                                    
                                                </div>
                                            </div>
                                            <!-- /panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion2" href="#settingFive">
                                                        <span class="glyphicon glyphicon-cog"></span>
                                                        {{ 'shk3.payment_methods' | translate }}
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="settingFive" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    
                                                    <table class="table table-hover">
                                                        <colgroup>
                                                            <col width="45%">
                                                            <col width="45%">
                                                            <col width="10%">
                                                        </colgroup>
                                                        <thead>
                                                            <tr>
                                                                <th>{{ 'shk3.name' | translate }}</th>
                                                                <th>
                                                                    {{ 'shk3.value' | translate }}
                                                                    <span class="glyphicon glyphicon-question-sign" tooltip-placement="bottom" uib-tooltip="{{ 'shk3.help_field_value' | translate }}"></span>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="item in data.payments">
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.label">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control input-sm" name="" ng-model="item.value">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-default btn-sm" title="Убрать" ng-click="removeRow($index,'payments')">
                                                                        <span class="glyphicon glyphicon-remove"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="active">
                                                                <td colspan="4">
                                                                    
                                                                    <button type="button" class="btn btn-default btn-sm" ng-click="addRow('payments')">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                        {{ 'shk3.add' | translate }}
                                                                    </button>
                                                                    
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- /payments -->
                                                    
                                                </div>
                                            </div>
                                            <!-- /panel-collapse -->
                                        </div>
                                        <!-- /panel -->
                                        
                                    </div>
                                    <!-- /accordion -->
                                    
                                </div>
                                <!-- /col-md-6 -->
                                
                            </div>
                            <!-- /row -->
                            
                        </form>
                        
                    </div>
                    <!-- /panel-body -->
                </div>
                <!-- /panel -->
                
            </div>
            <!-- /amod-container-c -->
            
        </div>
        <!-- /amod-container-b -->
        
    </div>
    <!-- /amod-container -->
    
    <span us-spinner spinner-start-active="true" spinner-key="spinner-1"></span>
</div>

{/literal}
