<?php
/**
 * Archive YCLAD Template
 *
 * The archive template is basically a placeholder for archives that don't have a template file. 
 * Ideally, all archives would be handled by a more appropriate template according to the current
 * page context.
 *
 * @package Hybrid
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<div id="content" class="hfeed content">
                   

                <?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
            
		
            
                 <?php 
                 
                    if ( class_exists( 'Oqp_Form' ) ) {
                            oqp_loop($args);
                    }else{
                        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                            <div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

                                    <?php get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'thumbnail' ) ); ?>

                                    <?php do_action( 'yclads_before_entry' ); // hybrid_before_entry ?>

                                    <div class="entry-summary">
                                            <?php the_excerpt(); ?>
                                    </div><!-- .entry-summary -->

                                    <?php do_atomic( 'yclads_after_entry' ); // hybrid_after_entry ?>

                            </div><!-- .hentry -->

                            <?php endwhile; ?>

                        <?php else: ?>

                                <?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

                        <?php endif; 
                    }
                    ?>

		

	</div><!-- .content .hfeed -->

<?php get_footer(); // Loads the footer.php template. ?>