<?php
/**
 * SATEM Child Theme Standard Page Template
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="satem-container" style="padding-top: 40px; padding-bottom: 60px;">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( ! is_front_page() && ! is_page_template( 'template-wholesale.php' ) ) : ?>
				<header class="entry-header" style="margin-bottom: 24px;">
					<h1 class="entry-title" style="font-size: 2.2rem; font-weight: 800; color: var(--satem-text-heading);"><?php the_title(); ?></h1>
				</header>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</div>

<?php
get_footer();
