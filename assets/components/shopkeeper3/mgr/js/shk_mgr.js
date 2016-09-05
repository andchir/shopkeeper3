var SHK = function(config) {
    config = config || {};
    SHK.superclass.constructor.call(this,config);
};
Ext.extend(SHK,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},tab:{},config: {}
});
Ext.reg('shk',SHK);

SHK = new SHK();