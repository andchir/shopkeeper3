<!DOCTYPE html>
<html>

<head>
<style type="text/css">
body{background-color:#fff;}
table {width:650px; margin:10px 0; border:1px solid #BCBCBC; border-collapse:collapse;}
table td {padding:5px; border:1px solid #BCBCBC;}
</style>
</head>

<body>

<p><b>[[++site_name]]</b></p>

<p>В интернет-магазине сделан заказ.</p>

<div style="padding:15px 0; margin:15px 0; border-top:3px solid #BCBCBC; border-bottom:3px solid #BCBCBC;">

    <p>Номер заказа: [[+orderID]]</p>
    
    <p>Дата: [[+orderDate]].</p>
    
    [[+orderOutputData]]

</div>

<a href="[[++site_url]]" target="_blank">[[++site_url]]</a>

<br /><br />

</body>
</html>