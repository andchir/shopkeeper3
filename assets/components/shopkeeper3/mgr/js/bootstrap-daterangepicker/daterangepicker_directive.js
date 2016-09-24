
/*

https://github.com/dangrossman/bootstrap-daterangepicker

ng-bs-daterangepicker
AngularJS directive

by Andchir - http://wdevblog.net.ru/

*/

angular.module('dateRangePicker', [])
.directive('daterangepicker', function() {
    return {
        restrict: 'A',
        require : 'ngModel',
        link : function (scope, element, attrs, ngModelCtrl) {
            
            var opts = !!attrs.options ? scope[attrs.options] : {};
            
            angular.element(function(){
                
                var daterangepickerOptions = {
                    parentEl: '.app-container',
                    format: 'DD/MM/YYYY',
                    showDropdowns: true,
                    locale: {
                        applyLabel: opts.apply_txt,
                        cancelLabel: opts.reset_txt,
                        fromLabel: opts.from_txt,
                        toLabel: opts.to_txt,
                        customRangeLabel: opts.othet_period_txt
                    } ,
                    ranges: {},
                    startDate: opts.startDate ? opts.startDate : moment().subtract(29,'days'),
                    endDate: opts.endDate ? opts.endDate : moment(),
                    opens: 'right',
                    buttonClasses: ['btn btn-default'],
                    applyClass: 'btn-sm btn-primary',
                    cancelClass: 'btn-sm'
                };
                
                daterangepickerOptions.ranges[ opts.today_txt ] = [moment(), moment()];
                daterangepickerOptions.ranges[ opts.yesterday_txt ] = [moment().subtract(1,'days'), moment().subtract(1,'days')];
                daterangepickerOptions.ranges[ opts.last_7_days_txt ] = [moment().subtract(6,'days'), moment()];
                daterangepickerOptions.ranges[ opts.last_30_days_txt ] = [moment().subtract(29,'days'), moment()];
                daterangepickerOptions.ranges[ opts.this_month_txt ] = [moment().startOf('month'), moment().endOf('month')];
                
                element
                .daterangepicker(
                    daterangepickerOptions
                )
                .on('show.daterangepicker', function(ev, picker) {
                    
                    if ( !angular.element( '.ranges li:last', picker.container ).is('.active') ) {
                        angular.element( '.calendar.right', picker.container ).hide();
                        angular.element( '.calendar.left', picker.container ).hide();
                    }
                   
                })
                .on('hide.daterangepicker', function(ev, picker) {
                    //element.trigger('apply.daterangepicker');
                })
                .on('cancel.daterangepicker', function(ev, picker) {
                    element.val('');
                    scope.$apply(function () {
                        ngModelCtrl.$setViewValue( '' );
                    });
                })
                .on('apply.daterangepicker', function(ev, picker) {
                    scope.$apply(function () {
                        ngModelCtrl.$setViewValue( $(ev.target).val() );
                    });
                });
                
            });
            
            angular.element(document).on('click','.daterangepicker .ranges li:last ',function(){
               
               angular.element( '.calendar.right', '.daterangepicker' ).show();
               angular.element( '.calendar.left', '.daterangepicker' ).show();
               
            });
            
        }
    }
});
