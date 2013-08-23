<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head> 
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', 'kubrick'), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php printf(__('%s Atom Feed', 'kubrick'), get_bloginfo('name')); ?>" href="<?php bloginfo('atom_url'); ?>" /> 
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>

<!--[if lt IE 7]>
<style type="text/css">@import url(/wp-content/themes/WJD/IE6.css);></style>
<![endif]-->
</head>
<body>
<div id="outer">
	<div id="header" class="clearfix">
		<?php $conf = wjdConfig::loadConfig(); ?>
		<div id="logo"><a href="/"><img src="<?php echo htmlspecialchars( $conf->logo ); ?>" alt="<?php echo htmlspecialchars( $conf->name ); ?>" class=""/></a></div>
		<ul>
			<li><a href="/kontakt/">Kontakt</a></li>
			<li><a href="/impressum/">Impressum</a></li>
			<li><a href="/feed/">RSS</a></li>
		</ul>
		<div id="claim"><img src="/wp-content/themes/WJD/images/slogan.png" alt="" class=""/></div>
	</div>
	<ul id="navi" class="clearfix">
		<?php  
			if( file_exists( 'navi.php' ) ) {
				include 'wp-content/themes/WJD/wj_hotnews.php'; 
			}else{
		?>
				<li class="gruen"><a href="/uber-uns/">&Uuml;ber uns<br /><span>Wer wir sind</span></a></li>
				<li class="blau"><a href="/">News<br /><span>Alles auf einen Blick</span></a></li>
				<li class="gelb"><a href="/newsletter/">Newsletter<br /><span>Alle Infos per Mail</span></a></li>
				<li class="rot"><a href="/termine/">Termine<br /><span>Nichts mehr verpassen</span></a></li>
				<li class="schwarz"><a href="/downloads/">Downloads<br /><span>Zum Runterladen</span></a></li>
				<li class="search"><form action="/" method="get"><input type="text" name="s" id="s" /><input type="submit" value="SUCHEN" /></form></li>
		<?php } ?> 
	</ul>
	<div id="identifier" style="background: url(<?php echo getWJIdentifierImage(); ?>);">
		&nbsp;
	</div>
	
	<div id="middle" class="clearfix">
		<div id="inner">
			<div id="content">