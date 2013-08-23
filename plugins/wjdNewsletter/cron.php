<?php
require('../../../wp-blog-header.php');
$m = isset( $_GET['m'] ) ? $_GET['m'] : date( 'Y-m', strtotime('last month') );

//echo '<a href="?m='.wjdNewsletter::prevMonth( $m ).'">&laquo; Zur&uuml;ck</a> | ';
//echo '<a href="?m='.wjdNewsletter::nextMonth( $m ).'">Weiter &raquo;</a>';

echo wjdNewsletter::getNewsletter( $m );
?>