		</div>
		<div id="subnavi">

			<?php $conf = wjdConfig::loadConfig(); ?>
			
			<?php get_sidebar(); ?>
			
			<h2>Kontakt</h2>
			<h3><?php echo htmlspecialchars( $conf->name ); ?></h3>
			<p>
				<?php echo nl2br( htmlspecialchars( $conf->adresse ) ); ?>
			</p>
			
			<h2>Tags und Kategorien</h2>
			<?php if( function_exists( 'wp_cumulus_insert' ) ){ wp_cumulus_insert(); } ?>
			
		</div>
	</div>
	<div id="context">
	
		<?php 
			include 'wp-content/themes/WJD/wj_hotnews.php';
		       /*
			* $customContentFile = dirname( __FILE__ ) . '/custom.inc.html';
			* if( file_exists( $customContentFile ) ){
			* 	include( $customContentFile );
			* }
			*/
		?>
<!-- Experimental Conference Banner System --------BEGIN--------- -->
<h2>Konferenzen</h2><p>
<?php    require('lib-xmlrpc-class.inc.php');    $xmlrpcbanner = new phpAds_XmlRpc('uplink.jci-europe.eu', '');    $xmlrpcbanner->view('zone:4', 0, '_blank', '', '0');?></p>
<!-- Experimental Conference Banner System ---------END---------- -->
		
		<?php echo wjdTermine::getNextTermineAsWidget(); ?>
		
		<?php if( $_SERVER['REQUEST_URI'] == '/' ){ ?>
			<h2>Partner</h2>
			<p>
			<?php
				foreach( $conf->convert( $conf->partner ) as $k=>$partner ){
					echo '<a href="'.htmlspecialchars( $partner[1] ).'" target="_blank">';
					echo htmlspecialchars( $partner[0] );
					echo '</a><br />';
				}
			?>
			</p>
		<?php } ?>

	</div><?php // id="kontext" ?>
</div><?php // id="middle" ?>


<img src="/wp-content/themes/WJD/images/trenner.png" alt="" /><br />
<div id="partner">
	<?php 
		$conf = wjdConfig::loadConfig();
		foreach( $conf->convert( $conf->fusszeilen_partner ) as $k=>$partner ){
			echo '<a href="'.htmlspecialchars( $partner[1] ).'" target="_blank">';
			echo '<img src="'.htmlspecialchars( $partner[0] ).'" alt="'.htmlspecialchars( $partner[1] ).'" class=""/>';
			echo '</a>';
		}
	?>
</div>
<img src="/wp-content/themes/WJD/images/trenner.png" alt="" />
<div id="loginout"><?php wp_loginout(); ?></div>

		<?php wp_footer(); ?>
		<?php
			$statsFile = dirname( __FILE__ ).'/stats.inc.php';
			if( file_exists( $statsFile ) ){
				include( $statsFile );
			}
		?>

</div><?php // id="outer" ?>

<div id="badge"><a href="/jetzt-helfen/"><img src="/wp-content/themes/WJD/images/badge.png" alt="" class=""/></a></div>

</body>
</html>