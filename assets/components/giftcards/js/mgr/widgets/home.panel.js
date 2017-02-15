
giftCards.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('giftcards')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: this.getItems()
        }]
    });
    giftCards.panel.Home.superclass.constructor.call(this,config);
};

Ext.extend(giftCards.panel.Home, MODx.Panel, {
    
    getItems: function(){
        
        var output = [];
        
        if(MODx.config['giftcards.giftcards_on'] != 0){
        
            output.push({
                title: _('giftcards.items')
                ,items: [{
                    html: '<p>'+_('giftcards.intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'giftcards-grid-groups'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            });
        
        }
        
        if(MODx.config['giftcards.discounts_on'] != 0){
        
            output.push({
                title: _('giftcards.discounts')
                ,items: [{
                    html: '<p>'+_('giftcards.discount_intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'giftcards-grid-discounts'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            });
        
        }
        
        return output;
        
    }
    
});

Ext.reg('giftcards-panel-home',giftCards.panel.Home);
