<div id="tv-input-properties-form{$tv}"></div>

{literal}
<script type="text/javascript">
// <![CDATA[
var params = {
{/literal}{foreach from=$params key=k item=v name='p'}
    '{$k}': '{$v|escape:"javascript"}'{if NOT $smarty.foreach.p.last},{/if}
{/foreach}{literal}
};

var oc = {'change':{fn:function(){Ext.getCmp('modx-panel-tv').markDirty();},scope:this}};
MODx.load({
    xtype: 'panel'
    ,layout: 'form'
    ,cls: 'form-with-labels'
    ,labelAlign: 'top'
    ,autoHeight: true
    ,border: false
    ,items: [{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_pe_fields_count}{literal}'
        ,description: ''
        ,name: 'inopt_input_count'
        ,id: 'input_count{/literal}{$tv}{literal}'
        ,value: params['input_count'] || 2
        ,width: 200
        ,listeners: oc
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_pe_fields_captions}{literal}'
        ,description: ''
        ,name: 'inopt_input_captions'
        ,id: 'input_captions{/literal}{$tv}{literal}'
        ,value: params['input_captions'] || 'Name,Price'
        ,width: 200
        ,listeners: oc
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_pe_fields_width}{literal}'
        ,description: ''
        ,name: 'inopt_input_width'
        ,id: 'input_width{/literal}{$tv}{literal}'
        ,value: params['input_width'] || 150
        ,width: 200
        ,listeners: oc
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_pe_fields_defaultval}{literal}'
        ,description: ''
        ,name: 'inopt_input_defaultval'
        ,id: 'input_defaultval{/literal}{$tv}{literal}'
        ,value: params['input_defaultval'] || ''
        ,width: 200
        ,listeners: oc
    },{
        xtype: 'combo-boolean'
        ,store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('yes'),true],[_('no'),false]]
        })
        ,fieldLabel: '{/literal}{$_lang.shk_pe_add_cols}{literal}'
        ,description: ''
        ,name: 'inopt_input_addcols'
        ,id: 'input_addcoll{/literal}{$tv}{literal}'
        ,value: params['input_addcols'] == 0 || params['input_addcols'] == _('no') ? false : true
        ,width: 200
        ,listeners: oc
    },{
        xtype: 'combo'
        ,mode: 'local'
        ,store: new Ext.data.ArrayStore({
            id: 0
            ,fields: ['d','v']
            ,data: [['text','text'],['image','image']]
        })
        ,valueField: 'd'
        ,displayField: 'v'
        ,typeAhead: true
        ,triggerAction: 'all'
        ,lazyRender: true
        ,fieldLabel: '{/literal}{$_lang.shk_pe_ftype}{literal}'
        ,description: ''
        ,name: 'inopt_input_ftype'
        ,id: 'input_ftype{/literal}{$tv}{literal}'
        ,value: params['input_ftype'] || 'text'
        ,width: 200
        ,listeners: oc
    }]
    ,renderTo: 'tv-input-properties-form{/literal}{$tv}{literal}'
});
// ]]>
</script>
{/literal}