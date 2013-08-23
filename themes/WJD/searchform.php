<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
<label class="hidden" for="s"><?php _e('Suche nach:', 'kubrick'); ?></label>
<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="<?php _e('Suchen', 'kubrick'); ?>" />
</div>
</form>
