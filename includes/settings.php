<?php
/**
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'plugin_action_links_' . KSAS_ASD_BASENAME, function ( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=ksas-settings' ) ) . '">' . __( '基本設定', 'kashiwazaki-seo-author-sd' ) . '</a>';
		$profile_link = '<a href="' . esc_url( admin_url( 'profile.php#ksas-author-fields' ) ) . '">' . __( '著者情報を編集', 'kashiwazaki-seo-author-sd' ) . '</a>';
		$users_link = '<a href="' . esc_url( admin_url( 'users.php' ) ) . '">' . __( 'ユーザー一覧', 'kashiwazaki-seo-author-sd' ) . '</a>';
		array_unshift( $links, $settings_link, $profile_link, $users_link );
		return $links;
	}
);

add_action( 'admin_menu', function () {
	add_menu_page(
		__( 'Kashiwazaki SEO Author Schema Display 基本設定', 'kashiwazaki-seo-author-sd' ),
		__( 'Kashiwazaki SEO Author Schema Display', 'kashiwazaki-seo-author-sd' ),
		'manage_options',
		'ksas-settings',
		'ksas_render_settings_page',
		'dashicons-admin-users',
		81
	);
});

function ksas_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }

	if ( isset( $_POST['submit'], $_POST['ksas_settings_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['ksas_settings_nonce']), 'ksas_save_settings' ) ) {

		$link_props_input = isset( $_POST['ksas_link_props'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['ksas_link_props'] ) ) : [];
		$sanitized_link_props = $link_props_input;
		$available_link_props_keys = array_keys( ksas_available_link_props() );
		$valid_link_props = array_intersect( $sanitized_link_props, $available_link_props_keys );
		update_option( 'ksas_link_props', !empty($valid_link_props) ? array_values($valid_link_props) : [ 'author' ] );

		$post_types_input = isset( $_POST['ksas_post_types'] ) ? array_map( 'sanitize_key', wp_unslash( (array) $_POST['ksas_post_types'] ) ) : [];
		$sanitized_post_types = $post_types_input;
		$available_post_types_keys_pt = array_keys( get_post_types( [ 'public' => true ], 'names' ) );
		$valid_post_types = array_intersect( $sanitized_post_types, $available_post_types_keys_pt );
		update_option( 'ksas_post_types', $valid_post_types ?: [] );

		$position_input = isset( $_POST['ksas_position'] ) ? sanitize_key( wp_unslash( $_POST['ksas_position'] ) ) : 'top';
		$valid_positions = [
			'top', 'bottom', 'both',
			'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
			'h1_after', 'h2_after', 'h3_after', 'h4_after', 'h5_after', 'h6_after',
			'first_p_after', 'second_p_after', 'third_p_after',
			'first_image_after', 'first_blockquote_after', 'first_list_after', 'first_table_after',
			'after_more_tag', 'before_last_p', 'last_p_after', 'last_tag_after'
		];
		$valid_position = in_array( $position_input, $valid_positions, true ) ? $position_input : 'top';
		update_option( 'ksas_position', $valid_position );

		$schema_mode_input = isset( $_POST['ksas_schema_mode'] ) ? sanitize_key( wp_unslash( $_POST['ksas_schema_mode'] ) ) : 'none';
		$valid_schema_mode = in_array( $schema_mode_input, [ 'none', 'author_simple', 'author_detailed', 'person_ref' ], true ) ? $schema_mode_input : 'author_detailed';
		update_option( 'ksas_schema_mode', $valid_schema_mode );

		$anchor_input = isset( $_POST['ksas_article_anchor'] ) ? sanitize_text_field( wp_unslash( $_POST['ksas_article_anchor'] ) ) : ''; // Default to empty string
		$anchor_input = trim( $anchor_input ); // Trim whitespace

		if ( ! empty( $anchor_input ) ) {
			// Ensure it starts with '#' if not empty
			if ( strpos( $anchor_input, '#' ) !== 0 ) {
				$anchor_input = '#' . $anchor_input;
			}
		}
		// If it's empty, or becomes just '#', make it empty.
		// This allows users to explicitly set an empty anchor or remove it.
		if ( $anchor_input === '#' ) {
			$anchor_input = '';
		}
		update_option( 'ksas_article_anchor', $anchor_input );

		$article_type_input = isset( $_POST['ksas_article_type'] ) ? sanitize_key( wp_unslash( $_POST['ksas_article_type'] ) ) : 'article';
		$valid_article_type = in_array( $article_type_input, [ 'article', 'newsarticle', 'blogposting', 'webpage' ], true ) ? $article_type_input : 'article';
		update_option( 'ksas_article_type', $valid_article_type );

		$plugin_schema_enable = isset( $_POST['ksas_schema_plugin_enable'] ) ? 1 : 0;
		update_option( 'ksas_schema_plugin_enable', $plugin_schema_enable );

		$display_on_home = isset( $_POST['ksas_display_on_home'] ) ? 1 : 0;
		update_option( 'ksas_display_on_home', $display_on_home );

		add_settings_error('ksas_settings_messages', 'ksas_settings_saved', __( '設定を保存しました。', 'kashiwazaki-seo-author-sd' ), 'updated');
	}

	settings_errors( 'ksas_settings_messages' );

	$current_post_types = get_option( 'ksas_post_types', [ 'post' ] );
	$current_position = get_option( 'ksas_position', 'top' );
	$current_schema = get_option( 'ksas_schema_mode', 'author_detailed' );
	$current_linkprops = get_option( 'ksas_link_props', [ 'author' ] );
	$current_anchor = get_option( 'ksas_article_anchor', '' ); // Default to empty string
	$current_atype = get_option( 'ksas_article_type', 'article' );
	$current_plugin_schema = get_option( 'ksas_schema_plugin_enable', 0 );
	$current_display_on_home = get_option( 'ksas_display_on_home', 0 );
	$current_display_on_category = get_option( 'ksas_display_on_category', 0 );
	$current_display_on_tag = get_option( 'ksas_display_on_tag', 0 );
	$available_post_types = get_post_types( [ 'public' => true ], 'objects' );

	// attachmentは除外
	unset( $available_post_types['attachment'] );

	// 各投稿タイプでthe_content()が使われているかチェック
	foreach ( $available_post_types as $post_type_name => $post_type_obj ) {
		if ( ! ksas_post_type_uses_the_content( $post_type_name ) ) {
			unset( $available_post_types[ $post_type_name ] );
		}
	}

	$prop_labels = ksas_available_link_props();

	wp_enqueue_script( 'ksas-admin-js', KSAS_ASD_URL . 'assets/admin.js', [ 'jquery' ], filemtime( KSAS_ASD_PATH . 'assets/admin.js' ), true );
	wp_localize_script( 'ksas-admin-js', 'ksasAdminData', [ 'currentSchemaMode' => $current_schema ] );
	?>
	<div class="wrap"><h1><?php echo esc_html__( 'Kashiwazaki SEO Author Schema Display – 基本設定', 'kashiwazaki-seo-author-sd' ); ?></h1>
		<p class="ksas-admin-links">
			<a href="<?php echo esc_url( admin_url( 'profile.php#ksas-author-fields' ) ); ?>"><?php echo esc_html__( '著者情報を編集', 'kashiwazaki-seo-author-sd' ); ?></a>
		</p>
		<p style="font-size: 0.85em; color: #666; margin-top: -0.5em; margin-bottom: 1.5em;">
			Version <?php echo esc_html( KSAS_ASD_VERSION ); ?>
		</p>
		<h2 class="nav-tab-wrapper">
			<a href="#" class="nav-tab nav-tab-active" data-tab="display-settings"><?php echo esc_html__( '表示設定', 'kashiwazaki-seo-author-sd' ); ?></a>
			<a href="#" class="nav-tab" data-tab="schema-settings"><?php echo esc_html__( 'スキーマ設定', 'kashiwazaki-seo-author-sd' ); ?></a>
			<a href="#" class="nav-tab" data-tab="shortcode-info"><?php echo esc_html__( 'ショートコード', 'kashiwazaki-seo-author-sd' ); ?></a>
		</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ksas-settings' ) ); ?>"><?php wp_nonce_field( 'ksas_save_settings', 'ksas_settings_nonce' ); ?>
			<div id="ksas-tab-display-settings" class="ksas-tab-content">
			<table class="form-table"><tbody>
				<tr><th scope="row"><?php echo esc_html__( '著者ボックスを表示するページ', 'kashiwazaki-seo-author-sd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( '著者ボックスを表示する投稿タイプを選択', 'kashiwazaki-seo-author-sd' ); ?></span></legend>
					<label style="display:block; margin-bottom: 5px;"><input type="checkbox" name="ksas_display_on_home" value="1" <?php checked( $current_display_on_home, 1 ); ?>> <?php echo esc_html__( 'ホームページ', 'kashiwazaki-seo-author-sd' ); ?></label>
					<?php foreach ( $available_post_types as $pt ) : ?>
						<label style="display:block; margin-bottom: 5px;"><input type="checkbox" name="ksas_post_types[]" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( is_array( $current_post_types ) && in_array( $pt->name, $current_post_types, true ) ); ?>> <?php echo esc_html( $pt->labels->singular_name ); ?> (<code><?php echo esc_html($pt->name); ?></code>)</label>
					<?php endforeach; ?></fieldset><p class="description"><?php echo esc_html__( '選択したページ・投稿タイプに著者ボックスが自動挿入されます。', 'kashiwazaki-seo-author-sd' ); ?></p></td></tr>
				<tr><th scope="row"><?php echo esc_html__( '著者ボックスの表示位置', 'kashiwazaki-seo-author-sd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'コンテンツ内での著者ボックスの位置を選択', 'kashiwazaki-seo-author-sd' ); ?></span></legend>
					<?php $position_groups = [
						'基本' => [
							'top' => __( '記事上', 'kashiwazaki-seo-author-sd' ),
							'bottom' => __( '記事下', 'kashiwazaki-seo-author-sd' ),
							'both' => __( '記事上下両方', 'kashiwazaki-seo-author-sd' ),
							'last_tag_after' => __( '最後のHTMLタグの直後', 'kashiwazaki-seo-author-sd' ),
						],
						'見出しの前' => [
							'h1' => __( '最初のh1上', 'kashiwazaki-seo-author-sd' ),
							'h2' => __( '最初のh2上', 'kashiwazaki-seo-author-sd' ),
							'h3' => __( '最初のh3上', 'kashiwazaki-seo-author-sd' ),
							'h4' => __( '最初のh4上', 'kashiwazaki-seo-author-sd' ),
							'h5' => __( '最初のh5上', 'kashiwazaki-seo-author-sd' ),
							'h6' => __( '最初のh6上', 'kashiwazaki-seo-author-sd' ),
						],
						'見出しの後' => [
							'h1_after' => __( '最初のh1下', 'kashiwazaki-seo-author-sd' ),
							'h2_after' => __( '最初のh2下', 'kashiwazaki-seo-author-sd' ),
							'h3_after' => __( '最初のh3下', 'kashiwazaki-seo-author-sd' ),
							'h4_after' => __( '最初のh4下', 'kashiwazaki-seo-author-sd' ),
							'h5_after' => __( '最初のh5下', 'kashiwazaki-seo-author-sd' ),
							'h6_after' => __( '最初のh6下', 'kashiwazaki-seo-author-sd' ),
						],
						'段落' => [
							'first_p_after' => __( '最初の段落の直後', 'kashiwazaki-seo-author-sd' ),
							'second_p_after' => __( '2番目の段落の直後', 'kashiwazaki-seo-author-sd' ),
							'third_p_after' => __( '3番目の段落の直後', 'kashiwazaki-seo-author-sd' ),
							'before_last_p' => __( '最後の段落の直前', 'kashiwazaki-seo-author-sd' ),
							'last_p_after' => __( '最後の段落の直後', 'kashiwazaki-seo-author-sd' ),
						],
						'特殊要素' => [
							'first_image_after' => __( '最初の画像の直後', 'kashiwazaki-seo-author-sd' ),
							'first_blockquote_after' => __( '最初の引用の直後', 'kashiwazaki-seo-author-sd' ),
							'first_list_after' => __( '最初のリストの直後', 'kashiwazaki-seo-author-sd' ),
							'first_table_after' => __( '最初のテーブルの直後', 'kashiwazaki-seo-author-sd' ),
							'after_more_tag' => __( '続きを読むタグ (<!--more-->) の直後', 'kashiwazaki-seo-author-sd' ),
						],
					]; ?>
					<?php foreach ( $position_groups as $group_name => $positions ) : ?>
						<p style="margin: 10px 0 5px 0; font-weight: 600; color: #2271b1;"><?php echo esc_html( $group_name ); ?></p>
						<?php foreach ( $positions as $value => $label ) : ?>
						<div style="margin-bottom: 6px; margin-left: 10px;"><label><input type="radio" name="ksas_position" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_position, $value ); ?>> <?php echo esc_html( $label ); ?></label></div>
						<?php endforeach; ?>
					<?php endforeach; ?></fieldset></td></tr>
			</tbody></table>
			<?php submit_button( __( '設定を保存', 'kashiwazaki-seo-author-sd' ) ); ?>
			</div>

			<div id="ksas-tab-schema-settings" class="ksas-tab-content" style="display: none;">
			<table class="form-table"><tbody>
				<tr valign="top"><th scope="row"><?php echo esc_html__( '構造化データ（スキーマ）', 'kashiwazaki-seo-author-sd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'Schema.org 出力モードを選択', 'kashiwazaki-seo-author-sd' ); ?></span></legend>
					<?php $schema_modes = [ 'none'=>__( 'スキーマを出力しない', 'kashiwazaki-seo-author-sd' ), 'author_simple'=>'<code>author</code>: ' . __( 'Person/Org 直埋め込み', 'kashiwazaki-seo-author-sd' ), 'author_detailed'=>'<code>author</code>: ' . __( 'Role＋Person/Org 参照（推奨）', 'kashiwazaki-seo-author-sd' ), 'person_ref'=>__( 'Person/Org 分離参照 (@id 利用)', 'kashiwazaki-seo-author-sd' ), ]; ?>
					<?php foreach ( $schema_modes as $value => $label ) : ?><label style="display:block; margin-bottom: 8px;"><input type="radio" class="ksas-schema-radio" name="ksas_schema_mode" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_schema, $value ); ?>> <?php echo wp_kses_post( $label ); ?></label><?php endforeach; ?>
					</fieldset><p class="description"><?php echo esc_html__( 'JSON-LDスキーマでの著者情報の表現方法を選択します。「Role＋Person/Org参照」モードはE-E-A-Tシグナルとして一般的に推奨されます。', 'kashiwazaki-seo-author-sd' ); ?></p></td></tr>
				<tr id="ksas-articletype-row" valign="top" style="<?php echo $current_schema === 'none' ? 'display: none;' : ''; ?>"><th scope="row"><?php echo esc_html__( 'Article スキーマタイプ', 'kashiwazaki-seo-author-sd' ); ?></th><td><select name="ksas_article_type" id="ksas_article_type">
					<?php $article_types = [ 'article'=>'Article', 'newsarticle'=>'NewsArticle', 'blogposting'=>'BlogPosting', 'webpage'=>'WebPage' ]; ?>
					<?php foreach ( $article_types as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_atype, $value ); ?> > <?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?></select>
					<p class="description"><?php echo esc_html__( 'コンテンツの主要なスキーマタイプ（Article, NewsArticle, BlogPosting, WebPage）を選択します。', 'kashiwazaki-seo-author-sd' ); ?><br><?php echo esc_html__( '他のプラグイン（SEOプラグイン等）やテーマで既に出力されているスキーマタイプと重複しないようにご注意ください。', 'kashiwazaki-seo-author-sd' ); ?></p>
					</td></tr>
				<tr id="ksas-linkprops-row" valign="top" style="<?php echo $current_schema !== 'person_ref' ? 'display: none;' : ''; ?>">
					<th scope="row"><?php echo esc_html__( 'Person/Org へのリンクプロパティ', 'kashiwazaki-seo-author-sd' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_html__( 'Article スキーマから分離した Person/Org スキーマへリンクするプロパティを選択', 'kashiwazaki-seo-author-sd' ); ?></span></legend>
							<?php foreach ( $prop_labels as $key => $lbl ) : ?>
							<label style="display: inline-block; margin: 0 .8em .4em 0; white-space: nowrap;">
								<input type="checkbox" name="ksas_link_props[]" value="<?php echo esc_attr( $key ); ?>"
								<?php checked( is_array( $current_linkprops ) && in_array( $key, $current_linkprops, true ) ); ?> >
								<code><?php echo esc_html( $lbl ); ?></code>
							</label>
							<?php endforeach; ?>
						</fieldset>
						<p class="description"><?php echo esc_html__( '「Person/Org 分離参照」モード選択時、Article スキーマから Person/Org スキーマへどのプロパティでリンクするか選択します (複数選択可)。通常は `author` のみで十分です。', 'kashiwazaki-seo-author-sd' ); ?></p>
					</td>
				</tr>
				<tr id="ksas-anchor-row" valign="top" style="<?php echo $current_schema !== 'person_ref' ? 'display: none;' : ''; ?>"><th scope="row"><label for="ksas_article_anchor"><?php echo esc_html__( '記事スキーマのアンカー', 'kashiwazaki-seo-author-sd' ); ?></label></th><td><input type="text" name="ksas_article_anchor" id="ksas_article_anchor" value="<?php echo esc_attr( $current_anchor ); ?>" class="regular-text" style="width: 140px;" placeholder="#Article"><p class="description"><?php echo esc_html__( '「Person/Org 分離参照」モード選択時、Article スキーマの `@id` の末尾に追加するアンカーを指定します (例: `#Article`)。空欄の場合はアンカーは付加されません。', 'kashiwazaki-seo-author-sd' ); ?></p></td></tr>
				<tr valign="top"><th scope="row"><?php echo esc_html__( 'プラグイン情報スキーマ', 'kashiwazaki-seo-author-sd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'このプラグイン自身の SoftwareApplication スキーマを出力するかどうか', 'kashiwazaki-seo-author-sd' ); ?></span></legend><label for="ksas_schema_plugin_enable"><input type="checkbox" name="ksas_schema_plugin_enable" id="ksas_schema_plugin_enable" value="1" <?php checked( $current_plugin_schema, 1 ); ?>> <?php echo esc_html__( 'このプラグイン自身の情報 (SoftwareApplication スキーマ) を出力する', 'kashiwazaki-seo-author-sd' ); ?></label><p class="description"><?php echo esc_html__( 'このプラグイン自体の情報を Schema.org を使って出力します。診断やプラグインの紹介に役立ちます。', 'kashiwazaki-seo-author-sd' ); ?></p></fieldset></td></tr>
			</tbody></table>
			<?php submit_button( __( '設定を保存', 'kashiwazaki-seo-author-sd' ) ); ?>
			</div>
		</form>

		<div id="ksas-tab-shortcode-info" class="ksas-tab-content" style="display: none;">
		<div class="card" style="max-width: 100%; margin-top: 1em;">
			<h3><?php echo esc_html__( 'ショートコードの使用方法', 'kashiwazaki-seo-author-sd' ); ?></h3>
			<p><?php echo esc_html__( '以下のショートコードを使用して、任意の場所に著者ボックスを表示できます。', 'kashiwazaki-seo-author-sd' ); ?></p>
			
			<table class="widefat" style="margin-top: 1em;">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'ショートコード', 'kashiwazaki-seo-author-sd' ); ?></th>
						<th><?php echo esc_html__( '説明', 'kashiwazaki-seo-author-sd' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><code>[ksas_author]</code></td>
						<td><?php echo esc_html__( '現在の投稿の著者またはデフォルトの著者を表示', 'kashiwazaki-seo-author-sd' ); ?></td>
					</tr>
					<tr>
						<td><code>[ksas_author user_id="1"]</code></td>
						<td><?php echo esc_html__( '指定したユーザーIDの著者を表示', 'kashiwazaki-seo-author-sd' ); ?></td>
					</tr>
					<tr>
						<td><code>[ksas_author author="username"]</code></td>
						<td><?php echo esc_html__( '指定したユーザー名の著者を表示', 'kashiwazaki-seo-author-sd' ); ?></td>
					</tr>
				</tbody>
			</table>
			
			<h4 style="margin-top: 2em; margin-bottom: 1em;"><?php echo esc_html__( 'テンプレートファイルでの使用方法', 'kashiwazaki-seo-author-sd' ); ?></h4>
			<p><?php echo esc_html__( 'category.php、tag.php、single.phpなどのテンプレートファイルに直接記述する場合は、以下のようにPHPコードで記述してください。', 'kashiwazaki-seo-author-sd' ); ?></p>
			
			<div style="background: #f1f1f1; padding: 15px; border-radius: 4px; margin: 15px 0; font-family: monospace; font-size: 13px; border-left: 4px solid #0073aa;">
				<strong><?php echo esc_html__( '基本的な使用方法：', 'kashiwazaki-seo-author-sd' ); ?></strong><br>
				<code>&lt;?php echo do_shortcode( '[ksas_author]' ); ?&gt;</code><br><br>
				
				<strong><?php echo esc_html__( '特定のユーザーIDを指定：', 'kashiwazaki-seo-author-sd' ); ?></strong><br>
				<code>&lt;?php echo do_shortcode( '[ksas_author user_id="1"]' ); ?&gt;</code><br><br>
				
				<strong><?php echo esc_html__( '特定のユーザー名を指定：', 'kashiwazaki-seo-author-sd' ); ?></strong><br>
				<code>&lt;?php echo do_shortcode( '[ksas_author author="username"]' ); ?&gt;</code>
			</div>
			
			<h4 style="margin-top: 2em; margin-bottom: 1em;"><?php echo esc_html__( '使用例', 'kashiwazaki-seo-author-sd' ); ?></h4>
			<div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0; font-family: monospace; font-size: 12px; border: 1px solid #ddd;">
				<strong>category.php での使用例：</strong><br>
				<code style="color: #666;">&lt;?php get_header(); ?&gt;</code><br>
				<code style="color: #666;">&lt;div class="container"&gt;</code><br>
				<code style="color: #666;">&nbsp;&nbsp;&nbsp;&nbsp;&lt;h1&gt;&lt;?php single_cat_title(); ?&gt;&lt;/h1&gt;</code><br>
				<code style="color: #d54e21;">&nbsp;&nbsp;&nbsp;&nbsp;&lt;?php echo do_shortcode( '[ksas_author]' ); ?&gt;</code><br>
				<code style="color: #666;">&nbsp;&nbsp;&nbsp;&nbsp;&lt;!-- ここに投稿一覧など --&gt;</code><br>
				<code style="color: #666;">&lt;/div&gt;</code><br>
				<code style="color: #666;">&lt;?php get_footer(); ?&gt;</code>
			</div>
			
			<p style="margin-top: 1em; font-size: 0.9em; color: #666;">
				<?php echo esc_html__( 'ショートコードは投稿、固定ページ、ウィジェット、テンプレートファイル内で使用できます。テンプレートファイルで使用する場合は必ず上記のようにPHPコードとして記述してください。', 'kashiwazaki-seo-author-sd' ); ?>
			</p>
		</div>
		</div>
	</div>
	<?php
}