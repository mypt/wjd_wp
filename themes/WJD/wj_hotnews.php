<?php 
if( $_SERVER['REQUEST_URI'] == '/' ){
	$my_query = new WP_Query('category_name=hotnews&showposts=1'); 
	while ($my_query->have_posts()) : $my_query->the_post();
	?>
		<div id="hotnews">
			<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
			<div class="entry">
				<?php 
				the_content( 'mehr &raquo;' );
				?>
				
			</div>
		</div>
	<?php endwhile; ?>
<?php } ?>