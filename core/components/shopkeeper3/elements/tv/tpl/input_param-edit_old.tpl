<!--input id="pe{$resource_tv_id}" name="tv{$tv->id}" class="param-edit" value="{$tv->get('value')|escape}" style="display:none;" /-->
<textarea id="pe{$resource_tv_id}" name="tv{$tv->get('id')}" class="param-edit" style="display:none;">{$tv->get('value')|escape}</textarea>

<!--div id="pe_textarea{$resource_tv_id}"></div-->
<div id="pe_tv{$resource_tv_id}"></div>
<div style="clear:both;"></div>
<div id="pe_buttons{$resource_tv_id}"></div>
<div style="clear:both;"></div>

<script type="text/javascript">

var tv{$resource_tv_id}_val = '{$tv->get('value')}';//|escape
var tv{$resource_tv_id}_val_arr = tv{$resource_tv_id}_val.split('||');
var pe_fields_count{$resource_tv_id} = {$params.input_count};
var pe_fields_defaultval{$resource_tv_id} = '{$params.input_defaultval}';
var pe_rowscount{$resource_tv_id} = 0;
var pe_addcols{$resource_tv_id} = '{$params.input_addcols}'==false || _('no')=='{$params.input_addcols}' ? false : true;

{literal}

MODx.panel.ImageTV_pe = Ext.extend(MODx.panel.ImageTV, {});
Ext.reg('modx-panel-tv-image-pe',MODx.panel.ImageTV_pe);

//Формирует сводную строку со всеми данными полей
function pe_setParamString{/literal}{$resource_tv_id}{literal}(){
    var fieldsArr = Ext.get(Ext.query("#pe_tv{/literal}{$resource_tv_id}{literal} textarea.x-form-field, #pe_tv{/literal}{$resource_tv_id}{literal} input.x-form-text")).elements;
    var outString = '';
    for (var i=0;i<fieldsArr.length;i++){
        outString += fieldsArr[i].value;
        if((i+1)%pe_fields_count{/literal}{$resource_tv_id}{literal}==0){
          outString += '||';
        }else{
          outString += '==';
        }
    }
    
    if(outString)
        Ext.get('pe{/literal}{$resource_tv_id}{literal}').dom.value = outString.slice(0,-2); //.set({text:outString.slice(0,-2)});
    else
        Ext.get('pe{/literal}{$resource_tv_id}{literal}').dom.value = '';//.set({text:''});
    
    MODx.fireResourceFormChange();
}

