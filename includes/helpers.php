<?php
/**
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( string $haystack, string $needle ): bool {
		return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
	}
}

function ksas_normalize_url( $url ): string {
	if ( ! is_string( $url ) ) {
		return '';
	}
	$url = trim( $url );
	if ( empty( $url ) ) {
		return '';
	}
	$url = str_replace( '％', '%', $url );
	$decoded_url = rawurldecode( $url );

	if ( ! preg_match( '#^([a-zA-Z][a-zA-Z0-9+.\-]*):#', $decoded_url ) ) {
		if ( strpos( $decoded_url, '//' ) === 0 ) {
			$decoded_url = 'https:' . $decoded_url;
		} elseif ( preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}/', $decoded_url) ) {
			$decoded_url = 'https://' . $decoded_url;
		}
	}
	return esc_url_raw( $decoded_url );
}

function ksas_sns_icon_class( string $url ): string {
	if ( empty( $url ) ) {
		return 'dashicons-admin-site';
	}
	$host = strtolower( wp_parse_url( $url, PHP_URL_HOST ) ?: '' );
	$host_without_www = preg_replace('/^www\./', '', $host);

	$icon_map = [
		'facebook.com'          => 'dashicons-facebook-alt',
		'x.com'                 => 'dashicons-twitter-alt',
		'twitter.com'           => 'dashicons-twitter-alt',
		'instagram.com'         => 'dashicons-instagram',
		'linkedin.com'          => 'dashicons-linkedin',
		'youtube.com'           => 'dashicons-youtube',
		'youtu.be'              => 'dashicons-youtube',
		'pinterest.'            => 'dashicons-pinterest',
		'tumblr.com'            => 'dashicons-tumblr',
		'reddit.com'            => 'dashicons-reddit',
		'tiktok.com'            => 'dashicons-format-video',
		'snapchat.com'          => 'dashicons-camera-alt',
		'whatsapp.com'          => 'dashicons-whatsapp',
		'wa.me'                 => 'dashicons-whatsapp',
		't.me'                  => 'dashicons-telegram',
		'telegram.org'          => 'dashicons-telegram',
        'telegram.me'           => 'dashicons-telegram',
		'discord.com'           => 'dashicons-format-chat',
		'discord.gg'            => 'dashicons-format-chat',
		'threads.net'           => 'dashicons-format-status',
		'behance.net'           => 'dashicons-portfolio',
		'dribbble.com'          => 'dashicons-admin-customizer',
		'github.com'            => 'dashicons-editor-code',
		'gist.github.com'       => 'dashicons-editor-code',
		'gitlab.com'            => 'dashicons-editor-code',
		'bitbucket.org'         => 'dashicons-editor-code',
		'stackoverflow.com'     => 'dashicons-editor-help',
		'stackexchange.com'     => 'dashicons-editor-help',
		'medium.com'            => 'dashicons-edit',
		'slideshare.net'        => 'dashicons-slides',
		'flickr.com'            => 'dashicons-camera',
		'vimeo.com'             => 'dashicons-vimeo',
		'soundcloud.com'        => 'dashicons-format-audio',
		'spotify.com'           => 'dashicons-spotify',
		'last.fm'               => 'dashicons-format-audio',
		'bandcamp.com'          => 'dashicons-format-audio',
		'deviantart.com'        => 'dashicons-art',
		'researchgate.net'      => 'dashicons-welcome-learn-more',
		'academia.edu'          => 'dashicons-welcome-learn-more',
		'kaggle.com'            => 'dashicons-chart-bar',
		'goodreads.com'         => 'dashicons-book-alt',
		'letterboxd.com'        => 'dashicons-format-video',
		'note.com'              => 'dashicons-welcome-write-blog',
        'note.mu'               => 'dashicons-welcome-write-blog',
		'qiita.com'             => 'dashicons-lightbulb',
		'zenn.dev'              => 'dashicons-lightbulb',
		'hatenablog.com'        => 'dashicons-edit',
        'hatenablog.jp'         => 'dashicons-edit',
        'b.hatena.ne.jp'        => 'dashicons-admin-links',
		'line.me'               => 'dashicons-format-chat',
        'line.blog'             => 'dashicons-format-chat',
        'line.naver.jp'         => 'dashicons-format-chat',
		'mixi.jp'               => 'dashicons-groups',
		'weibo.com'             => 'dashicons-share',
		'weibo.cn'              => 'dashicons-share',
		'qq.com'                => 'dashicons-format-chat',
		'qzone.qq.com'          => 'dashicons-format-gallery',
		'wechat.com'            => 'dashicons-format-chat',
		'douyin.com'            => 'dashicons-format-video',
		'kuaishou.com'          => 'dashicons-format-video',
		'mastodon.'             => 'dashicons-share',
		'misskey.'              => 'dashicons-share-alt2',
		'gettr.com'             => 'dashicons-format-status',
		'truthsocial.com'       => 'dashicons-format-status',
		'gab.com'               => 'dashicons-format-status',
		'mewe.com'              => 'dashicons-groups',
		'parler.com'            => 'dashicons-format-status',
		'minds.com'             => 'dashicons-lightbulb',
		'twitch.tv'             => 'dashicons-video-alt',
		'viber.com'             => 'dashicons-format-chat',
		'skype.com'             => 'dashicons-format-chat',
        'teams.microsoft.com'   => 'dashicons-groups',
		'zoom.us'               => 'dashicons-video-alt3',
		'clubhouse.com'         => 'dashicons-microphone',
		'signal.org'            => 'dashicons-lock',
		'patreon.com'           => 'dashicons-money-alt',
		'pixiv.net'             => 'dashicons-admin-customizer',
		'etsy.com'              => 'dashicons-cart',
		'amazon.'               => 'dashicons-cart',
		'imdb.com'              => 'dashicons-format-video',
		'wikipedia.org'         => 'dashicons-book',
        'ja.wikipedia.org'      => 'dashicons-book',
        'wordpress.org'         => 'dashicons-wordpress',
        'wordpress.com'         => 'dashicons-wordpress',
        'profiles.wordpress.org' => 'dashicons-wordpress',
        'scholar.google.com'    => 'dashicons-welcome-learn-more',
        'scholar.google.co.jp'  => 'dashicons-welcome-learn-more',
		'soundcloud.app.goo.gl' => 'dashicons-format-audio',
		'spotify.link'          => 'dashicons-spotify',
		'fb.watch'              => 'dashicons-facebook-alt',
        'fb.me'                 => 'dashicons-facebook-alt',
		'instagr.am'            => 'dashicons-instagram',
        'bit.ly'                => 'dashicons-admin-links',
        'is.gd'                 => 'dashicons-admin-links',
        'tinyurl.com'           => 'dashicons-admin-links',
        'ow.ly'                 => 'dashicons-admin-links',
        'buff.ly'               => 'dashicons-admin-links',
        'feedly.com'            => 'dashicons-rss',
        'bloglovin.com'         => 'dashicons-heart',
	];

	if ( isset( $icon_map[ $host ] ) ) {
		return $icon_map[ $host ];
	}
	if ( isset( $icon_map[ $host_without_www ] ) ) {
		return $icon_map[ $host_without_www ];
	}

	foreach ( $icon_map as $domain_start => $icon_class ) {
		if ( str_ends_with( $domain_start, '.' ) ) {
            $check_domain = rtrim($domain_start, '.');
			if ( $host === $check_domain || strpos( $host, $domain_start ) === 0 ) {
				return $icon_class;
			}
            if ( $host_without_www === $check_domain || strpos( $host_without_www, $domain_start ) === 0 ) {
				return $icon_class;
			}
		}
	}

	if (strpos($url, 'skype:') === 0) {
		return 'dashicons-format-chat';
	}
	if (strpos($url, 'weixin:') === 0) {
		return 'dashicons-format-chat';
	}
    if (strpos($url, 'mailto:') === 0) {
		return 'dashicons-email-alt';
	}
    if (preg_match('#(/feed|/rss|/atom)(\.xml)?/?$#i', $url) || preg_match('/feed=(rss|atom|rss2)/i', $url)) {
        return 'dashicons-rss';
    }

	return 'dashicons-admin-site';
}

function ksas_role_icon_class( string $role ): string {
	switch ( $role ) {
		case 'admin':
			return 'dashicons-shield-alt';
		case 'supervisor':
			return 'dashicons-visibility';
		case 'author':
		default:
			return 'dashicons-edit';
	}
}

function ksas_available_link_props(): array {
	return [
		'author'            => 'author',
		'editor'            => 'editor',
		'contributor'       => 'contributor',
		'creator'           => 'creator',
		'provider'          => 'provider',
		'publisher'         => 'publisher',
		'translator'        => 'translator',
		'reviewedBy'        => 'reviewedBy',
		'accountablePerson' => 'accountablePerson',
		'copyrightHolder'   => 'copyrightHolder',
	];
}

function ksas_get_author_data_by_type( int $user_id ): array {
	$author_type = get_user_meta( $user_id, 'asd_author_type', true ) ?: 'person';
	$name_default = get_the_author_meta( 'display_name', $user_id ) ?: get_bloginfo( 'name' );
	
	// 共通データ
	$data = [
		'author_type' => $author_type,
		'role' => get_user_meta( $user_id, 'asd_role_type', true ) ?: 'author',
	];
	
	// 著者タイプ別のデータを取得（新しいフィールド優先、なければ旧フィールドから取得）
	switch ( $author_type ) {
		case 'person':
			$data['display_name'] = get_user_meta( $user_id, 'asd_person_display_name', true ) 
									?: get_user_meta( $user_id, 'asd_display_name', true ) 
									?: $name_default;
			$data['avatar'] = get_user_meta( $user_id, 'asd_person_avatar_url', true ) 
							 ?: get_user_meta( $user_id, 'asd_avatar_url', true );
			$data['alternate'] = get_user_meta( $user_id, 'asd_person_alternate_name', true ) 
								?: get_user_meta( $user_id, 'asd_alternate_name', true );
			$data['occupation'] = get_user_meta( $user_id, 'asd_person_occupation', true ) 
								 ?: get_user_meta( $user_id, 'asd_occupation', true );
			$data['org'] = get_user_meta( $user_id, 'asd_person_organization', true ) 
						  ?: get_user_meta( $user_id, 'asd_organization', true );
			$data['email'] = get_user_meta( $user_id, 'asd_person_contact_email', true ) 
							?: get_user_meta( $user_id, 'asd_contact_email', true );
			$data['profile'] = get_user_meta( $user_id, 'asd_person_profile_link', true ) 
							  ?: get_user_meta( $user_id, 'asd_profile_link', true );
			$data['bio'] = get_user_meta( $user_id, 'asd_person_bio', true ) 
						  ?: get_user_meta( $user_id, 'asd_bio', true );
			$data['sns_raw'] = get_user_meta( $user_id, 'asd_person_sns_urls', true ) 
							  ?: get_user_meta( $user_id, 'asd_sns_urls', true );
			break;
			
		case 'organization':
			$data['display_name'] = get_user_meta( $user_id, 'asd_organization_display_name', true ) 
									?: get_user_meta( $user_id, 'asd_display_name', true ) 
									?: $name_default;
			$data['avatar'] = get_user_meta( $user_id, 'asd_organization_avatar_url', true ) 
							 ?: get_user_meta( $user_id, 'asd_avatar_url', true );
			$data['alternate'] = get_user_meta( $user_id, 'asd_organization_alternate_name', true ) 
								?: get_user_meta( $user_id, 'asd_alternate_name', true );
			$data['occupation'] = ''; // 組織には職業はなし
			$data['org'] = ''; // 組織自体なので所属組織はなし
			$data['email'] = get_user_meta( $user_id, 'asd_organization_contact_email', true ) 
							?: get_user_meta( $user_id, 'asd_contact_email', true );
			$data['profile'] = get_user_meta( $user_id, 'asd_organization_profile_link', true ) 
							  ?: get_user_meta( $user_id, 'asd_profile_link', true );
			$data['bio'] = get_user_meta( $user_id, 'asd_organization_bio', true ) 
						  ?: get_user_meta( $user_id, 'asd_bio', true );
			$data['sns_raw'] = get_user_meta( $user_id, 'asd_organization_sns_urls', true ) 
							  ?: get_user_meta( $user_id, 'asd_sns_urls', true );
			break;
			
		case 'corporation':
			$data['display_name'] = get_user_meta( $user_id, 'asd_corporation_display_name', true ) 
									?: get_user_meta( $user_id, 'asd_display_name', true ) 
									?: $name_default;
			$data['avatar'] = get_user_meta( $user_id, 'asd_corporation_avatar_url', true ) 
							 ?: get_user_meta( $user_id, 'asd_avatar_url', true );
			$data['alternate'] = get_user_meta( $user_id, 'asd_corporation_alternate_name', true ) 
								?: get_user_meta( $user_id, 'asd_alternate_name', true );
			$data['occupation'] = ''; // 法人には職業はなし
			$data['org'] = ''; // 法人自体なので所属組織はなし
			$data['email'] = get_user_meta( $user_id, 'asd_corporation_contact_email', true ) 
							?: get_user_meta( $user_id, 'asd_contact_email', true );
			$data['profile'] = get_user_meta( $user_id, 'asd_corporation_profile_link', true ) 
							  ?: get_user_meta( $user_id, 'asd_profile_link', true );
			$data['bio'] = get_user_meta( $user_id, 'asd_corporation_bio', true ) 
						  ?: get_user_meta( $user_id, 'asd_bio', true );
			$data['sns_raw'] = get_user_meta( $user_id, 'asd_corporation_sns_urls', true ) 
							  ?: get_user_meta( $user_id, 'asd_sns_urls', true );
			break;
			
		default:
			// デフォルトは person として扱う
			$data['display_name'] = get_user_meta( $user_id, 'asd_display_name', true ) ?: $name_default;
			$data['avatar'] = get_user_meta( $user_id, 'asd_avatar_url', true );
			$data['alternate'] = get_user_meta( $user_id, 'asd_alternate_name', true );
			$data['occupation'] = get_user_meta( $user_id, 'asd_occupation', true );
			$data['org'] = get_user_meta( $user_id, 'asd_organization', true );
			$data['email'] = get_user_meta( $user_id, 'asd_contact_email', true );
			$data['profile'] = get_user_meta( $user_id, 'asd_profile_link', true );
			$data['bio'] = get_user_meta( $user_id, 'asd_bio', true );
			$data['sns_raw'] = get_user_meta( $user_id, 'asd_sns_urls', true );
			break;
	}
	
	// SNS URLsを配列に変換
	$data['sns'] = array_filter( array_map( 'trim', explode( "\n", $data['sns_raw'] ) ) );

	return $data;
}

/**
 * 投稿タイプがthe_content()を使用しているかチェック
 * テンプレートファイルを静的解析し、template_redirectフックもチェックする
 *
 * @param string $post_type 投稿タイプ名
 * @return bool the_content()を使用していればtrue
 */
