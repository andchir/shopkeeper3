<textarea id="pe{$resource_tv_id}" name="tv{$tv->get('id')}" class="param-edit" style="display:none;">{$tv->get('value')|escape}</textarea>

<div class="param_edit_wrapper" id="pe_tv{$resource_tv_id}"></div>

<script type="text/javascript">

(function( containerId, targetId, captions, value, tv_fcount, field_type, defaultval, tvSource, tvSourceBaseUrl ){

    var grid;

    var tv_val_arr = value.length > 0
                    ? value.split('||')
                    : [],
            tv_captions_arr = captions.length > 0
                    ? captions.replace(', ',',').split(',')
                    : [],
            tv_data = [],
            pe_tv_columns = [],
            tv_fieldnames = [];

    for( var i = 0; i < tv_val_arr.length; i++ ){
        if(typeof tv_val_arr[i] == 'string'){
            var temp_arr = tv_val_arr[i].split('==');
            tv_data.push(temp_arr);
        }
    }

    for( var i = 0; i < tv_fcount; i++ ){
        tv_fieldnames.push('field'+(i+1));
        var uniqueid = Ext.id();
        var currentFieldType = i==0 ? field_type : 'text';
        switch( currentFieldType ){
            case 'image':

                pe_tv_columns.push(
                        {
                            header: typeof tv_captions_arr[i] != 'undefined' ? tv_captions_arr[i] : _('name')
                            ,width: 200
                            ,dataIndex: 'field'+(i+1)
                            ,renderer: SHK.grid.imageRenderer
                            ,editor: {
                                xtype: 'shk-panel-grid-image'
                                ,id: uniqueid
                                ,tv: targetId
                                ,cls: 'shk-panel-grid-image'
                                ,enableKeyEvents: true
                                ,allowBlank: true
                                ,submitValue: false
                                ,width: 70
                                ,source: tvSource
                                ,source_base_url: tvSourceBaseUrl
                            }
                        }
                );

                break;
            default:

                pe_tv_columns.push(
                        {
                            header: typeof tv_captions_arr[i] != 'undefined' ? tv_captions_arr[i] : _('name')
                            ,width: 200
                            ,dataIndex: 'field'+(i+1)
                            ,editor: { xtype: 'textfield' }
                        }
                );

                break;
        }
    }

    var tvUpdateValue = function(e) {
        var data_arr = e.grid.getDataArray('==');
        outString = data_arr.join('||');
        Ext.get(targetId).dom.value = outString;

        e.grid.reload();
    };

    grid = MODx.load({
        xtype: 'shk-grid-local'
        ,id: 'pe_grid' + targetId
        ,columns: pe_tv_columns
        ,fields: tv_fieldnames
        ,default_value: defaultval
        ,store: {
            xtype: 'arraystore'
            ,idIndex: 0
            ,fields: tv_fieldnames
            ,data: tv_data
            ,source: tvSource
            ,source_base_url: tvSourceBaseUrl
        }
        ,listeners: {
            'afteredit': function(e){
                tvUpdateValue(e);
                MODx.fireResourceFormChange();
            }
        }
        ,removeCallback: function( grid ){
            var e = { 'grid': grid };
            tvUpdateValue(e);
            MODx.fireResourceFormChange();
        }
        ,renderTo: containerId
    });

})( 'pe_tv{$resource_tv_id}', 'pe{$resource_tv_id}', '{$params.input_captions|default:'Name,Price'}', '{$tv->get('value')}', {$params.input_count|default:'2'}, '{$params.input_ftype|default:'text'}', '{$params.input_defaultval|default:''}', {$tv_source}, '{$tv_source_base_url}' );

</script>