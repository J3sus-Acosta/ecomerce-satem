<?php
/**
 * SATEM Child Theme WooCommerce Wrapper
 *
 * @package SatemChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="satem-container" style="padding-top: 32px; padding-bottom: 64px;">
	<?php if ( is_shop() || is_product_category() || is_product_tag() ) : ?>
		<div class="satem-shop-header">
			<div>
				<h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: var(--satem-text-heading);">
					<?php woocommerce_page_title(); ?>
				</h1>
				<p style="color: var(--satem-text-muted); margin-top: 4px; margin-bottom: 0;">
					<?php esc_html_e( 'Browse commercial product lines for retail B2C and wholesale B2B buyers.', 'satem-child' ); ?>
				</p>
			</div>

			<form role="search" method="get" class="satem-shop-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search products...', 'satem-child' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
				<input type="hidden" name="post_type" value="product" />
				<button type="submit" class="satem-btn satem-btn-primary" style="padding: 10px 18px; font-size: 0.9rem;"><?php esc_html_e( 'Search', 'satem-child' ); ?></button>
			</form>
		</div>
	<?php endif; ?>

	<div class="satem-woocommerce-content-wrapper">
		<?php woocommerce_content(); ?>
	</div>
</div>

<?php
get_footer();
