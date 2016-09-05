<div id="tv-wprops-form{$tv}"></div>
{literal}
<script type="text/javascript">
// <![CDATA[
var params = {
{/literal}{foreach from=$params key=k item=v name='p'}
 '{$k}': '{$v}'{if NOT $smarty.foreach.p.last},{/if}
{/foreach}{literal}
};
var oc = {'change':{fn:function(){Ext.getCmp('modx-panel-tv').markDirty();},scope:this}};
MODx.load({
    xtype: 'panel'
    ,layout: 'form'
    ,autoHeight: true
    ,labelWidth: 150
    ,border: false
    ,items: [{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_function}{literal}'
        ,name: 'prop_function'
        ,id: 'prop_function{/literal}{$tv}{literal}'
        ,value: params['function'] || 'SHK.additOpt(this)'
        ,width: 300
        ,listeners: oc
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_cssclass}{literal}'
        ,name: 'prop_cssclass'
        ,id: 'prop_cssclass{/literal}{$tv}{literal}'
        ,value: params['cssclass'] || 'shk_param'
        ,width: 300
        ,listeners: oc
    },{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_wraptag}{literal}'
        ,name: 'prop_wraptag'
        ,id: 'prop_wraptag{/literal}{$tv}{literal}'
        ,value: params['wraptag'] || 'div'
        ,width: 300
        ,listeners: oc
    }/*,{
        xtype: 'textfield'
        ,fieldLabel: '{/literal}{$_lang.shk_id}{literal}'
        ,name: 'prop_id'
        ,id: 'prop_id{/literal}{$tv}{literal}'
        ,value: params['id'] || '[[+id]]'
        ,width: 300
        ,listeners: oc
    }*/,{
        xtype: 'combo'
        ,fieldLabel: '{/literal}{$_lang.shk_first_selected}{literal}'
        ,store: new Ext.data.SimpleStore({
            fields: ['v','d']
            ,data: [[1,_('yes')],[0,_('no')]]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,name: 'prop_first_selected'
        ,hiddenName: 'prop_first_selected'
        ,id: 'prop_first_selected{/literal}{$tv}{literal}'
        ,editable: false
        ,forceSelection: true
        ,typeAhead: false
        ,triggerAction: 'all'
        ,value: params['first_selected'] || 1
        ,listeners: oc
    },{
        xtype: 'textfield'//'hidden'
        ,fieldLabel: '{/literal}{$_lang.shk_param_name}{literal}'
        ,name: 'prop_param_name'
        ,id: 'shk_param_name{/literal}{$tv}{literal}'
        ,value: '{/literal}{$tv_name}{literal}'
        ,width: 300
        ,listeners: oc
    }]
    ,renderTo: 'tv-wprops-form{/literal}{$tv}{literal}'
});
// ]]>
</script>
{/literal}