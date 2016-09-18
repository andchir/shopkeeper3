
{literal}

<div class="amod-wrapper app-container" ng-app="shkManagerApp">
    
    <div class="amod-container" ng-controller="statsController" ng-init="toppanel_fixed = false" ng-class="{'container-overflow-a':!toppanel_fixed,'container-overflow-b':toppanel_fixed}">
        <div class="amod-container-b">
            
            <!-- top panel -->
            <div class="relative">
                <div class="panel panel-default panel-top">
                    <div class="panel-heading">
                        
                        <!-- top buttons -->
                        <div class="pull-right">
                            
                            <div class="dropdown pull-right panel-top-ddmenu" ng-include="'menu/main_menu.html'">
                                
                            </div>
                            
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
                    
                    <div class="panel-heading">
                        
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
                            
                            <button type="button" class="btn btn-default" ng-click="submitFilters()">
                                <span class="glyphicon glyphicon-ok"></span>
                                {{ 'shk3.apply' | translate }}
                            </button>
                            
                        </div>
                        <!-- /tool-panel-item -->
                        
                        <div class="clearfix"></div>
                    </div>
                    <!-- /panel-heading -->
                    
                    <div class="panel-body" style="min-height: 400px;">
                        
                        <div id="chart"></div>
                        
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