function ksas_post_type_uses_the_content( string $post_type ): bool {
	static $cache = [];

	// キャッシュチェック
	if ( isset( $cache[ $post_type ] ) ) {
		return $cache[ $post_type ];
	}

	// 開発者がフィルターで強制的に除外可能
	$forced_exclusion = apply_filters( 'ksas_force_exclude_post_type', false, $post_type );
	if ( $forced_exclusion ) {
		$cache[ $post_type ] = false;
		return false;
	}

	// template_redirectフックで独自ルーティングを使用しているかチェック
	if ( ksas_has_custom_template_redirect( $post_type ) ) {
		$cache[ $post_type ] = false;
		return false;
	}

	// プラグインが single_template フィルターでテンプレートを置き換えているかチェック
	$plugin_template = ksas_get_plugin_template_for_post_type( $post_type );
	if ( $plugin_template ) {
		$result = ksas_check_template_for_the_content( $plugin_template, $post_type );
		$cache[ $post_type ] = $result;
		return $result;
	}

	// テンプレート階層に従ってテンプレートファイルを探す
	$template_hierarchy = [
		"single-{$post_type}.php",
		'single.php',
		'singular.php',
		'index.php',
	];

	$template_dir = get_stylesheet_directory();
	$parent_template_dir = get_template_directory();

	foreach ( $template_hierarchy as $template_file ) {
		// 子テーマをチェック
		$file_path = $template_dir . '/' . $template_file;
		if ( file_exists( $file_path ) ) {
			$result = ksas_check_template_for_the_content( $file_path, $post_type );
			$cache[ $post_type ] = $result;
			return $result;
		}

		// 親テーマをチェック（子テーマと異なる場合のみ）
		if ( $template_dir !== $parent_template_dir ) {
			$file_path = $parent_template_dir . '/' . $template_file;
			if ( file_exists( $file_path ) ) {
				$result = ksas_check_template_for_the_content( $file_path, $post_type );
				$cache[ $post_type ] = $result;
				return $result;
			}
		}
	}

	// テンプレートが見つからない場合はtrueを返す（デフォルトでindex.phpが使われる想定）
	$cache[ $post_type ] = true;
	return true;
}

