<?php

/*

Статистика заказов Shopkeeper3

*/

ob_start();

?>

<link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/c3.css" rel="stylesheet" type="text/css">
<script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/d3/d3.min.js"></script>
<script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/c3.min.js"></script>
<!--script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/angular-spinner/spin.min.js"></script-->

<table width="100%">
    <col width="130">
    <col width="*">
    <tr>
        <td>
            <img src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/img/shk_widget_icon2.png" alt="" />
        </td>
        <td>
            <div style="width:100%;">
                <div id="shk_stat" style="position: relative;"></div>
            </div>
        </td>
    </tr>
</table>

<script type="text/javascript">
    
    //var spin_opts = { radius:30, width:8, length: 16, color: '#000' };
    //var spinner = new Spinner( spin_opts ).spin( document.getElementById('shk_stat') );
    
    var shk_lang = {
        month1: '<?php echo $modx->lexicon('shk3.month1'); ?>',
        month2: '<?php echo $modx->lexicon('shk3.month2'); ?>',
        month3: '<?php echo $modx->lexicon('shk3.month3'); ?>',
        month4: '<?php echo $modx->lexicon('shk3.month4'); ?>',
        month5: '<?php echo $modx->lexicon('shk3.month5'); ?>',
        month6: '<?php echo $modx->lexicon('shk3.month6'); ?>',
        month7: '<?php echo $modx->lexicon('shk3.month7'); ?>',
        month8: '<?php echo $modx->lexicon('shk3.month8'); ?>',
        month9: '<?php echo $modx->lexicon('shk3.month9'); ?>',
        month10: '<?php echo $modx->lexicon('shk3.month10'); ?>',
        month11: '<?php echo $modx->lexicon('shk3.month11'); ?>',
        month12: '<?php echo $modx->lexicon('shk3.month12'); ?>'
    };
    
    var post_data = {
        action: 'mgr/getStat',
        HTTP_MODAUTH: '<?php echo $modx->user->getUserToken($modx->context->get('key')); ?>',
        filters: { date: [ '<?php echo date( "d/m/Y", strtotime("-5 month") ); ?>', '<?php echo date("d/m/Y"); ?>' ] }
    };
    
    post_data.filters = Ext.encode( post_data.filters );
    
    Ext.Ajax.request({
        url: '<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/connector.php',
        method: 'POST',
        params: post_data,
        success: function(response){
            
            if ( response.responseText ) {
                
                var stat_data = Ext.util.JSON.decode( response.responseText );
                
                if ( stat_data.object && stat_data.object.columns.length > 1 ) {
                    
                    var date = new Date();
                    
                    var chart = c3.generate({
                        bindto: '#shk_stat',
                        data: stat_data.object,
                        axis: {
                            y: {
                                label: {
                                    text: '<?php echo $modx->lexicon('shk3.orders_count'); ?>',
                                    position: 'outer-middle'
                                }
                            },
                            x: {
                                label: {
                                    text: '<?php echo $modx->lexicon('shk3.months'); ?>',
                                    position: 'outer-middle'
                                },
                                type : 'timeseries',
                                tick: {
                                    format: function (x) {
                                        return shk_lang[ 'month' + ( x.getMonth() + 1 ) ] + ( date.getFullYear() != x.getFullYear() ? ' ' + x.getFullYear() : '' );
                                    }
                                }
                            }
                        },
                        zoom: {
                            enabled: false
                        }
                    });
                    
                }
                
            }
            
            //spinner.stop();
            
        }
    });
</script>

<?php

$content = ob_get_contents();
ob_end_clean();

return $content;
