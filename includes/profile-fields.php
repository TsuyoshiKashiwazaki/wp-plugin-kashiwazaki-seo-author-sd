<?php
/**
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function ksas_profile_fields( $user ) {
	?>
	<h2>Kashiwazaki SEO Author Schema Display - 著者データ入力</h2>
	<p>著者ボックスに表示し、スキーママークアップで使用される著者情報を入力します。</p>
	<table class="form-table" role="presentation">
		<?php
		wp_nonce_field( 'ksas_save_profile_' . $user->ID, 'ksas_profile_nonce' );

		$author_types = [
			'person'       => '人物 (Person)',
			'organization' => '組織 (Organization)',
			'corporation'  => '法人 (Corporation)',
		];
		$current_author_type = get_user_meta( $user->ID, 'asd_author_type', true ) ?: 'person';
		?>
		<tr>
			<th><label for="asd_author_type"><?php echo esc_html__( '著者タイプ', 'kashiwazaki-seo-asd' ); ?></label></th>
			<td>
				<select name="asd_author_type" id="asd_author_type">
					<?php foreach ( $author_types as $type_value => $type_label ) : ?>
						<option value="<?php echo esc_attr( $type_value ); ?>" <?php selected( $current_author_type, $type_value ); ?>><?php echo esc_html( $type_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php echo esc_html__( '著者の種別を選択します。スキーマの @type や表示項目に影響します。', 'kashiwazaki-seo-asd' ); ?></p>
			</td>
		</tr>
		<?php

		$fields = [
			// 共通フィールド
			'asd_role_type'     => [ '役割種別', 'select', 'このユーザー/組織の主な役割（例：執筆者、監修者）を選択します。バッジ表示やスキーマに影響します。', 'common' ],
			
			// 人物タイプ専用フィールド
			'asd_person_display_name'  => [ '表示名', 'text', '著者ボックスとスキーマの`name`に使用されます。空の場合はWordPressの表示名が使用されます。', 'person' ],
			'asd_person_avatar_url'    => [ '顔写真/プロフィール画像 URL', 'url', 'プロフィール画像のURL（推奨：正方形、112x112px以上）。空の場合はGravatar/WordPressアバターが使用されます。', 'person' ],
			'asd_person_alternate_name'=> [ '別名（ペンネーム、ニックネーム等）', 'text', 'スキーマの`alternateName`に使用されます。', 'person' ],
			'asd_person_occupation'    => [ '職業/肩書き', 'text', '著者ボックスとスキーマの`jobTitle`に使用されます。', 'person' ],
			'asd_person_organization'  => [ '所属組織', 'text', 'スキーマの`worksFor`に使用されます。', 'person' ],
			'asd_person_contact_email' => [ '連絡先メールアドレス（公開用）', 'email', '連絡用の公開メールアドレス。メールアイコンのリンクとスキーマの`email`に使用されます。', 'person' ],
			'asd_person_profile_link'  => [ 'プロフィールリンク（著者ページ、ウェブサイト等）', 'url', '著者のメインプロフィールやウェブサイトへのリンク。画像/名前のリンクとスキーマの`url`/`@id`に使用されます。', 'person' ],
			'asd_person_bio'           => [ 'プロフィール文', 'textarea', '簡単な自己紹介。著者ボックスとスキーマの`description`に使用されます。HTMLタグは除去されます。', 'person' ],
			'asd_person_sns_urls'      => [ 'SNS・ウェブサイトURL（1行に1つ）', 'textarea', '関連するURLを1行に1つずつ入力します。アイコンリンクとスキーマの`sameAs`に使用されます。', 'person' ],
			
			// 組織タイプ専用フィールド
			'asd_organization_display_name'  => [ '組織名', 'text', '著者ボックスとスキーマの`name`に使用されます。', 'organization' ],
			'asd_organization_avatar_url'    => [ 'ロゴ画像 URL', 'url', '組織ロゴのURL（推奨：正方形、112x112px以上）。', 'organization' ],
			'asd_organization_alternate_name'=> [ '代替名（略称、旧組織名など）', 'text', 'スキーマの`alternateName`に使用されます。', 'organization' ],
			'asd_organization_contact_email' => [ '連絡先メールアドレス（公開用）', 'email', '連絡用の公開メールアドレス。メールアイコンのリンクとスキーマの`email`に使用されます。', 'organization' ],
			'asd_organization_profile_link'  => [ '公式サイト URL', 'url', '組織の公式サイトへのリンク。画像/名前のリンクとスキーマの`url`/`@id`に使用されます。', 'organization' ],
			'asd_organization_bio'           => [ '組織概要', 'textarea', '組織の概要や事業内容。著者ボックスとスキーマの`description`に使用されます。HTMLタグは除去されます。', 'organization' ],
			'asd_organization_sns_urls'      => [ '関連リンク（公式サイト、SNS等、1行に1つ）', 'textarea', '関連するURLを1行に1つずつ入力します。アイコンリンクとスキーマの`sameAs`に使用されます。', 'organization' ],
			
			// 法人タイプ専用フィールド
			'asd_corporation_display_name'  => [ '法人名', 'text', '著者ボックスとスキーマの`name`に使用されます。', 'corporation' ],
			'asd_corporation_avatar_url'    => [ 'ロゴ画像 URL', 'url', '法人ロゴのURL（推奨：正方形、112x112px以上）。', 'corporation' ],
			'asd_corporation_alternate_name'=> [ '代替名（略称、旧法人名など）', 'text', 'スキーマの`alternateName`に使用されます。', 'corporation' ],
			'asd_corporation_contact_email' => [ '連絡先メールアドレス（公開用）', 'email', '連絡用の公開メールアドレス。メールアイコンのリンクとスキーマの`email`に使用されます。', 'corporation' ],
			'asd_corporation_profile_link'  => [ '公式サイト URL', 'url', '法人の公式サイトへのリンク。画像/名前のリンクとスキーマの`url`/`@id`に使用されます。', 'corporation' ],
			'asd_corporation_bio'           => [ '法人概要', 'textarea', '法人の概要や事業内容。著者ボックスとスキーマの`description`に使用されます。HTMLタグは除去されます。', 'corporation' ],
			'asd_corporation_sns_urls'      => [ '関連リンク（公式サイト、SNS等、1行に1つ）', 'textarea', '関連するURLを1行に1つずつ入力します。アイコンリンクとスキーマの`sameAs`に使用されます。', 'corporation' ],
		];

		foreach ( $fields as $key => $info ) {
			list( $label, $type, $description, $visibility ) = array_pad( $info, 4, null );
			$val = get_user_meta( $user->ID, $key, true );

			$row_style = '';
			$row_class = '';
			
			if ( $visibility === 'common' ) {
				$row_class = ' class="ksas-profile-field-common"';
			} elseif ( $visibility === 'person' && $current_author_type !== 'person' ) {
				$row_style = ' style="display: none;"';
				$row_class = ' class="ksas-profile-field-person"';
			} elseif ( $visibility === 'organization' && $current_author_type !== 'organization' ) {
				$row_style = ' style="display: none;"';
				$row_class = ' class="ksas-profile-field-organization"';
			} elseif ( $visibility === 'corporation' && $current_author_type !== 'corporation' ) {
				$row_style = ' style="display: none;"';
				$row_class = ' class="ksas-profile-field-corporation"';
			} else {
				$row_class = ' class="ksas-profile-field-' . esc_attr( $visibility ) . '"';
			}

			?>
			<tr<?php echo $row_style; ?><?php echo $row_class; ?>>
				<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<?php if ( 'select' === $type && $key === 'asd_role_type' ) :
						$options = [ 'author'=>'執筆者','supervisor'=>'監修者','admin'=>'管理者' ];
						$current_val = $val ?: 'author'; ?>
						<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>">
							<?php foreach ( $options as $option_value => $option_label ) : ?>
								<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $current_val, $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php elseif ( 'textarea' === $type ) : ?>
						<textarea name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" rows="5" class="large-text"><?php echo esc_textarea( $val ); ?></textarea>
					<?php elseif ( 'url' === $type && ( strpos( $key, '_avatar_url' ) !== false ) ) : ?>
						<div class="ksas-media-uploader">
							<input type="url" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text ksas-media-url" />
							<button type="button" class="button ksas-media-button" data-target="<?php echo esc_attr( $key ); ?>">メディアライブラリから選択</button>
							<?php if ( ! empty( $val ) ) : ?>
								<div class="ksas-media-preview">
									<img src="<?php echo esc_url( $val ); ?>" alt="プレビュー" />
								</div>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" />
					<?php endif; ?>
					<?php if ( ! empty( $description ) ) : ?><p class="description"><?php echo wp_kses_post( $description ); ?></p><?php endif; ?>
				</td>
			</tr>
		<?php } ?>
	</table>
	<style>
		.ksas-media-uploader {
			display: flex;
			align-items: flex-start;
			gap: 10px;
			flex-wrap: wrap;
		}
		.ksas-media-uploader input.ksas-media-url {
			flex: 1;
			min-width: 300px;
		}
		.ksas-media-button {
			white-space: nowrap;
		}
		.ksas-media-preview {
			margin-top: 10px;
			width: 100%;
		}
		.ksas-media-preview img {
			max-width: 100px;
			max-height: 100px;
			border: 1px solid #ddd;
			padding: 5px;
			background: #fff;
			border-radius: 3px;
		}
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// フィールド表示切り替え機能
			function toggleProfileFields(selectedType) {
				// 全てのタイプ固有フィールドを隠す
				$('.ksas-profile-field-person').closest('tr').hide();
				$('.ksas-profile-field-organization').closest('tr').hide();
				$('.ksas-profile-field-corporation').closest('tr').hide();
				
				// 共通フィールドは常に表示
				$('.ksas-profile-field-common').closest('tr').show();
				
				// 選択されたタイプのフィールドのみ表示
				if (selectedType === 'person') {
					$('.ksas-profile-field-person').closest('tr').show();
				} else if (selectedType === 'organization') {
					$('.ksas-profile-field-organization').closest('tr').show();
				} else if (selectedType === 'corporation') {
					$('.ksas-profile-field-corporation').closest('tr').show();
				}
			}

			var initialType = $('#asd_author_type').val();
			toggleProfileFields(initialType);

			$('#asd_author_type').on('change', function() {
				toggleProfileFields($(this).val());
			});
			
			// メディアアップローダー機能
			var mediaUploader;
			
			$('.ksas-media-button').on('click', function(e) {
				e.preventDefault();
				var targetInput = $(this).data('target');
				var $targetField = $('#' + targetInput);
				var $previewDiv = $(this).siblings('.ksas-media-preview');
				
				// メディアアップローダーがまだ作成されていない場合は作成
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				
				// メディアアップローダーを作成
				mediaUploader = wp.media({
					title: '画像を選択',
					button: {
						text: '選択'
					},
					multiple: false,
					library: {
						type: 'image'
					}
				});
				
				// 画像が選択されたときの処理
				mediaUploader.on('select', function() {
					var attachment = mediaUploader.state().get('selection').first().toJSON();
					$targetField.val(attachment.url);
					
					// プレビュー画像を更新
					if ($previewDiv.length) {
						$previewDiv.find('img').attr('src', attachment.url);
					} else {
						// プレビューが存在しない場合は作成
						$('<div class="ksas-media-preview"><img src="' + attachment.url + '" alt="プレビュー" /></div>').insertAfter($targetField.parent().find('.ksas-media-button'));
					}
				});
				
				// アップローダーを開く
				mediaUploader.open();
			});
			
			// URL入力フィールドの値が変更されたときのプレビュー更新
			$('.ksas-media-url').on('change', function() {
				var $this = $(this);
				var $preview = $this.parent().find('.ksas-media-preview img');
				var newUrl = $this.val();
				
				if (newUrl && $preview.length) {
					$preview.attr('src', newUrl);
				} else if (newUrl && !$preview.length) {
					// プレビューが存在しない場合は作成
					$('<div class="ksas-media-preview"><img src="' + newUrl + '" alt="プレビュー" /></div>').insertAfter($this.parent().find('.ksas-media-button'));
				} else if (!newUrl && $preview.length) {
					// URLが空の場合はプレビューを削除
					$this.parent().find('.ksas-media-preview').remove();
				}
			});
		});
	</script>
	<?php
}
add_action( 'show_user_profile', 'ksas_profile_fields' );
add_action( 'edit_user_profile', 'ksas_profile_fields' );

// ユーザープロフィール画面でメディアライブラリスクリプトを読み込む
function ksas_admin_enqueue_media_scripts() {
	$screen = get_current_screen();
	if ( $screen && ( $screen->id === 'profile' || $screen->id === 'user-edit' ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
}
add_action( 'admin_enqueue_scripts', 'ksas_admin_enqueue_media_scripts' );

function ksas_save_profile( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) { return; }
	if ( ! isset( $_POST['ksas_profile_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ksas_profile_nonce'] ), 'ksas_save_profile_' . $user_id ) ) { return; }

	$meta_fields = [
		// 基本設定
		'asd_author_type'   => 'key',
		'asd_role_type'     => 'key',
		
		// 人物タイプ専用フィールド
		'asd_person_display_name'  => 'text',
		'asd_person_avatar_url'    => 'url',
		'asd_person_alternate_name'=> 'text',
		'asd_person_occupation'    => 'text',
		'asd_person_organization'  => 'text',
		'asd_person_contact_email' => 'email',
		'asd_person_profile_link'  => 'url',
		'asd_person_bio'           => 'textarea_strip',
		'asd_person_sns_urls'      => 'textarea_urls',
		
		// 組織タイプ専用フィールド
		'asd_organization_display_name'  => 'text',
		'asd_organization_avatar_url'    => 'url',
		'asd_organization_alternate_name'=> 'text',
		'asd_organization_contact_email' => 'email',
		'asd_organization_profile_link'  => 'url',
		'asd_organization_bio'           => 'textarea_strip',
		'asd_organization_sns_urls'      => 'textarea_urls',
		
		// 法人タイプ専用フィールド
		'asd_corporation_display_name'  => 'text',
		'asd_corporation_avatar_url'    => 'url',
		'asd_corporation_alternate_name'=> 'text',
		'asd_corporation_contact_email' => 'email',
		'asd_corporation_profile_link'  => 'url',
		'asd_corporation_bio'           => 'textarea_strip',
		'asd_corporation_sns_urls'      => 'textarea_urls',
		
		// 旧フィールドとの互換性（後で削除予定）
		'asd_display_name'  => 'text',
		'asd_avatar_url'    => 'url',
		'asd_alternate_name'=> 'text',
		'asd_occupation'    => 'text',
		'asd_organization'  => 'text',
		'asd_contact_email' => 'email',
		'asd_profile_link'  => 'url',
		'asd_bio'           => 'textarea_strip',
		'asd_sns_urls'      => 'textarea_urls',
	];

	foreach ( $meta_fields as $key => $type ) {
		if ( array_key_exists( $key, $_POST ) ) {
			$raw_value = wp_unslash( $_POST[ $key ] );
			$sanitized_value = '';

			switch ( $type ) {
				case 'url':
					$sanitized_value = function_exists('ksas_normalize_url') ? ksas_normalize_url( $raw_value ) : esc_url_raw( trim( $raw_value ) );
					break;
				case 'email':
					$sanitized_value = sanitize_email( $raw_value );
					break;
				case 'textarea_strip':
					$sanitized_value = wp_strip_all_tags( $raw_value );
					break;
				case 'textarea_urls':
					$lines = explode( "\n", $raw_value );
					$trimmed_lines = array_map( 'trim', $lines );
					$normalized_urls = function_exists('ksas_normalize_url') ? array_map( 'ksas_normalize_url', $trimmed_lines ) : array_map( 'esc_url_raw', $trimmed_lines );
					$valid_urls = array_filter( $normalized_urls );
					$sanitized_value = implode( "\n", $valid_urls );
					break;
				case 'key':
					$allowed_keys = [];
					$default_key = '';
					if ( $key === 'asd_author_type' ) {
						$allowed_keys = ['person', 'organization', 'corporation'];
						$default_key = 'person';
					} elseif ( $key === 'asd_role_type' ) {
						$allowed_keys = ['author','supervisor','admin'];
						$default_key = 'author';
					}
					$sanitized_value = in_array( $raw_value, $allowed_keys, true ) ? $raw_value : $default_key;
					break;
				case 'text':
				default:
					$sanitized_value = sanitize_text_field( $raw_value );
					break;
			}
			update_user_meta( $user_id, $key, $sanitized_value );
		}
	}
}
add_action( 'personal_options_update', 'ksas_save_profile' );
add_action( 'edit_user_profile_update', 'ksas_save_profile' );