/**
 * プラグインが single_template フィルターで提供しているテンプレートを取得
 *
 * @param string $post_type 投稿タイプ名
 * @return string|false テンプレートファイルパス、なければfalse
 */
function ksas_get_plugin_template_for_post_type( string $post_type ) {
	global $wp_filter;

	if ( ! isset( $wp_filter['single_template'] ) ) {
		return false;
	}

	// single_template フィルターに登録されている全ての関数を調べる
	foreach ( $wp_filter['single_template']->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			try {
				$function = $callback['function'];

				// Reflectionで関数の情報を取得
				if ( is_array( $function ) && is_object( $function[0] ) ) {
					$reflection = new ReflectionMethod( $function[0], $function[1] );
				} elseif ( is_array( $function ) ) {
					$reflection = new ReflectionMethod( $function[0], $function[1] );
				} elseif ( is_string( $function ) ) {
					$reflection = new ReflectionFunction( $function );
				} elseif ( $function instanceof Closure ) {
					$reflection = new ReflectionFunction( $function );
				} else {
					continue;
				}

				$file_path = $reflection->getFileName();
				if ( ! $file_path || strpos( $file_path, WP_PLUGIN_DIR ) !== 0 ) {
					continue;
				}

				// 関数の全ソースコードを取得
				$start_line = $reflection->getStartLine();
				$end_line = $reflection->getEndLine();
				$file_lines = file( $file_path );
				$function_code = implode( '', array_slice( $file_lines, $start_line - 1, $end_line - $start_line + 1 ) );

				// この投稿タイプに関する条件分岐があるかチェック（様々なパターンに対応）
				$type_check_patterns = [
					preg_quote( $post_type, '/' ),
				];

				$has_type_check = false;
				foreach ( $type_check_patterns as $pattern ) {
					if ( preg_match( '/' . $pattern . '/', $function_code ) ) {
						$has_type_check = true;
						break;
					}
				}

				if ( ! $has_type_check ) {
					continue;
				}

				// 関数内で定義されている全ての定数を見つけて、それらの値から .php ファイルパスを構築
				// 全ての定数を取得
				$all_constants = get_defined_constants( true );
				$user_constants = isset( $all_constants['user'] ) ? $all_constants['user'] : [];

				// 関数内で使用されている定数を探す
				preg_match_all( '/\b([A-Z_][A-Z0-9_]*)\b/', $function_code, $const_in_code );
				$constants_in_function = array_unique( $const_in_code[1] );

				// プラグインディレクトリに関連する定数を探して、PHPファイルパスを構築
				foreach ( $constants_in_function as $const_name ) {
					if ( ! defined( $const_name ) ) {
						continue;
					}

					$const_value = constant( $const_name );
					if ( ! is_string( $const_value ) || strpos( $const_value, WP_PLUGIN_DIR ) !== 0 ) {
						continue;
					}

					// この定数値をベースに、関数内で連結されている文字列を探す
					preg_match_all( '/' . preg_quote( $const_name, '/' ) . '\s*\.\s*[\'"]([^\'"]+\.php)[\'"]/', $function_code, $path_matches );

					foreach ( $path_matches[1] as $relative_path ) {
						$full_path = $const_value . $relative_path;
						if ( file_exists( $full_path ) ) {
							return $full_path;
						}
					}
				}

			} catch ( Exception $e ) {
				continue;
			}
		}
	}

	return false;
}

