<textarea id="pe{{$resource_tv_id}}" name="tv{{$tv->get('id')}}" class="param-edit" style="display:none;">{{$tv->get('value')|escape}}</textarea>

<div class="param_edit_wrapper" id="pe_tv{{$resource_tv_id}}"></div>

<script type="text/javascript">
var tv{{$resource_tv_id}}_val = '{{$tv->get('value')}}';
var tv{{$resource_tv_id}}_val_arr = tv{{$resource_tv_id}}_val.length > 0 ? tv{{$resource_tv_id}}_val.split('||') : [];
var tv{{$resource_tv_id}}_fcount = {{$params.input_count}};
var tv{{$resource_tv_id}}_defaultval = '{{$params.input_defaultval}}'
var tv{{$resource_tv_id}}_captions = '{{$params.input_captions}}';
var tv{{$resource_tv_id}}_captions_arr = tv{{$resource_tv_id}}_captions.length > 0 ? tv{{$resource_tv_id}}_captions.replace(', ',',').split(',') : [];
var tv{{$resource_tv_id}}_data = [];
var tv{{$resource_tv_id}}_fieldnames = [];
var tv{{$resource_tv_id}}_columns = [];

for(var i in tv{{$resource_tv_id}}_val_arr){
    if(typeof(tv{{$resource_tv_id}}_val_arr[i]) == 'string'){
        var temp_arr = tv{{$resource_tv_id}}_val_arr[i].split('==');
        tv{{$resource_tv_id}}_data.push(temp_arr);
    }
}
for(var i=0;i<tv{{$resource_tv_id}}_fcount;i++){
    tv{{$resource_tv_id}}_fieldnames.push('field'+(i+1));
    var field_type = i==0 ? '{{$params.input_ftype}}' : 'text';
    var uniqueid = Ext.id();
    switch(field_type){
        case 'image':
            
            tv{{$resource_tv_id}}_columns.push(
                {
                    header: typeof(tv{{$resource_tv_id}}_captions_arr[i])!='undefined' ? tv{{$resource_tv_id}}_captions_arr[i] : _('name')
                    ,width: 200
                    ,dataIndex: 'field'+(i+1)
                    ,renderer: SHK.grid.imageRenderer
                    ,editor: {
                        xtype: 'shk-panel-grid-image'
                        ,id: uniqueid
                        ,tv: '{{$resource_tv_id}}'
                        ,cls: 'shk-panel-grid-image'
                        ,enableKeyEvents: true
                        ,allowBlank: true
                        ,submitValue: false
                        ,width: 70
                        ,source: {{$tv_source}}
                        ,source_base_url: '{{$tv_source_base_url}}'
                    }
                }
            );
            
        break;
        default:
            
            tv{{$resource_tv_id}}_columns.push(
            {
                header: typeof(tv{{$resource_tv_id}}_captions_arr[i])!='undefined' ? tv{{$resource_tv_id}}_captions_arr[i] : _('name')
                ,width: 200
                ,dataIndex: 'field'+(i+1)
                ,editor: {xtype: 'textfield'}
            }
        );
            
        break;
    }
}

function tv{{$resource_tv_id}}UpdateValue(e) {
    
    var data_arr = e.grid.getDataArray('==');
    outString = data_arr.join('||');
    Ext.get('pe{{$resource_tv_id}}').dom.value = outString;
    
    e.grid.reload();
    
}

Ext.onReady(function() {
    
    var grid{{$resource_tv_id}} = MODx.load({
        xtype: 'shk-grid-local'
        ,id: 'pe_grid{{$resource_tv_id}}'
        ,columns: tv{{$resource_tv_id}}_columns
        ,fields: tv{{$resource_tv_id}}_fieldnames
        ,store: {
            xtype: 'arraystore'
            ,idIndex: 0
            ,fields: tv{{$resource_tv_id}}_fieldnames
            ,data: tv{{$resource_tv_id}}_data
            ,source: {{$tv_source}}
            ,source_base_url: '{{$tv_source_base_url}}'
        }
        ,listeners: {
            'afteredit': function(e){
                tv{{$resource_tv_id}}UpdateValue(e);
                MODx.fireResourceFormChange();
            }
        }
        ,removeCallback: function(grid){
            var e = {"grid":grid}
            tv{{$resource_tv_id}}UpdateValue(e);
            MODx.fireResourceFormChange();
        }
        ,renderTo: 'pe_tv{{$resource_tv_id}}'
    });

});

</script>