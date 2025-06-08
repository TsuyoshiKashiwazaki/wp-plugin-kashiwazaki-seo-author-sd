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
			'asd_display_name'  => [ '表示名 / 組織名', 'text', '著者ボックスとスキーマの`name`に使用されます。空の場合はWordPressの表示名が使用されます。' ],
			'asd_avatar_url'    => [ 'プロフィール画像 / ロゴ URL', 'url', '著者の画像または組織ロゴのURL（推奨：正方形、112x112px以上）。空の場合はGravatar/WordPressアバターが使用されます。' ],
			'asd_role_type'     => [ '役割種別', 'select', 'このユーザー/組織の主な役割（例：執筆者、監修者）を選択します。バッジ表示やスキーマに影響します。' ],
			'asd_alternate_name'=> [ '別名 / 代替名', 'text', 'スキーマの`alternateName`に使用されます。（例：ペンネーム、旧組織名など）' ],
			'asd_occupation'    => [ '職業/肩書き', 'text', '【人物タイプのみ】著者ボックスとスキーマの`jobTitle`に使用されます。', 'person' ],
			'asd_organization'  => [ '所属組織', 'text', '【人物タイプのみ】スキーマの`worksFor`に使用されます。', 'person' ],
			'asd_contact_email' => [ '連絡先メールアドレス（公開用）', 'email', '連絡用の公開メールアドレス。メールアイコンのリンクとスキーマの`email`に使用されます。' ],
			'asd_profile_link'  => [ 'プロフィールリンク（著者ページ、公式サイト等）', 'url', '著者/組織のメインプロフィールやウェブサイトへのリンク。画像/名前のリンクとスキーマの`url`/`@id`に使用されます。' ],
		];

		foreach ( $fields as $key => $info ) {
			list( $label, $type, $description, $visibility ) = array_pad( $info, 4, null );
			$val = get_user_meta( $user->ID, $key, true );

			$row_style = '';
			if ( $visibility === 'person' && $current_author_type !== 'person' ) {
				$row_style = ' style="display: none;"';
			} elseif ( $visibility === 'org' && $current_author_type === 'person' ) {
				$row_style = ' style="display: none;"';
			}
			$row_class = '';
			if ($visibility === 'person') { $row_class = ' class="ksas-profile-field-person"'; }
			elseif ($visibility === 'org') { $row_class = ' class="ksas-profile-field-org"'; }

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
					<?php else : ?>
						<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" />
					<?php endif; ?>
					<?php if ( ! empty( $description ) ) : ?><p class="description"><?php echo wp_kses_post( $description ); ?></p><?php endif; ?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<th><label for="asd_bio">プロフィール文 / 組織概要</label></th>
			<td><textarea name="asd_bio" id="asd_bio" rows="5" class="large-text"><?php echo esc_textarea( get_user_meta( $user->ID, 'asd_bio', true ) ); ?></textarea>
				<p class="description">簡単な自己紹介や組織の概要。著者ボックスとスキーマの`description`に使用されます。HTMLタグは除去されます。</p></td>
		</tr>
		<tr>
			<th><label for="asd_sns_urls">関連リンク（SNS・ウェブサイト等、1行に1つ）</label></th>
			<td><textarea name="asd_sns_urls" id="asd_sns_urls" rows="5" class="large-text" placeholder="<?php echo esc_attr("https://twitter.com/your_account\nhttps://your-corp.com/\nhttps://facebook.com/your_page"); ?>"><?php echo esc_textarea( get_user_meta( $user->ID, 'asd_sns_urls', true ) ); ?></textarea>
				<p class="description">関連するURLを1行に1つずつ入力します。アイコンリンクとスキーマの`sameAs`に使用されます。</p></td>
		</tr>
	</table>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			function toggleProfileFields(selectedType) {
				if (selectedType === 'person') {
					$('.ksas-profile-field-person').closest('tr').show();
				} else {
					$('.ksas-profile-field-person').closest('tr').hide();
				}

                $('label[for="asd_display_name"]').text(selectedType === 'person' ? '表示名' : '組織名 / 法人名');
                $('label[for="asd_avatar_url"]').text(selectedType === 'person' ? '顔写真/プロフィール画像 URL' : 'ロゴ画像 URL');
                $('label[for="asd_profile_link"]').text(selectedType === 'person' ? 'プロフィールリンク（著者ページ、ウェブサイト等）' : '公式サイト URL');
                $('label[for="asd_bio"]').text(selectedType === 'person' ? 'プロフィール文' : '組織概要 / 事業内容');
                $('label[for="asd_sns_urls"]').text(selectedType === 'person' ? 'SNS・ウェブサイトURL（1行に1つ）' : '関連リンク（公式サイト、SNS等、1行に1つ）');
                $('label[for="asd_alternate_name"]').text(selectedType === 'person' ? '別名（ペンネーム、ニックネーム等）' : '代替名（略称、旧組織名など）');
                $('label[for="asd_occupation"]').text('職業/肩書き');
                $('label[for="asd_organization"]').text('所属組織');

                if (selectedType === 'person') {
                    $('.ksas-profile-field-person').closest('tr').css('display', 'table-row');
                }
			}

			var initialType = $('#asd_author_type').val();
			toggleProfileFields(initialType);

			$('#asd_author_type').on('change', function() {
				toggleProfileFields($(this).val());
			});
		});
	</script>
	<?php
}
add_action( 'show_user_profile', 'ksas_profile_fields' );
add_action( 'edit_user_profile', 'ksas_profile_fields' );

function ksas_save_profile( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) { return; }
	if ( ! isset( $_POST['ksas_profile_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ksas_profile_nonce'] ), 'ksas_save_profile_' . $user_id ) ) { return; }

	$meta_fields = [
		'asd_author_type'   => 'key',
		'asd_display_name'  => 'text',
		'asd_avatar_url'    => 'url',
		'asd_role_type'     => 'key',
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