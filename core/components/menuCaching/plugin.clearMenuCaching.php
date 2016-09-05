<?php
/*

clearMenuCaching

OnSiteRefresh

*/

$paths = array('menuCaching/');
$options = array('objects' => null, 'extensions' => array('.php', '.log'));
$modx->cacheManager->clearCache($paths, $options);

return 'clearMenuCaching: cleared.';