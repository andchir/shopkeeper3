
{literal}

<div class="tm-wrapper" ng-app="tagManagerApp">

<div class="tm-container" ng-controller="homeController" ng-init="toppanel_fixed = false" ng-class="{'container-overflow-a':!toppanel_fixed,'container-overflow-b':toppanel_fixed}">
    <div class="tm-container-b">
        
        <!-- top panel -->
        <div class="relative">
            <div class="panel panel-default panel-top">
                <div class="panel-heading">
                    
                    <!-- top buttons -->
                    <div class="pull-right">
                        
                        <button class="btn btn-default" ng-disabled="!active_branch.active" ng-click="updateFilters()">
                            <span class="glyphicon glyphicon-refresh"></span>
                            {{ 'tag_mgr2.update_values' | translate }}
                        </button>
                        
                        <button class="btn btn-primary" ng-disabled="filters_length == 0" ng-click="saveFilters()">
                            <span class="glyphicon glyphicon-save"></span>
                            {{ 'tag_mgr2.save' | translate }}
                        </button>
                        
                    </div>
                    <!-- /top buttons -->
                    
                    <!-- acom_titile -->
                    <div class="acom_titile">
                        <div>
                            <a class="glyphicon glyphicon-pushpin" ng-click="toppanel_fixed = !toppanel_fixed"></a>
                            <a class="glyphicon" href="{{new_window_url}}" target="_top" ng-class="new_window_icon"></a>
                        </div>
                        <h2>{{ 'tag_mgr2.tag_manager' | translate }}</h2>
                    </div>
                    <!-- /acom_titile -->
                    
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- /top panel -->
        
        <div class="tm-container-c">
            
            <div class="row">
                
                <div class="col-md-8 relative">
                    
                    <div class="alert alert-warning alert-dismissable" ng-repeat="(k,v) in alerts">
                        <button type="button" class="close" ng-click="closeMsg($index)">&times;</button>
                        {{v}}
                    </div>
                    
                    <div class="panel panel-default" ng-init="filterlist_collapsed = false">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="glyphicon" ng-click="filterlist_collapsed = !filterlist_collapsed" ng-class="{'glyphicon-chevron-down':filterlist_collapsed,'glyphicon-chevron-up':!filterlist_collapsed}"></a>
                                &nbsp;
                                {{ 'tag_mgr2.filters' | translate }} ({{filters_length}})
                            </h4>
                        </div>
                        <div ng-show="!filterlist_collapsed">
                            <div class="panel-body">
                                
                                <ul class="flt-list" ui-sortable="sortableFilterOptions" ng-model="filters_all">
                                    <li ng-repeat="item in filters_all" class="label" ng-class="{'label-warning':item.active,'label-default':!item.active}">
                                        {{item.tvcaption}}
                                        <a class="glyphicon glyphicon-ok icn-dark" ng-click="filterGroupAdd($index)" tooltip="{{ 'tag_mgr2.active_unactive' | translate }}"></a>
                                    </li>
                                </ul>
                                
                                <div ng-show="loading && !filters_all[0]" class="loader-line"></div>
                                
                                <div class="clearfix"></div>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" ng-model="data.searchFlt" placeholder="{{ 'tag_mgr2.search' | translate }}" style_="width:400px;">
                    </div>
                    
                    <div class="panel panel-default" ng-repeat="flt in filters | filter:data.searchFlt" ng-init="outerIndex = $index">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <div class="pull-right" ng-init="filter_caption_edit = false" style="width:230px; margin:-3px -3px 0 0;">
                                    <div class="pull-right">
                                        <button class="btn btn-default btn-xs" ng-click="filter_caption_edit = !filter_caption_edit"><span class="glyphicon glyphicon-pencil"></span></button>
                                    </div>
                                    <input type="text" class="form-control input-sm" ng-model="flt.tvcaption" ng-show="filter_caption_edit" style="width:200px; margin-top:-3px; display:inline-block;">
                                </div>
                                <span>
                                    {{flt.tvcaption}}
                                </span>
                            </h4>
                        </div>
                        <div class="panel-body">
                            
                            <ul class="flt-tags-list" ui-sortable="sortableOptions" ng-model="flt.tags">
                                <li ng-repeat="tag in flt.tags" class="label" ng-class="{'label-success': tag.active, 'label-default': !tag.active}" ng-init="innerIndex = $index">
                                    <span class="glyphicon glyphicon-tag"></span>
                                    {{tag.value}}
                                    <a class="glyphicon glyphicon-collapse-up icn-dark" ng-click="showTagToolbar($event,flt.tvid,tag.value)"></a>
                                </li>
                            </ul>
                            
                            <div class="clearfix"></div>
                            
                            <div ng-show="flt.loading" class="loader-line"></div>
                            
                        </div>
                    </div>
                    
                </div><!-- /col -->
                
                <div class="col-md-4">
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            
                            <div class="pull-right">
                                <button tooltip="{{ 'tag_mgr2.remove_all_filters' | translate }}" ng-click="filtersRemoveAll()" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span></button>
                            </div>
                            
                            <button tooltip="{{ 'tag_mgr2.collapse_expand' | translate }}" ng-click="expandAll()" class="btn btn-default"><span class="glyphicon glyphicon-folder-open"></span></button>
                            <button tooltip="{{ 'tag_mgr2.refresh_tree' | translate }}" ng-click="refreshTree()" class="btn btn-default"><span class="glyphicon glyphicon-refresh"></span></button>
                            <button tooltip="{{ 'tag_mgr2.remove_cat_filters' | translate }}" ng-click="filtersRemoveCategory()" ng-show="active_branch.active" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></button>
                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            
                            <div ng-show="tree_loading" class="loader-line"></div>
                            
                            <abn-tree ng-style="{'visibility':(tree_loading ? 'hidden' : 'visible')}" tree-data="tree_data" on-select="my_tree_handler(branch)" expand-level="2"></abn-tree>
                            
                        </div>
                    </div>
                    
                </div><!-- /col -->
                
            </div><!-- /row -->
            
            <!-- tag_toolbar -->
            <div id="tag_toolbar" class="popover top in tagtoolbar" ng-style="tagtoolbar.style">
                <div class="arrow"></div>
                <div class="popover-content">
                    <div class="form-group" style="width:157px;" ng-show="tagtoolbar.edit">
                        <input type="text" id="tagtoolbarValue" class="form-control" ng-model="filters[tagtoolbar.outerIndex].tags[tagtoolbar.innerIndex].value">
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default" type="button" title="{{ 'tag_mgr2.update_value_for_all' | translate }}" ng-click="tagUpdate()" ng-disabled="!tagtoolbar.edit"><span class="glyphicon glyphicon-save"></span></button>
                        <button class="btn btn-default" type="button" title="{{ 'tag_mgr2.active_unactive' | translate }}" ng-click="filters[tagtoolbar.outerIndex].tags[tagtoolbar.innerIndex].active = !filters[tagtoolbar.outerIndex].tags[tagtoolbar.innerIndex].active" ng-class="{'active':filters[tagtoolbar.outerIndex].tags[tagtoolbar.innerIndex].active}"><span class="glyphicon glyphicon-ok"></span></button>
                        <button class="btn btn-default" type="button" title="{{ 'tag_mgr2.edit_value' | translate }}" ng-click="tagEdit()" ng-class="{'active':tagtoolbar.edit}"><span class="glyphicon glyphicon-pencil"></span></button>
                        <button class="btn btn-default" type="button" title="{{ 'tag_mgr2.remove' | translate }}" ng-click="tagDelete()"><span class="glyphicon glyphicon-remove"></span></button>
                    </div>
                </div>
            </div>
            <!-- /tag_toolbar -->
            
        </div>
        <!-- /tm-container-c -->
    
    </div>
    <div class="tm_loader" ng-show="showloader || saving" ng-class="{'loader-transp':saving}"></div>