//Добавление строки с полями
function pe_addFieldsRow{/literal}{$resource_tv_id}{literal}(index,pe_val_arr){
    
    if(typeof(pe_val_arr)=='undefined') var pe_val_arr = [];
    if(pe_val_arr.length>pe_fields_count{/literal}{$resource_tv_id}{literal}) pe_fields_count{/literal}{$resource_tv_id}{literal} = pe_val_arr.length;
    
    var pe_items = [];
    for(var i=0;i<pe_fields_count{/literal}{$resource_tv_id}{literal};i++){
        var uniqueid = Ext.id();
        var field_type = i==0 ? '{/literal}{$params.input_ftype}{literal}' : 'text';
        switch(field_type){
            case 'image':
                pe_items.push(
                    {
                        xtype: 'modx-panel-tv-image-pe'
                        ,id: uniqueid
                        ,tv: '{/literal}{$tv->get('id')}{literal}_'+uniqueid
                        ,enableKeyEvents: true
                        ,allowBlank: true
                        ,submitValue: false
                        ,width: 300
                        ,relativeValue: typeof(pe_val_arr[i])!='undefined' ? pe_val_arr[i] : (i==1 ? pe_fields_defaultval{/literal}{$resource_tv_id}{literal} : '')
                        ,style: 'float:left; margin-right:5px;'
                        ,source: {/literal}{$tv_source}{literal}
                        ,msgTarget: 'under'
                        ,hideSourceCombo: true
                        ,listeners: {
                            'select': pe_setParamString{/literal}{$resource_tv_id}{literal}
                            ,'change': {fn:function(cb,nv) {},scope:this}
                        }
                    }
                );
            break;
            default:
                pe_items.push(
                    {
                        xtype: 'textarea'
                        ,id: uniqueid
                        ,tv: '{/literal}{$tv->get('id')}{literal}_'+uniqueid
                        ,enableKeyEvents: true
                        ,allowBlank: true
                        ,submitValue: false
                        //,'class': 'x-form-text'
                        //,width: {/literal}{$params.input_width}{literal}
                        ,value: typeof(pe_val_arr[i])!='undefined' ? pe_val_arr[i] : (i==1 ? pe_fields_defaultval{/literal}{$resource_tv_id}{literal} : '')
                        ,style: 'width: {/literal}{$params.input_width}{literal}px; height:{/literal}{$params.input_height}{literal}px; padding: 5px; float:left; margin-right:5px;'
                        ,listeners: {
                            'keyup': pe_setParamString{/literal}{$resource_tv_id}{literal}
                            ,'focus': function(o){
                                Ext.select("#pe_tv{/literal}{$resource_tv_id}{literal} div.pe_row")
                                .removeClass('active')
                                .setStyle({'background-color':'#fff'});
                                o.el.findParent('div',2,true)
                                .addClass('active')
                                .setStyle({'background-color':'#eee'});
                            }
                        }
                    }
                );
            break;
        }
    }
    
    var pe_new_row = new Ext.Container({
        fieldLabel: ''
        //,renderTo: 'pe_tv{$resource_tv_id}'
        ,id: 'pe_tv{/literal}{$resource_tv_id}{literal}-cmp'+pe_rowscount{/literal}{$resource_tv_id}{literal}
        //,monitorResize: true
        ,cls: 'pe_row'
        ,layout: 'fit'
        ,autoEl: 'div'
        ,style: {'width':'100%', 'padding':'4px', 'clear':'both', 'position':'relative'}
        ,items: pe_items
        
    });
    
    //Определяем позицию и вставляем контейнер
    var pe_active_row = Ext.select("#pe_tv{/literal}{$resource_tv_id}{literal} div.pe_row.active");
    var prev_all_count = 0;
    if(pe_active_row.elements.length>0){
        var node = pe_active_row.elements[0];
        while( node && node.nodeType === 1 && node !== this ) {
            node = node.previousElementSibling || node.previousSibling;
            prev_all_count++;
        }
    }
    var pe_new_pos = pe_active_row.elements.length>0 ? prev_all_count : pe_rowscount{/literal}{$resource_tv_id}{literal};
    pe_new_row.render(pe_tv{/literal}{$resource_tv_id}{literal},pe_new_pos);
    
    pe_new_row.el.insertHtml('beforeEnd','<br clear="all">');
    
    pe_rowscount{/literal}{$resource_tv_id}{literal}++;
    
}

//Добавление или удаление колонки с полями
function pe_chengeColCount{/literal}{$resource_tv_id}{literal}(count){
    
    var rows = Ext.get(Ext.query("#pe_tv{/literal}{$resource_tv_id}{literal} div.pe_row")).elements;
    
    for(var i=0;i<rows.length;i++){
        
        var fields = Ext.get(Ext.query("textarea.x-form-field",rows[i])).elements;
        var new_id = Ext.id();
        
        //добавляем поля
        if(count>0){
            
            Ext.DomHelper.insertAfter(
                Ext.DomQuery.selectNode('#'+fields[fields.length-1].id),
                {  
                    tag: 'textarea'
                    ,id: new_id
                    ,type: 'text'
                    //,name: 'pe_field[]'
                    ,submitValue: false
                    ,enableKeyEvents: true
                    ,allowBlank: true
                    ,'class': 'x-form-text x-form-field x-column'
                    ,style: 'width: {/literal}{$params.input_width}{literal}px; height:18px; padding: 5px; float:left; margin-right:5px;'
                },
                false
            );
            
            Ext.get(new_id).on('keyup', pe_setParamString{/literal}{$resource_tv_id}{literal});
        
        //удаляем поля в последней колонке
        }else if(fields.length>1){
            Ext.get(fields[fields.length-1]).remove();
        }else{
            return;
        }
        
    }
    
    pe_fields_count{/literal}{$resource_tv_id}{literal} += count;
    
    pe_setParamString{/literal}{$resource_tv_id}{literal}();
    
}


