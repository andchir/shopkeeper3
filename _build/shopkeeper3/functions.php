<?php

/**
* @package 
* @subpackage build
*/
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = trim($o);
    return $o;
}

/* Due to a bug in Revo RC-2, lexicon-based properties cannot be done.
* To workaround this until RC-3, auto-translate them to en here.
*/
function adjustProperties($modx,$properties = array(),$lexiconDir = false) {
    $_lang = array();
    if (empty($lexiconDir)) return $_lang;
    include $lexiconDir.'en/properties.inc.php';

    $newProperties = array();
    foreach ($properties as $property) {
        $property['desc'] = $_lang[$property['desc']];
        unset($property['lexicon']);
        $newProperties[] = $property;
    }
    return $newProperties;
}