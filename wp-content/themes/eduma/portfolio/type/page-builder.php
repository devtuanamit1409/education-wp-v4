<?php
/**
 * @package thimpress
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('portfolio-format-page-builder'); ?>>
	<div class="entry-content-portfolio">
		<?php the_content(); ?>
	</div>
    <?php thim_related_portfolio( get_the_ID() ); ?>
</article>