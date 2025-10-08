<?php
/**
 * Plugin Name:  Kashiwazaki SEO Author Schema Display
 * Plugin URI:   https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-author-sd
 * Description:  著者カード（顔写真・肩書・SNS 等）を記事上下に自動表示し、Article・NewsArticle・BlogPosting・WebPage＋Role・Person の JSON‑LD を生成、E‑E‑A‑Tとリッチリザルトを一括強化するオールインワン SEO プラグイン。
 * Version:      1.0.5
 * Author:       柏崎剛 (Tsuyoshi Kashiwazaki)
 * Author URI:   https://www.tsuyoshikashiwazaki.jp/profile/
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  kashiwazaki-seo-author-sd
 * Domain Path:  /languages
 *
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'KSAS_ASD_VERSION', '1.0.5' );
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
		add_option( 'ksas_article_anchor', '' );
	}
	if ( get_option( 'ksas_schema_plugin_enable', null ) === null ) {
		add_option( 'ksas_schema_plugin_enable', 0 );
	}
	if ( get_option( 'ksas_display_on_home', null ) === null ) {
		add_option( 'ksas_display_on_home', 0 );
	}
	
	// データベース構造アップグレード
	ksas_upgrade_database();
});

/**
 * データベース構造のアップグレード処理
 * 旧フィールドから新しいタイプ別フィールドへのマイグレーション
 */
function ksas_upgrade_database() {
	$current_version = get_option( 'ksas_db_version', '1.0.0' );
	
	if ( version_compare( $current_version, '1.0.2', '<' ) ) {
		// バージョン1.0.2へのアップグレード: タイプ別フィールドへの移行
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Necessary for data migration, runs only during plugin upgrade
		$users = get_users( [ 'meta_key' => 'asd_author_type' ] );
		
		foreach ( $users as $user ) {
			$author_type = get_user_meta( $user->ID, 'asd_author_type', true ) ?: 'person';
			
			// 旧フィールドから新フィールドへデータをコピー（既に新フィールドにデータがない場合のみ）
			$migration_map = [
				'asd_display_name' => 'display_name',
				'asd_avatar_url' => 'avatar_url', 
				'asd_alternate_name' => 'alternate_name',
				'asd_occupation' => 'occupation',
				'asd_organization' => 'organization',
				'asd_contact_email' => 'contact_email',
				'asd_profile_link' => 'profile_link',
				'asd_bio' => 'bio',
				'asd_sns_urls' => 'sns_urls',
			];
			
			foreach ( $migration_map as $old_key => $new_suffix ) {
				$old_value = get_user_meta( $user->ID, $old_key, true );
				if ( ! empty( $old_value ) ) {
					$new_key = 'asd_' . $author_type . '_' . $new_suffix;
					$existing_new_value = get_user_meta( $user->ID, $new_key, true );
					
					// 新フィールドが空の場合のみ移行
					if ( empty( $existing_new_value ) ) {
						update_user_meta( $user->ID, $new_key, $old_value );
					}
				}
			}
		}
		
		update_option( 'ksas_db_version', '1.0.2' );
	}
	
	if ( version_compare( $current_version, '1.0.3', '<' ) ) {
		// バージョン1.0.3へのアップグレード: テーマ独立性強化とメディアライブラリ対応
		// 特別なデータベース変更は不要のため、バージョン番号のみ更新
		update_option( 'ksas_db_version', '1.0.3' );
	}
	
	if ( version_compare( $current_version, '1.0.4', '<' ) ) {
		// バージョン1.0.4へのアップグレード: ホームページ設定統一とPlugin Check対応
		// ksas_display_on_front_page オプションを削除（ホームページ設定に統一）
		delete_option( 'ksas_display_on_front_page' );
		update_option( 'ksas_db_version', '1.0.4' );
	}

	if ( version_compare( $current_version, '1.0.5', '<' ) ) {
		// バージョン1.0.5へのアップグレード: UI改善、表示位置拡張、タブ化
		// データベース変更は不要のため、バージョン番号のみ更新
		update_option( 'ksas_db_version', '1.0.5' );
	}
}


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
		'ksas_display_on_home',
	];
	foreach ( $options_to_delete as $option_name ) {
		delete_option( $option_name );
	}
}
register_uninstall_hook( __FILE__, 'ksas_asd_uninstall' );