/**
 * 投稿タイプがtemplate_redirectで独自ルーティングを使用しているかチェック
 *
 * @param string $post_type 投稿タイプ名
 * @return bool 独自ルーティングを使用していればtrue
 */
function ksas_has_custom_template_redirect( string $post_type ): bool {
	global $wp_filter;

	// template_redirectフックが存在しない場合はfalse
	if ( ! isset( $wp_filter['template_redirect'] ) ) {
		return false;
	}

	// アクティブなプラグインのディレクトリを取得
	$plugin_dirs = [];
	$active_plugins = get_option( 'active_plugins', [] );
	foreach ( $active_plugins as $plugin ) {
		$plugin_dir = dirname( WP_PLUGIN_DIR . '/' . $plugin );
		$plugin_dirs[] = $plugin_dir;
	}

	// template_redirectフックに登録されている全ての関数をチェック
	foreach ( $wp_filter['template_redirect']->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			// リフレクションで関数の定義場所を取得
			try {
				if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
					$reflection = new ReflectionMethod( $callback['function'][0], $callback['function'][1] );
				} elseif ( is_array( $callback['function'] ) ) {
					$reflection = new ReflectionMethod( $callback['function'][0], $callback['function'][1] );
				} elseif ( is_string( $callback['function'] ) ) {
					$reflection = new ReflectionFunction( $callback['function'] );
				} elseif ( $callback['function'] instanceof Closure ) {
					$reflection = new ReflectionFunction( $callback['function'] );
				} else {
					continue;
				}

				$file_path = $reflection->getFileName();
				if ( ! $file_path ) {
					continue;
				}

				// プラグインディレクトリ内の関数かチェック
				$is_plugin_function = false;
				foreach ( $plugin_dirs as $plugin_dir ) {
					if ( strpos( $file_path, $plugin_dir ) === 0 ) {
						$is_plugin_function = true;
						break;
					}
				}

				if ( ! $is_plugin_function ) {
					continue;
				}

				// 関数のソースコードを取得
				$start_line = $reflection->getStartLine();
				$end_line = $reflection->getEndLine();
				$file_lines = file( $file_path );
				$function_code = implode( '', array_slice( $file_lines, $start_line - 1, $end_line - $start_line + 1 ) );

				// 投稿タイプ名のチェックをより厳密に
				// 様々なパターンをチェック
				$escaped_post_type = preg_quote( $post_type, '/' );
				$post_type_patterns = [
					'/[\'"]post_type[\'"]\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
					'/\$_GET\[[\'"]post_type[\'"]\]\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
					'/\$_POST\[[\'"]post_type[\'"]\]\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
					'/\$post_type\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
					'/get_post_type\(\)\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
					'/post->post_type\s*===?\s*[\'"]' . $escaped_post_type . '[\'"]/i',
				];

				$has_post_type_check = false;
				foreach ( $post_type_patterns as $pattern ) {
					if ( preg_match( $pattern, $function_code ) ) {
						$has_post_type_check = true;
						break;
					}
				}

				// 投稿タイプチェックとexit/dieの両方が存在する場合のみtrue
				if ( $has_post_type_check && ( stripos( $function_code, 'exit' ) !== false || stripos( $function_code, 'die' ) !== false ) ) {
					return true;
				}

			} catch ( ReflectionException $e ) {
				// リフレクションエラーは無視
				continue;
			}
		}
	}

	return false;
}

