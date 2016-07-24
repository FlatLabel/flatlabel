<!DOCTYPE html>

<html lang="en">

<head>

<?php
echo '<meta charset="utf-8">
<meta name="viewport" content="width=device-width" />
<title>'.ucwords( $c['page'] ).' | '.$c['title'].'</title>
<link rel="stylesheet" type="text/css" href="themes/'.$c['theme'].'/style.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="./scripts/videos.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
$("#wrapper").vids();
});
</script>';
editTags();
?>

</head>

<body>

<div id="wrapper" class="hfeed">

<header id="header">

<div id="site-title"><a href="./"><?php echo $c['title']; ?></a></div>

<nav id="menu">

<label class="toggle" for="toggle">&#9776; Menu</label>
<input id="toggle" class="toggle" type="checkbox" />

<ul class="xoxo">
<?php menu( '<li><a', '</a></li>' ); ?>
</ul>

</nav>

</header>

<main id="content" class="hentry">

<article class="entry-content">

<header>

<h1 class="entry-title"><?php echo $c['page']; ?></h1>

</header>

<?php content( $c['page'],$c['content'] ); ?>

</article>

</main>

<footer id="footer">

<div id="copyright">

<?php echo $c['copyright'] ." | $loginstate"; ?>

</div>

</footer>

<?php if( is_loggedin() ) settings(); ?>

</div>

</body>

</html>