function pe_onReady{/literal}{$resource_tv_id}{literal}(){
    
    //new Ext.form.TextArea({
    //    name: 'tv{/literal}{$tv->id}{literal}'
    //    ,id: 'pe{/literal}{$resource_tv_id}{literal}'
    //    ,renderTo: 'pe_textarea{/literal}{$resource_tv_id}{literal}'
    //});
    
    for (var i=0;i<tv{/literal}{$resource_tv_id}{literal}_val_arr.length;i++){
        {/literal}
        pe_addFieldsRow{$resource_tv_id}(i,tv{$resource_tv_id}_val_arr[i].split('=='));
        {literal}
    }
    
    var paramEditButton{/literal}{$resource_tv_id}{literal} = new Ext.Container({
        fieldLabel: ''
        ,id: 'pe_button{/literal}{$resource_tv_id}{literal}-cmp'
        ,style: {padding: '4px 0'}
        ,items: []
    })
    
    //кнопка добавления строки с полями
    paramEditButton{/literal}{$resource_tv_id}{literal}.add({
        xtype: 'button'
        ,width: 30
        ,type: 'button'
        ,text: '+'
        ,handler: function(b,e){
            var fieldsCount = Ext.get(Ext.query("#pe_tv{/literal}{$resource_tv_id}{literal} textarea.x-form-field")).elements.length;
            {/literal}pe_addFieldsRow{$resource_tv_id}((fieldsCount / pe_fields_count{$resource_tv_id}));{literal}
        }
        ,style: {'float':'left', 'margin-right':'5px'}
    });
    
    //Кнопка удаления строки с полями
    paramEditButton{/literal}{$resource_tv_id}{literal}.add({
        xtype: 'button'
        ,width: 30
        ,type: 'button'
        ,text: '-'
        ,handler: function(b,e){
            var pe_row = Ext.select("#pe_tv{/literal}{$resource_tv_id}{literal} div.pe_row.active");
            if(pe_row.elements.length==0) pe_row = Ext.select("#pe_tv{/literal}{$resource_tv_id}{literal} div.pe_row:last");
            if(pe_row.elements.length>0){
                pe_row.remove();
                pe_setParamString{/literal}{$resource_tv_id}{literal}();
            }
        }
        ,style: {'float':'left', 'margin-right':'5px'}
    });
    
    if(pe_addcols{/literal}{$resource_tv_id}{literal}){
        //кнопка удаления колонки с полями
        paramEditButton{/literal}{$resource_tv_id}{literal}.add({
            xtype: 'button'
            ,width: 30
            ,type: 'button'
            ,text: '<'
            ,handler: function(b,e){
                pe_chengeColCount{/literal}{$resource_tv_id}{literal}(-1);
            }
            ,style: {'float':'left', 'margin-right':'5px'}
        });
        //кнопка добавления колонки с полями
        paramEditButton{/literal}{$resource_tv_id}{literal}.add({
            xtype: 'button'
            ,width: 30
            ,type: 'button'
            ,text: '>'
            ,handler: function(b,e){
                pe_chengeColCount{/literal}{$resource_tv_id}{literal}(1);
            }
            ,style: {'float':'left', 'margin-right':'5px'}
        });
    }
    
    paramEditButton{/literal}{$resource_tv_id}{literal}
    .render(Ext.get('pe_buttons{/literal}{$resource_tv_id}{literal}'));
    
}

Ext.onReady(pe_onReady{/literal}{$resource_tv_id}{literal});

{/literal}

</script>