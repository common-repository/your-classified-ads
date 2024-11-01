<?php
/**
 * The loop that displays posts
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */

?>
<div id="yclads-dir-list" class="yclads item-list">
	<ul>
	<?php /* If there are no posts to display, such as an empty archive page  */ ?>
	<?php if ( ! Yclads_Widget_Custom_Query::WigetPosts()->have_posts() ) : ?>
		<li id="post-0" class="post error404 not-found">

			<div class="entry-content">
				<p><?php _e( 'No classified ads found.', 'yclads' ); ?></p>

			</div><!-- .entry-content -->
		</li><!-- #post-0 -->
	<?php endif; ?>

	<?php /* Start the Loop  */ ?>
	<?php while ( Yclads_Widget_Custom_Query::WigetPosts()->have_posts() ) : Yclads_Widget_Custom_Query::WigetPosts()->the_post(); ?>
	<?php /* How to display all other posts  */ ?>

			<li id="post-<?php the_ID(); ?>" <?php post_class('widget'); ?>>
				<span class="entry-headline">
					<span class="entry-title item-title">
						<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
					</span>
					<span class="entry-date">
						<a href="<?php the_permalink(); ?>" title="<?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?>" rel="bookmark"></a>
					</span>
				</span>
				<span class="entry-content item">
					<?php echo yclads_limit_words(get_the_excerpt(), '25'); ?>
				</span>

				<div>
					<?php yclad_breadcrumb();?>
					<div class="entry-info item-meta">
						<span class="entry-icons">
							<span class="comments-link">
								<?php comments_popup_link(__('<img src="'.yclads_get_theme_file_url('comment.png','_inc/images').'"> 0'), __('<img src="'.yclads_get_theme_file_url('comment.png','_inc/images').'"> 1'), __('<img src="'.yclads_get_theme_file_url('comments.png','_inc/images').'">  10'));?>
							</span>
							<?php if (oqp_get_ad_visit_count()) {?>
							<span class="views">
								<?php _e('Views','yclads');?> :
								<?php printf(__( '%d views', 'yclads' ),oqp_get_ad_visit_count()); ?>								
							</span>
							<?php } ?>
							<?php do_action('oqp_post_entry_icons');?>
						</span>
					</div>
				</div>

				<div class="clear"></div>
			</li><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>
	</ul>
</div>
