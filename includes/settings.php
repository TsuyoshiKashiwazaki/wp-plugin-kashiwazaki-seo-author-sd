<?php
/**
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'plugin_action_links_' . KSAS_ASD_BASENAME, function ( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=ksas-settings' ) ) . '">' . __( '設定', 'kashiwazaki-seo-asd' ) . '</a>';
		$profile_link = '<a href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . __( '著者データ入力 (自身)', 'kashiwazaki-seo-asd' ) . '</a>';
		$users_link = '<a href="' . esc_url( admin_url( 'users.php' ) ) . '">' . __( 'ユーザー一覧', 'kashiwazaki-seo-asd' ) . '</a>';
		array_unshift( $links, $settings_link, $profile_link, $users_link );
		return $links;
	}
);

add_action( 'admin_menu', function () {
	add_menu_page(
		__( 'Kashiwazaki SEO Author Schema Display 設定', 'kashiwazaki-seo-asd' ),
		__( 'Kashiwazaki SEO Author Schema Display', 'kashiwazaki-seo-asd' ),
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

		$link_props_input = isset( $_POST['ksas_link_props'] ) ? (array) $_POST['ksas_link_props'] : [];
		$sanitized_link_props = array_map( 'sanitize_text_field', $link_props_input );
		$available_link_props_keys = array_keys( ksas_available_link_props() );
		$valid_link_props = array_intersect( $sanitized_link_props, $available_link_props_keys );
		update_option( 'ksas_link_props', !empty($valid_link_props) ? array_values($valid_link_props) : [ 'author' ] );

		$post_types_input = isset( $_POST['ksas_post_types'] ) ? (array) $_POST['ksas_post_types'] : [];
		$sanitized_post_types = array_map( 'sanitize_key', $post_types_input );
		$available_post_types_keys_pt = array_keys( get_post_types( [ 'public' => true ], 'names' ) );
		$valid_post_types = array_intersect( $sanitized_post_types, $available_post_types_keys_pt );
		update_option( 'ksas_post_types', $valid_post_types ?: [] );

		$position_input = isset( $_POST['ksas_position'] ) ? sanitize_key( $_POST['ksas_position'] ) : 'top';
		$valid_position = in_array( $position_input, [ 'top', 'bottom', 'both' ], true ) ? $position_input : 'top';
		update_option( 'ksas_position', $valid_position );

		$schema_mode_input = isset( $_POST['ksas_schema_mode'] ) ? sanitize_key( $_POST['ksas_schema_mode'] ) : 'none';
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

		$article_type_input = isset( $_POST['ksas_article_type'] ) ? sanitize_key( $_POST['ksas_article_type'] ) : 'article';
		$valid_article_type = in_array( $article_type_input, [ 'article', 'newsarticle', 'blogposting', 'webpage' ], true ) ? $article_type_input : 'article';
		update_option( 'ksas_article_type', $valid_article_type );

		$plugin_schema_enable = isset( $_POST['ksas_schema_plugin_enable'] ) ? 1 : 0;
		update_option( 'ksas_schema_plugin_enable', $plugin_schema_enable );

		$display_on_front_page = isset( $_POST['ksas_display_on_front_page'] ) ? 1 : 0;
		update_option( 'ksas_display_on_front_page', $display_on_front_page );


		add_settings_error('ksas_settings_messages', 'ksas_settings_saved', __( '設定を保存しました。', 'kashiwazaki-seo-asd' ), 'updated');
	}

	settings_errors( 'ksas_settings_messages' );

	$current_post_types = get_option( 'ksas_post_types', [ 'post' ] );
	$current_position = get_option( 'ksas_position', 'top' );
	$current_schema = get_option( 'ksas_schema_mode', 'author_detailed' );
	$current_linkprops = get_option( 'ksas_link_props', [ 'author' ] );
	$current_anchor = get_option( 'ksas_article_anchor', '' ); // Default to empty string
	$current_atype = get_option( 'ksas_article_type', 'article' );
	$current_plugin_schema = get_option( 'ksas_schema_plugin_enable', 0 );
	$current_display_on_front_page = get_option( 'ksas_display_on_front_page', 0 );
	$available_post_types = get_post_types( [ 'public' => true ], 'objects' ); unset( $available_post_types['attachment'] );
	$prop_labels = ksas_available_link_props();

	wp_enqueue_script( 'ksas-admin-js', KSAS_ASD_URL . 'assets/admin.js', [ 'jquery' ], KSAS_ASD_VERSION, true );
	wp_localize_script( 'ksas-admin-js', 'ksasAdminData', [ 'currentSchemaMode' => $current_schema ] );
	?>
	<div class="wrap"><h1><?php echo esc_html__( 'Kashiwazaki SEO Author Schema Display – 設定', 'kashiwazaki-seo-asd' ); ?></h1>
		<p class="ksas-admin-links">
			<a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php echo esc_html__( '著者データ入力 (自身)', 'kashiwazaki-seo-asd' ); ?></a>
		</p>
		<p style="font-size: 0.85em; color: #666; margin-top: -0.5em; margin-bottom: 1.5em;">
			Version <?php echo esc_html( KSAS_ASD_VERSION ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ksas-settings' ) ); ?>"><?php wp_nonce_field( 'ksas_save_settings', 'ksas_settings_nonce' ); ?>
			<h2 class="nav-tab-wrapper"><a href="#" class="nav-tab nav-tab-active"><?php echo esc_html__( '表示とスキーマ', 'kashiwazaki-seo-asd' ); ?></a></h2>
			<table class="form-table"><tbody>
				<tr><th scope="row"><?php echo esc_html__( '著者ボックスを表示するページ', 'kashiwazaki-seo-asd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( '著者ボックスを表示する投稿タイプを選択', 'kashiwazaki-seo-asd' ); ?></span></legend>
					<label style="display:block; margin-bottom: 5px;"><input type="checkbox" name="ksas_display_on_front_page" value="1" <?php checked( $current_display_on_front_page, 1 ); ?>> <?php echo esc_html__( 'トップページ (固定フロントページ)', 'kashiwazaki-seo-asd' ); ?></label>
					<?php foreach ( $available_post_types as $pt ) : ?>
						<label style="display:block; margin-bottom: 5px;"><input type="checkbox" name="ksas_post_types[]" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( is_array( $current_post_types ) && in_array( $pt->name, $current_post_types, true ) ); ?>> <?php echo esc_html( $pt->labels->singular_name ); ?> (<code><?php echo esc_html($pt->name); ?></code>)</label>
					<?php endforeach; ?></fieldset><p class="description"><?php echo esc_html__( '選択した投稿タイプまたは固定フロントページに著者ボックスが自動挿入されます。トップページがブログ投稿一覧の場合は表示されません。', 'kashiwazaki-seo-asd' ); ?></p></td></tr>
				<tr><th scope="row"><?php echo esc_html__( '著者ボックスの表示位置', 'kashiwazaki-seo-asd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'コンテンツ内での著者ボックスの位置を選択', 'kashiwazaki-seo-asd' ); ?></span></legend>
					<?php $positions = [ 'top'=>__( '記事上', 'kashiwazaki-seo-asd' ), 'bottom'=>__( '記事下', 'kashiwazaki-seo-asd' ), 'both'=>__( '記事上下両方', 'kashiwazaki-seo-asd' ) ]; ?>
					<?php foreach ( $positions as $value => $label ) : ?><label style="margin-right: 1em;"><input type="radio" name="ksas_position" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_position, $value ); ?>> <?php echo esc_html( $label ); ?></label><?php endforeach; ?></fieldset></td></tr>
				<tr valign="top"><th scope="row"><?php echo esc_html__( '構造化データ（スキーマ）', 'kashiwazaki-seo-asd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'Schema.org 出力モードを選択', 'kashiwazaki-seo-asd' ); ?></span></legend>
					<?php $schema_modes = [ 'none'=>__( 'スキーマを出力しない', 'kashiwazaki-seo-asd' ), 'author_simple'=>'<code>author</code>: ' . __( 'Person/Org 直埋め込み', 'kashiwazaki-seo-asd' ), 'author_detailed'=>'<code>author</code>: ' . __( 'Role＋Person/Org 参照（推奨）', 'kashiwazaki-seo-asd' ), 'person_ref'=>__( 'Person/Org 分離参照 (@id 利用)', 'kashiwazaki-seo-asd' ), ]; ?>
					<?php foreach ( $schema_modes as $value => $label ) : ?><label style="display:block; margin-bottom: 8px;"><input type="radio" class="ksas-schema-radio" name="ksas_schema_mode" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_schema, $value ); ?>> <?php echo $label; ?></label><?php endforeach; ?>
					</fieldset><p class="description"><?php echo esc_html__( 'JSON-LDスキーマでの著者情報の表現方法を選択します。「Role＋Person/Org参照」モードはE-E-A-Tシグナルとして一般的に推奨されます。', 'kashiwazaki-seo-asd' ); ?></p></td></tr>
				<tr id="ksas-articletype-row" valign="top" style="<?php echo $current_schema === 'none' ? 'display: none;' : ''; ?>"><th scope="row"><?php echo esc_html__( 'Article スキーマタイプ', 'kashiwazaki-seo-asd' ); ?></th><td><select name="ksas_article_type" id="ksas_article_type">
					<?php $article_types = [ 'article'=>'Article', 'newsarticle'=>'NewsArticle', 'blogposting'=>'BlogPosting', 'webpage'=>'WebPage' ]; ?>
					<?php foreach ( $article_types as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_atype, $value ); ?> > <?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?></select>
					<p class="description"><?php echo esc_html__( 'コンテンツの主要なスキーマタイプ（Article, NewsArticle, BlogPosting, WebPage）を選択します。', 'kashiwazaki-seo-asd' ); ?><br><?php echo esc_html__( '他のプラグイン（SEOプラグイン等）やテーマで既に出力されているスキーマタイプと重複しないようにご注意ください。', 'kashiwazaki-seo-asd' ); ?></p>
					</td></tr>
				<tr id="ksas-linkprops-row" valign="top" style="<?php echo $current_schema !== 'person_ref' ? 'display: none;' : ''; ?>">
					<th scope="row"><?php echo esc_html__( 'Person/Org へのリンクプロパティ', 'kashiwazaki-seo-asd' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php echo esc_html__( 'Article スキーマから分離した Person/Org スキーマへリンクするプロパティを選択', 'kashiwazaki-seo-asd' ); ?></span></legend>
							<?php foreach ( $prop_labels as $key => $lbl ) : ?>
							<label style="display: inline-block; margin: 0 .8em .4em 0; white-space: nowrap;">
								<input type="checkbox" name="ksas_link_props[]" value="<?php echo esc_attr( $key ); ?>"
								<?php checked( is_array( $current_linkprops ) && in_array( $key, $current_linkprops, true ) ); ?> >
								<code><?php echo esc_html( $lbl ); ?></code>
							</label>
							<?php endforeach; ?>
						</fieldset>
						<p class="description"><?php echo esc_html__( '「Person/Org 分離参照」モード選択時、Article スキーマから Person/Org スキーマへどのプロパティでリンクするか選択します (複数選択可)。通常は `author` のみで十分です。', 'kashiwazaki-seo-asd' ); ?></p>
					</td>
				</tr>
				<tr id="ksas-anchor-row" valign="top" style="<?php echo $current_schema !== 'person_ref' ? 'display: none;' : ''; ?>"><th scope="row"><label for="ksas_article_anchor"><?php echo esc_html__( '記事スキーマのアンカー', 'kashiwazaki-seo-asd' ); ?></label></th><td><input type="text" name="ksas_article_anchor" id="ksas_article_anchor" value="<?php echo esc_attr( $current_anchor ); ?>" class="regular-text" style="width: 140px;" placeholder="#Article"><p class="description"><?php echo esc_html__( '「Person/Org 分離参照」モード選択時、Article スキーマの `@id` の末尾に追加するアンカーを指定します (例: `#Article`)。空欄の場合はアンカーは付加されません。', 'kashiwazaki-seo-asd' ); ?></p></td></tr>
				<tr valign="top"><th scope="row"><?php echo esc_html__( 'プラグイン情報スキーマ', 'kashiwazaki-seo-asd' ); ?></th><td><fieldset><legend class="screen-reader-text"><span><?php echo esc_html__( 'このプラグイン自身の SoftwareApplication スキーマを出力するかどうか', 'kashiwazaki-seo-asd' ); ?></span></legend><label for="ksas_schema_plugin_enable"><input type="checkbox" name="ksas_schema_plugin_enable" id="ksas_schema_plugin_enable" value="1" <?php checked( $current_plugin_schema, 1 ); ?>> <?php echo esc_html__( 'このプラグイン自身の情報 (SoftwareApplication スキーマ) を出力する', 'kashiwazaki-seo-asd' ); ?></label><p class="description"><?php echo esc_html__( 'このプラグイン自体の情報を Schema.org を使って出力します。診断やプラグインの紹介に役立ちます。', 'kashiwazaki-seo-asd' ); ?></p></fieldset></td></tr>
			</tbody></table><?php submit_button( __( '設定を保存', 'kashiwazaki-seo-asd' ) ); ?>
		</form></div>
	<?php
}