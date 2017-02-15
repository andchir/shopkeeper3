var giftCards = function(config) {
    config = config || {};
    giftCards.superclass.constructor.call(this,config);
};
Ext.extend(giftCards,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}
});
Ext.reg('giftcards',giftCards);

giftCards = new giftCards();