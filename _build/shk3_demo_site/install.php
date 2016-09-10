<?php

/**
 * Shopkeeper Demo site installer
 * @author Andchir <andchir@gmail.com>
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

if( !empty($_GET['import_bd']) ){
    
    include __DIR__ . '/config.core.php';
    include MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
    
    $mysqli = new mysqli($database_server, $database_user, $database_password, $dbase);
    if ($mysqli->connect_errno) {
        echo "Ошибка при подключении к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        exit;
    }
    
    $all_lines = file('mysql_dump.sql', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    
    $query = '';
    
    foreach($all_lines as $q) {
        $q = trim($q);
        if( substr($q, 0, 2) == '--' || empty($q)) {
            continue;
        }
        $query .= $q . ' ';
        if( !preg_match( "/;$/", $q ) ){
            continue;
        }
        if( $query ) {
            $query = trim( $query );
            $mysqli->query( $query ) or die("Ошибка выполнения запроса<br/>\"$query\"<br/>" . $mysqli->error);
            $query = '';
        }
    }
    
    echo 'Готово.';
    
}
else {
    
    $setup_url = str_replace('install.php', 'setup/', $_SERVER['PHP_SELF']);
    $zip = new ZipArchive;
    if ( !file_exists( __DIR__ . '/index.php' ) ) {
        
        if ( !is_writable( __DIR__ ) ) {
            echo 'Папка не доступна для записи.';
            exit;
        }
        
        if ( $zip->open(__DIR__ . '/shk3_simple_site.zip') === true ) {
            $zip->extractTo(__DIR__);
            $zip->close();
            header('Location: ' . $setup_url);
        } else {
            echo 'Ошибка.';
        }
        
    }
    
}