</div>

<script type="text/ng-template" id="js/mgr/angular-bootstrap-nav-tree/abn_tree_template.html">
    <div class="form-group">
        <input type="text" class="form-control input-sm" ng-model="searchTreeFlt" placeholder="{{ 'tag_mgr2.search' | translate }}">
    </div>
    <ul class="nav nav-list nav-pills nav-stacked abn-tree">
        <li ng-repeat="row in tree_rows | filter: (searchTreeFlt || {visible:true}) track by row.branch.uid" ng-animate="'abn-tree-animate'" ng-class="'level-' + {{ row.level }} + (row.branch.selected ? ' active':'') + (row.active ? ' checked' : '')" class="abn-tree-row">
            <a>
                <span ng-class="row.tree_icon" class="indented glyphicon" ng-click="row.branch.expanded = !row.branch.expanded"></span>
                <span ng-class="{'glyphicon-unchecked': !row.active,'glyphicon-check': row.active}" class="indented glyphicon"> </span>
                <span class="indented tree-label" ng-click="user_clicks_branch(row.branch)">{{ row.label }}</span>
            </a>
        </li>
    </ul>
</script>

<script type="text/ng-template" id="modal_alert.html">
    <div class="modal-header">
        <h3>{{title}}</h3>
    </div>
    <div class="modal-body">
        {{message}}
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" ng-click="ok()">{{ 'tag_mgr2.ok' | translate }}</button>
        <!--button class="btn btn-warning" ng-click="cancel()">Cancel</button-->
    </div>
</script>

<script type="text/ng-template" id="modal_confirm.html">
    <div class="modal-header">
        <h3>{{ 'tag_mgr2.confirm' | translate }}</h3>
    </div>
    <div class="modal-body">
        {{ 'tag_mgr2.confirm_msg' | translate }}
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" ng-click="ok()">{{ 'tag_mgr2.confirm_ok' | translate }}</button>
        <button class="btn btn-warning" ng-click="cancel()">{{ 'tag_mgr2.cancel' | translate }}</button>
    </div>
</script>

</div>

{/literal}
