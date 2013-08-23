<?php
require('../../../wp-blog-header.php');
$id = isset( $_GET['getTerminAsICal'] ) ? $_GET['getTerminAsICal'] : null;

if( $_GET['source'] ) echo wjdTermine::getTermineAsiCal( $id );
else wjdTermine::sendiCal( $id );
?>