
/* xtype: shk-grid-local */
SHK.grid.ArrayGrid = function(config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.applyIf(config,{
        id: 'shk_grid'+this.ident
        ,autoHeight: true
        ,data: []
        ,columns: []
        ,fields: []
        ,bbar: new Ext.Toolbar({
            items: [
                {
                    xtype: 'button'
                    ,iconCls: 'add-icn'
                    ,width: '60'
                    ,type: 'button'
                    ,style: 'margin: 4px 6px 0 0;'
                    ,handler: function(b,e){
                        
                        var fp = this.ownerCt.ownerCt;
                        var store = fp.getStore();
                        var data = this.ownerCt.ownerCt.getDataArray();
                        
                        data.push(['',config.default_value]);
                        store.loadData(data);
                        
                        fp.addCallback(fp);
                        
                    }
                },
                {
                    xtype: 'button'
                    ,iconCls: 'del-icn'
                    ,width: '60'
                    ,type: 'button'
                    ,style: 'margin: 4px 6px 0 0;'
                    ,handler: function(b,e){
                        
                        var fp = this.ownerCt.ownerCt;
                        var store = fp.getStore();
                        var data = this.ownerCt.ownerCt.getDataArray();
                        
                        data.pop();
                        store.loadData(data);
                        
                        fp.removeCallback(fp);
                        
                    }
                }
            ]
        })
    });
    SHK.grid.ArrayGrid.superclass.constructor.call(this,config);
}
Ext.extend(SHK.grid.ArrayGrid,MODx.grid.LocalGrid,{
    
    //get data array
    getDataArray: function(separator){
        
        if (typeof(separator)=='undefined') var separator = false;
        var data = [];
        var store = this.getStore();
        var store_data = store.getRange();
        
        for(var i in store_data){
            if(typeof(store_data[i])=='object'){
                var temp_data = [];
                for(var key in store_data[i].data) {
                    temp_data.push(store_data[i].data[key]);
                }
                if (separator!==false) {
                    temp_data = temp_data.join(separator);
                }
                data.push(temp_data);
            }
        }
        
        return data;
        
    }
    
    //reload
    ,reload: function(){
        
        var store = this.getStore();
        var data = this.getDataArray();
        store.loadData(data);
        this.reloadCallback(this);
        
    }

    //addCallback
    ,addCallback: function(grid){ }
    
    //removeCallback
    ,removeCallback: function(grid){ }
    
    //udateCallback
    ,reloadCallback: function(grid){ }
    
});
Ext.reg('shk-grid-local',SHK.grid.ArrayGrid);


/**
 * imageRenderer
 */
SHK.grid.imageRenderer =  function(val, md, rec, row, col, s){
    //console.log(val, md, rec, row, col, s);
    if (val.substr(0,4) == 'http'){
        return '<img class="thumb" style="height:50px" src="' + val + '" alt="'+val+'" />' ;
    }
    if (val != ''){
        return '<img class="thumb" src="'+MODx.config.connectors_url+'system/phpthumb.php?w=50&h=50&zc=1&src='+s.source_base_url+val+'&wctx=mgr" alt="'+val+'" />';
    }
    return val;
};



/**
 * 
 *
 * @class SHK.panel.GridImage
 * @extends MODx.panel.ImageTV
 * @param {Object} config An object of options.
 * @xtype shk-panel-tv-image
 */
if (typeof(MODx.panel.ImageTV) != 'undefined') {

SHK.panel.GridImage = function(config) {
    config = config || {};
    config.filemanager_url = MODx.config.filemanager_url;
    Ext.applyIf(config,{
        reset: function(){}
        ,setValue: function(v){
            this.items.items[0].setValue(v);
            this.items.items[1].setValue(v);
            Ext.select('#'+this.gridEditor.boundEl.id+' img.thumb').setStyle({'visibility':'hidden'});
        }
        ,getValue: function(){
            Ext.select('#'+this.gridEditor.boundEl.id+' img.thumb').setStyle({'visibility':'visible'});
            return this.gridEditor.record.json[0];
        }
        ,isValid: function(){ return true; }
        ,listeners: {
            'select': {fn:function(data) {
                
                this.gridEditor.record.json[0] = data.relativeUrl;
                this.gridEditor.record.data.field1 = data.relativeUrl;
                
                var grid = Ext.getCmp('pe_grid'+this.config.tv);
                grid.reload();
                grid.fireEvent('afteredit',{
                    'grid': grid
                });
                
            },scope:this}
            ,'change': {fn:function(cb,nv) {
                //console.log('change',this,cb,nv);
                /*
                this.fireEvent('select',{
                    relativeUrl: nv
                    ,url: nv
                });
                */
            },scope:this}
        }
    });
    SHK.panel.GridImage.superclass.constructor.call(this,config);
};
Ext.extend(SHK.panel.GridImage,MODx.panel.ImageTV);
Ext.reg('shk-panel-grid-image',SHK.panel.GridImage);

}

/* xtype: shk-color-button */
SHK.panel.ColorButton = function(config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.applyIf(config,{
        id: Ext.id()
        ,iconCls: 'color-icn'
        ,width: '60'
        ,menu : {items: [
            new Ext.ColorPalette({
                value: ''
                ,index: ''
                ,listeners: {
                    select: function(cp, color){
                        console.log(this.index,color);
                    }
                }
            })
        ]}
    });
    SHK.panel.ColorButton.superclass.constructor.call(this,config);
}
Ext.extend(SHK.panel.ColorButton,Ext.Button,{
    
});
Ext.reg('shk-color-button',SHK.panel.ColorButton);

