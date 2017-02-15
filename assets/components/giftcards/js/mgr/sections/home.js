Ext.onReady(function() {
    MODx.load({ xtype: 'giftcards-page-home'});
});

giftCards.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'giftcards-panel-home'
            ,renderTo: 'giftcards-panel-home-div'
        }]
    }); 
    giftCards.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(giftCards.page.Home,MODx.Component);
Ext.reg('giftcards-page-home',giftCards.page.Home);