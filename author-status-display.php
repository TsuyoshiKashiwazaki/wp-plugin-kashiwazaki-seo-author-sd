<?php
/**
 * Plugin Name:  Kashiwazaki SEO Author Schema Display
 * Plugin URI:   https://www.tsuyoshikashiwazaki.jp/
 * Description:  著者カード（顔写真・肩書・SNS 等）を記事上下に自動表示し、Article・NewsArticle・BlogPosting・WebPage＋Role・Person の JSON‑LD を生成、E‑E‑A‑Tとリッチリザルトを一括強化するオールインワン SEO プラグイン。
 * Version:      1.0.1
 * Author:       柏崎剛 (Tsuyoshi Kashiwazaki)
 * Author URI:   https://www.tsuyoshikashiwazaki.jp/
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  kashiwazaki-seo-asd
 * Domain Path:  /languages
 *
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'KSAS_ASD_VERSION', '1.0.1' );
define( 'KSAS_ASD_PATH', plugin_dir_path( __FILE__ ) );
define( 'KSAS_ASD_URL',  plugin_dir_url( __FILE__ ) );
define( 'KSAS_ASD_BASENAME', plugin_basename( __FILE__ ) );

require_once KSAS_ASD_PATH . 'includes/helpers.php';

if ( is_admin() ) {
	require_once KSAS_ASD_PATH . 'includes/settings.php';
	require_once KSAS_ASD_PATH . 'includes/profile-fields.php';
}

require_once KSAS_ASD_PATH . 'includes/frontend.php';

register_activation_hook( __FILE__, function () {
	if ( get_option( 'ksas_post_types', null ) === null ) {
		add_option( 'ksas_post_types', [ 'post' ] );
	}
	if ( get_option( 'ksas_position', null ) === null ) {
		add_option( 'ksas_position', 'top' );
	}
	if ( get_option( 'ksas_schema_mode', null ) === null ) {
		add_option( 'ksas_schema_mode', 'author_detailed' );
	}
	if ( get_option( 'ksas_article_type', null ) === null ) {
		add_option( 'ksas_article_type', 'article' );
	}
	if ( get_option( 'ksas_link_props', null ) === null ) {
		add_option( 'ksas_link_props', [ 'author' ] );
	}
	if ( get_option( 'ksas_article_anchor', null ) === null ) {
		add_option( 'ksas_article_anchor', '' ); // Default to empty string
	}
	if ( get_option( 'ksas_schema_plugin_enable', null ) === null ) {
		add_option( 'ksas_schema_plugin_enable', 0 );
	}
	if ( get_option( 'ksas_display_on_front_page', null ) === null ) {
		add_option( 'ksas_display_on_front_page', 0 );
	}
});

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'kashiwazaki-seo-asd', false, dirname( KSAS_ASD_BASENAME ) . '/languages/' );
});

/**
 * Handles plugin uninstallation.
 * Deletes all plugin options from the database.
 */
function ksas_asd_uninstall() {
	$options_to_delete = [
		'ksas_post_types',
		'ksas_position',
		'ksas_schema_mode',
		'ksas_article_type',
		'ksas_link_props',
		'ksas_article_anchor',
		'ksas_schema_plugin_enable',
		'ksas_display_on_front_page',
	];
	foreach ( $options_to_delete as $option_name ) {
		delete_option( $option_name );
	}
}
register_uninstall_hook( __FILE__, 'ksas_asd_uninstall' );