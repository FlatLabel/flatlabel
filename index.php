<?php
ob_start();
session_start();
$rp = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
$hostname = '//'.$_SERVER['HTTP_HOST'].str_replace( $rp, '', $_SERVER['REQUEST_URI'] );
$c['password'] = 'password';
$c['loggedin'] = false;
$c['page'] = 'home';
$d['page']['home'] = "<p>Thank you for using FlatLabel. — Click Here to Edit</p>";
$d['page']['about'] = "<p>Something about yourself. — Click Here to Edit</p>";
$d['page']['contact'] = "<p>How can visitors contact you? — Click Here to Edit</p>";
$d['new_page']['admin'] = "<p>Click Here to Edit</p>";
$d['new_page']['visitor'] = "<p>404 — Not Found</p>";
$d['default']['content'] = "Click Here to Edit";
$c['theme'] = "default";
$c['menu'] = "Home<br />\nAbout<br />\nContact";
$c['title'] = 'Site Title';
$c['copyright'] = '&copy; '. date( 'Y' ) .' Your Site';
$hook['admin-rich-text'] = "rich.php";
foreach( $c as $key => $val ) {
if( $key == 'content' )continue;
$fval = @file_get_contents( 'pages/'.$key );
$d['default'][$key] = $c[$key];
if( $fval )$c[$key] = $fval;
switch( $key ) {
case 'password':
if( !$fval )$c[$key] = savePassword( $val );
break;
case 'loggedin':
if( isset( $_SESSION['l'] ) and $_SESSION['l']==$c['password'] )$c[$key] = true;
if( isset( $_REQUEST['logout'] ) ) {
session_destroy();
header( 'Location: ./' );
exit;
}
if( isset( $_REQUEST['login'] ) ) {
if( is_loggedin() )header( 'Location: ./' );
loginForm();
}
$loginstate = ( is_loggedin() )? "<a href='./?logout'>Logout</a>": "<a href='./?login'>Login</a>";
break;
case 'page':
if( $rp )$c[$key]=$rp;
$c[$key] = getSlug( $c[$key] );
if( isset( $_REQUEST['login'] ) )continue;
$c['content'] = @file_get_contents( "pages/".$c[$key] );
if( !$c['content'] ) {
if( !isset( $d['page'][$c[$key]] ) ) {
header( 'HTTP/1.1 404 Not Found' );
$c['content'] = ( is_loggedin() )? $d['new_page']['admin']:$c['content'] = $d['new_page']['visitor'];
} else {
$c['content'] = $d['page'][$c[$key]];
}
}
break;
default:
break;
}
}
loadPlugins();
require( "themes/".$c['theme']."/theme.php" );
function loadPlugins() {
global $hook,$c;
$cwd = getcwd();
if( chdir( "./plugins/" ) ) {
$dirs = glob( '*', GLOB_ONLYDIR );
if( is_array( $dirs ) )foreach( $dirs as $dir ) {
require_once( $cwd.'/plugins/'.$dir.'/index.php' );
}
}
chdir( $cwd );
$hook['admin-head'][] = "<script type='text/javascript' src='./scripts/edit-live.php?hook=".$hook['admin-rich-text']."'></script>";
}
function getSlug( $p ) {
$p = strip_tags( $p );
preg_match_all( '/([a-z0-9A-Z-_]+)/', $p, $matches );
$matches = array_map( 'strtolower', $matches[0] );
$slug = implode( '-', $matches );
return $slug;
}
function is_loggedin() {
global $c;
return $c['loggedin'];
}
function editTags() {
global $hook;
if( !is_loggedin() && !isset( $_REQUEST['login'] ) ) return;
foreach( $hook['admin-head'] as $o ) {
echo "\t".$o."\n";
}
}
function content( $id,$content ) {
global $d;
echo ( is_loggedin() )? "<span title='".$d['default']['content']."' id='".$id."' class='edit-text rich-text'>".$content."</span>": $content;
}
function menu( $stags,$etags ) {
global $c,$hostname;
$mlist = explode( '<br />',$c['menu'] );
for( $i=0;$i<count( $mlist );$i++ ) {
$page = getSlug( $mlist[$i] );
if( !$page ) continue;
echo $stags." href='".$hostname.$page."'>".str_replace( '-',' ',$page )." ".$etags." \n";
}
}
function loginForm() {
global $c, $msg;
$msg = '';
if( isset( $_POST['sub'] ) ) login();
$c['content'] = "<form action='' method='post'>
Password <input type='password' name='password' />
<input type='submit' name='login' value='Login' /> $msg
<br />
<br />
If you'd like to change your password, enter your old password above and your new one below.
<br />
New Password <input type='password' name='new' />
<input type='submit' name='login' value='Change' />
<input type='hidden' name='sub' value='sub' />
</div>
</form>";
}
function login() {
global $c, $msg;
if( md5( $_POST['password'] )<>$c['password'] ) {
$msg = "Incorrect Password";
return;
}
if( $_POST['new'] ) {
savePassword( $_POST['new'] );
$msg = 'Password Changed';
return;
}
$_SESSION['l'] = $c['password'];
header( 'Location: ./' );
exit;
}
function savePassword( $p ) {
$file = @fopen( 'pages/password', 'w' );
if( !$file ) {
echo "Error. Check permissions.";
exit;
}
fwrite( $file, md5( $p ) );
fclose( $file );
return md5( $p );
}
function settings() {
global $c,$d;
echo "<div id='settings'><h3>Settings</h3>
<div class='setting'><h4>Theme</h4><span id='theme-select'><select name='theme' onchange='fieldSave( \"theme\",this.value );'>";
if( chdir( "./themes/" ) ) {
$dirs = glob( '*', GLOB_ONLYDIR );
foreach( $dirs as $val ) {
$select = ( $val == $c['theme'] )? ' selected' : ''; 
echo '<option value="'.$val.'"'.$select.'>'.$val."</option>\n";
}
}
echo "</select></span></div>
<div class='setting'><h4>Menu</h4><span id='menu' title='Home' class='edit-text'>".$c['menu']."</span></div>";
foreach( array( 'title', 'copyright' ) as $key ) {
echo "<div class='setting'><h4>Text</h4><span title='".$d['default'][$key]."' id='".$key."' class='edit-text'>".$c[$key]."</span></div>";
}
echo "<div class='setting'><a href='javascript:location.reload(true);' class='button'>Update</a></div></div>";
}
ob_end_flush();
?>