<?php get_header(); ?>

	<?php if (have_posts()) : ?>
<?php if (is_front_page()) : ?>

<?php /* ********************* experimental JCI banner system ******* BEGIN ************************** */ ?>
<script language='JavaScript' type='text/javascript' src='http://uplink.jci-europe.eu/adx.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
   if (!document.phpAds_used) document.phpAds_used = ',';
   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);
   
   document.write ("<" + "script language='JavaScript' type='text/javascript' src='");
   document.write ("http://uplink.jci-europe.eu/adjs.php?n=" + phpAds_random);
   document.write ("&amp;what=zone:1");
   document.write ("&amp;exclude=" + document.phpAds_used);
   if (document.referrer)
      document.write ("&amp;referer=" + escape(document.referrer));
   document.write ("'><" + "/script>");
//-->
</script><noscript><a href='http://uplink.jci-europe.eu/adclick.php?n=a6212f34' target='_blank'><img src='http://uplink.jci-europe.eu/adview.php?what=zone:1&amp;n=a6212f34' border='0' alt=''></a></noscript>
<?php /* ********************* experimental JCI banner system ******* END **************************** */ ?>

<?php endif; ?>

		<?php while (have_posts()) : the_post(); ?>

			<?php 
				if( !$hasWJHotNews && stristr( get_the_category_list(), 'Hot News' ) && $_SERVER['REQUEST_URI'] == '/' ) {
					$hasWJHotNews = true;
					continue; 
				}
			?>
			
			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Link zu %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
			
				<div class="entry clearfix">
					<?php the_content(__('mehr &raquo;', 'kubrick')); ?>
				</div>
					<p class="postmetadata">
						<?php if( !is_page() ) { ?>
							Veröffentlicht am <?php the_time(__('d.m.Y, H:i', 'kubrick')) ?> von <?php the_author() ?><br />
							<?php the_tags(__('Tags:', 'kubrick') . ' ', ', ', '<br />'); ?> <?php printf(__('Beitrag aus dem Bereich %s', 'kubrick'), get_the_category_list(', ')); ?> |
							<?php comments_popup_link(__('Keine Kommentare &#187;', 'kubrick'), __('1 Kommentar &#187;', 'kubrick'), __('% Kommentare &#187;', 'kubrick'), '', __('Kommentieren nicht m�glich', 'kubrick') ); ?>
						<?php } ?>
						<?php edit_post_link(__('Bearbeiten', 'kubrick'), '', ''); ?>
					</p>
			</div>

		<?php endwhile; ?>

		<div class="navigation clearfix">
			<div class="alignleft"><?php next_posts_link(__('&laquo;Ältere Einträge', 'kubrick')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Neuere Einträge &raquo;', 'kubrick')) ?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e('Nicht gefunden', 'kubrick'); ?></h2>
		<p class="center"><?php _e('Der Inhalt den Du suchst wurde nicht gefunden.', 'kubrick'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

<?php get_footer(); ?>