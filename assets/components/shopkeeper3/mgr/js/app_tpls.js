
/* templates */

angular.module("app.tpls", [
    "ng-table/headers/checkbox.html",
    "modals/change_status.html",
    "modals/confirm.html",
    "modals/alert.html",
    "menu/main_menu.html"
]);

angular.module("ng-table/headers/checkbox.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("ng-table/headers/checkbox.html",
        '<div class="text-center">\
        <button class="btn btn-default btn-sm" ng-click="selectAll()"><span class="glyphicon glyphicon-ok"></span></buttin>\
        </div>'
    );
}]);

angular.module("modals/change_status.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modals/change_status.html",
    '<div class="modal-header">\
        <h3 class="modal-title">{{ \'shk3.change_status\' | translate }}<small ng-show="data.order_id"> - {{ \'shk3.order\' | translate }} #{{data.order_id_str}}</small></h3>\
    </div>\
    <div class="modal-body">\
        <div class="row">\
            <div class="col-sm-12">\
                <form name="changeStatus" id="changeStatus" method="" role="form">\
                    <div class="form-group">\
                        <label for="selectStatus">{{ \'shk3.status\' | translate }}</label>\
                        <select id="selectStatus" class="form-control" ng-model="data.status" required>\
                            <option ng-repeat="item in statusesData" ng-selected="item.id == data.status" value="{{item.id}}">{{item.label}}</option>\
                        </select>\
                    </div>\
                </form>\
                <div class="alert alert-warning" role="alert" ng-show="data.message">{{data.message}}</div>\
            </div>\
        </div>\
    </div>\
    <div class="modal-footer">\
        <button class="btn btn-primary" ng-click="save()" ng-disabled="changeStatus.$invalid || modal_loading">{{ \'shk3.accept\' | translate }}</button>\
        <button class="btn btn-warning" ng-click="cancel()">{{ \'shk3.cancel\' | translate }}</button>\
    </div>'
    );
}]);

angular.module("modals/confirm.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modals/confirm.html",
    '<div class="modal-header">\
        <h3 class="modal-title">{{ \'shk3.confirm\' | translate }}</h3>\
    </div>\
    <div class="modal-body">\
        <div class="row">\
            <div class="col-sm-12">\
                {{ \'shk3.confirm_question\' | translate }}\
            </div>\
        </div>\
    </div>\
    <div class="modal-footer">\
        <button class="btn btn-primary" ng-click="ok()" ng-disabled="modal_loading">{{ \'shk3.accept\' | translate }}</button>\
        <button class="btn btn-warning" ng-click="cancel()">{{ \'shk3.cancel\' | translate }}</button>\
    </div>'
    );
}]);

angular.module("modals/alert.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modals/alert.html",
    '<div class="modal-header">\
        <h3 class="modal-title">{{title}}</h3>\
    </div>\
    <div class="modal-body">\
        <div class="row">\
            <div class="col-sm-12">\
                {{message}}\
            </div>\
        </div>\
    </div>\
    <div class="modal-footer">\
        <button class="btn btn-warning" ng-click="cancel()">{{ \'shk3.close\' | translate }}</button>\
    </div>'
    );
}]);

angular.module("menu/main_menu.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("menu/main_menu.html",
    '<button class="btn btn-default" type="button" data-toggle="dropdown">\
        <span class="glyphicon glyphicon-align-justify"></span>\
    </button>\
    <ul class="dropdown-menu" role="menu">\
        <li role="presentation" ng-repeat="item in main_menu" ng-class="{active: item.id == menu_current}">\
            <a role="menuitem" tabindex="-1" href="{{item.link}}">\
                <span class="glyphicon {{item.icon}}"></span>\
                {{ item.name | translate }}\
            </a>\
        </li>\
    </ul>'
    );
}]);


