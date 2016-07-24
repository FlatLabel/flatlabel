<?php
session_start();
$fieldname = $_REQUEST['fieldname'];
$encrypt_pass = @file_get_contents( 'pages/password' );
if( $_SESSION['l']!=$encrypt_pass ) {
header( 'HTTP/1.1 401 Unauthorized' );
exit;
}
$content = trim( rtrim( stripslashes( $_REQUEST['content'] ) ) );
$file = @fopen( "pages/$fieldname", "w" );
if( !$file ) {
echo "Failed. Check permissions.";
exit;
}
fwrite( $file, $content );
fclose( $file );
echo $content;
?>