/**
 * テンプレートファイル内にthe_content()が含まれているかチェック
 *
 * @param string $file_path テンプレートファイルのパス
 * @param string $post_type 投稿タイプ名
 * @return bool the_content()が含まれていればtrue
 */
function ksas_check_template_for_the_content( string $file_path, string $post_type = '' ): bool {
	static $checked_files = [];

	// 無限ループ防止
	if ( isset( $checked_files[ $file_path ] ) ) {
		return $checked_files[ $file_path ];
	}

	// ファイルサイズが大きすぎる場合はスキップ（1MB以上）
	if ( filesize( $file_path ) > 1048576 ) {
		$checked_files[ $file_path ] = true;
		return true; // 安全のためtrueを返す
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- テンプレート解析のため必要
	$content = file_get_contents( $file_path );
	if ( $content === false ) {
		$checked_files[ $file_path ] = true;
		return true; // 読み込めない場合は安全のためtrueを返す
	}

	// the_content() の使用をチェック
	if ( preg_match( '/\bthe_content\s*\(/', $content ) ) {
		$checked_files[ $file_path ] = true;
		return true;
	}

	// get_template_part() で読み込まれる可能性のある部分テンプレートもチェック
	if ( preg_match_all( '/get_template_part\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches ) ) {
		$template_dir = dirname( $file_path );
		foreach ( $matches[1] as $partial_template ) {
			$partial_path = $template_dir . '/' . $partial_template . '.php';
			if ( file_exists( $partial_path ) ) {
				if ( ksas_check_template_for_the_content( $partial_path, $post_type ) ) {
					$checked_files[ $file_path ] = true;
					return true;
				}
			}
		}
	}

	// require, include で読み込まれるファイルもチェック
	if ( preg_match_all( '/(?:require|include)(?:_once)?\s*[(\s]+[\'"]([^\'"]+\.php)[\'"]/', $content, $matches ) ) {
		$template_dir = dirname( $file_path );
		foreach ( $matches[1] as $included_file ) {
			// 相対パスの場合のみチェック（絶対パスやWordPressコアファイルは除外）
			if ( strpos( $included_file, '/' ) !== 0 && strpos( $included_file, ABSPATH ) !== 0 ) {
				$included_path = $template_dir . '/' . $included_file;
				if ( file_exists( $included_path ) ) {
					if ( ksas_check_template_for_the_content( $included_path, $post_type ) ) {
						$checked_files[ $file_path ] = true;
						return true;
					}
				}
			}
		}
	}

	$checked_files[ $file_path ] = false;
	return false;
}