
/* Таблица групп карт */
giftCards.grid.Groups = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'giftcards-grid-groups'
        ,url: giftCards.config.connector_url
        ,baseParams: {
            action: 'mgr/item/getlistgroups'
        }
        ,fields: ['id','date','nominal','count','valids','expired']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 50
        },{
            header: _('giftcards.date')
            ,sortable: true
            ,dataIndex: 'date'
        },{
            header: _('giftcards.nominal')
            ,sortable: true
            ,dataIndex: 'nominal'
        },{
            header: _('giftcards.count')
            ,dataIndex: 'count'
        },{
            header: _('giftcards.valids')
            ,dataIndex: 'valids'
        },{
            header: _('giftcards.expired')
            ,dataIndex: 'expired'
        }]
        ,tbar: [{
            text: _('giftcards.item_create')
            ,handler: this.createItem
            ,scope: this
        }]
    });
    giftCards.grid.Groups.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.grid.Groups,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('giftcards.cards_view')
            ,handler: this.viewItems
        });
        m.push('-');
        m.push({
            text: _('giftcards.item_remove')
            ,handler: this.removeItem
        });
        this.addContextMenuItem(m);
    }
    
    ,createItem: function(btn,e) {
        if (!this.windows.createItem) {
            this.windows.createItem = MODx.load({
                xtype: 'giftcards-window-item-create'
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createItem.fp.getForm().reset();
        this.windows.createItem.show(e.target);
    }
    
    ,viewItems: function(btn,e) {
        if (!this.menu.record || !this.menu.record.id) return false;
        var r = this.menu.record;
        if (this.windows.viewItems) {
            this.windows.viewItems.destroy();
        }
        this.windows.viewItems = MODx.load({
            xtype: 'giftcards-window-viewitems'
            ,record: r
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });
        this.windows.viewItems.fp.getForm().reset();
        this.windows.viewItems.fp.getForm().setValues(r);
        this.windows.viewItems.show(e.target);
    }
    
    ,removeItem: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('giftcards.item_remove')
            ,text: _('giftcards.item_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }
});
Ext.reg('giftcards-grid-groups',giftCards.grid.Groups);


/* Создание новой группы карт */
giftCards.window.CreateItem = function(config) {
    config = config || {};
    this.ident = config.ident || 'giftcards-mecitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('giftcards.item_create')
        ,id: this.ident
        ,height: 350
        ,autoHeight: false
        ,width: 475
        ,url: giftCards.config.connector_url
        ,action: 'mgr/item/create'
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('giftcards.count')
            ,name: 'count'
            ,id: this.ident+'-count'
            ,anchor: '100%'
            ,allowBlank: false
        },{
            xtype: 'textfield'
            ,fieldLabel: _('giftcards.nominal')
            ,name: 'nominal'
            ,id: this.ident+'-nominal'
            ,anchor: '100%'
            ,allowBlank: false
        }]
    });
    giftCards.window.CreateItem.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.window.CreateItem, MODx.Window);
Ext.reg('giftcards-window-item-create',giftCards.window.CreateItem);


/* Окно со списком карт */
giftCards.window.ViewItems = function(config) {
    config = config || {};
    this.ident = config.ident || 'giftcards-meuitem'+Ext.id();
    //if (!this.menu.record) this.menu.recor = config.record;
    Ext.applyIf(config,{
        title: _('giftcards.view_items')
        ,id: 'items_win' + this.record
        ,layout: 'anchor'
        ,width: 700
        ,autoHeight: true
        ,closeAction: 'close'
        ,autoDestroy: true
        ,url: giftCards.config.connector_url
        ,action: ''
        ,padding: 10
        ,items: [{
                xtype: 'giftcards-grid-items'
                ,id: 'items_grid'+config.record.id
                ,record: config.record
                ,preventRender: true
            }
        ],buttons: [{
            text: config.cancelBtnText || _('close')
            ,scope: this
            ,handler: function() { config.closeAction !== 'close' ? this.hide() : this.close(); }
        }]
    });
    giftCards.window.ViewItems.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.window.ViewItems,MODx.Window);
Ext.reg('giftcards-window-viewitems',giftCards.window.ViewItems);


/* Таблица с кодами карт */
giftCards.grid.Items = function(config) {
    config = config || {};
    this.ident = config.ident || 'giftcards-сitem'+Ext.id();
    Ext.applyIf(config,{
        id: 'items_grid'+config.record.id
        ,url: giftCards.config.connector_url
        ,pageSize: 10
        ,baseParams: {
            action: 'mgr/item/getlistitems'
            ,id: config.record.id
        }
        ,fields: ['id','parent','code','date','orderid','state','state_value']
        ,maxHeight: 400
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,autoDestroy: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,renderer: this.rowRenderer
            ,width: 50
        },{
            header: _('giftcards.code')
            ,dataIndex: 'code'
            ,sortable: true
            ,renderer: this.rowRenderer
            ,width: 150
        },{
            header: _('giftcards.date')
            ,dataIndex: 'date'
            ,sortable: true
            ,renderer: this.rowRenderer
        },{
            header: _('giftcards.state')
            ,dataIndex: 'state'
            ,sortable: true
            ,renderer: this.rowRenderer
        },{
            header: _('giftcards.orderid')
            ,dataIndex: 'orderid'
            ,sortable: true
            ,renderer: this.rowRenderer
            ,width: 60
        }]
    });
    giftCards.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.grid.Items,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        this.addContextMenuItem(m);
    }
    
    ,remove: function(text) {}
    
    ,rowRenderer: function(v,md,rec){
        return rec.data.state_value=='expire' ? '<span class="expire">'+v+'</span>' : v;
    }
    
});
Ext.reg('giftcards-grid-items',giftCards.grid.Items);


/* Таблица с группами и скидками */
giftCards.grid.Discounts = function(config) {
    config = config || {};
    this.ident = config.ident || 'giftcards-сitem'+Ext.id();
    Ext.applyIf(config,{
        id: 'items_grid_discounts'
        ,url: giftCards.config.connector_url
        ,pageSize: 10
        ,baseParams: {
            action: 'mgr/item/getlistdiscounts'
        }
        ,save_action: 'mgr/item/updateFromGrid'
        ,fields: ['id','name','discount','condition']
        ,sortInfo: {field: 'discount', direction: 'DESC'}
        ,sortBy: 'discount'
        ,sortDir: 'DESC'
        ,autoHeight: true
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,autoDestroy: true
        ,columns: [{
            header: _('id')
            ,sortable: true
            ,dataIndex: 'id'
            ,renderer: this.rowRenderer
            ,width: 50
        },{
            header: _('giftcards.group_name')
            ,sortable: true
            ,dataIndex: 'name'
            ,width: 150
        },{
            header: _('giftcards.discount')
            ,sortable: true
            ,dataIndex: 'discount'
            ,editor: {xtype: 'textfield'}
        },{
            header: _('giftcards.summ')
            ,sortable: true
            ,dataIndex: 'condition'
            ,editor: {xtype: 'textfield'}
        }]
    });
    giftCards.grid.Discounts.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.grid.Discounts,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        this.addContextMenuItem(m);
    }
    
    ,remove: function(text) {}
    
    ,rowRenderer: function(v,md,rec){
        return rec.data.state=='expire' ? '<span class="expire">'+v+'</span>' : v;
    }
    
});
Ext.reg('giftcards-grid-discounts',giftCards.grid.Discounts);

