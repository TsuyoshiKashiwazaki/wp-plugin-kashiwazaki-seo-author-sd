<?php
/**
 * @package Kashiwazaki_Seo_Author_Schema_Display
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once KSAS_ASD_PATH . 'includes/helpers.php';

function ksas_render_author_html( int $uid ): string {
	$author_type_raw = get_user_meta( $uid, 'asd_author_type', true ) ?: 'person';
    switch ($author_type_raw) {
        case 'corporation':
            $schema_type_url = 'https://schema.org/Corporation';
            $author_type = 'corporation';
            break;
        case 'organization':
            $schema_type_url = 'https://schema.org/Organization';
             $author_type = 'organization';
            break;
        case 'person':
        default:
            $schema_type_url = 'https://schema.org/Person';
            $author_type = 'person';
            break;
    }

	$name_default = get_the_author_meta( 'display_name', $uid ) ?: get_bloginfo( 'name' );
	$meta = [
		'display_name' => get_user_meta( $uid, 'asd_display_name', true ) ?: $name_default,
		'avatar'       => get_user_meta( $uid, 'asd_avatar_url', true ),
		'role'         => get_user_meta( $uid, 'asd_role_type', true ) ?: 'author',
		'alternate'    => get_user_meta( $uid, 'asd_alternate_name', true ),
		'occupation'   => ( $author_type === 'person' ) ? get_user_meta( $uid, 'asd_occupation', true ) : '',
		'org'          => ( $author_type === 'person' ) ? get_user_meta( $uid, 'asd_organization', true ) : '',
		'email'        => get_user_meta( $uid, 'asd_contact_email', true ),
		'profile'      => get_user_meta( $uid, 'asd_profile_link', true ),
		'bio'          => get_user_meta( $uid, 'asd_bio', true ),
		'sns_raw'      => get_user_meta( $uid, 'asd_sns_urls', true ),
	];
	$meta['sns'] = array_filter( array_map( 'trim', explode( "\n", $meta['sns_raw'] ) ) );
	if ( empty( $meta['display_name'] ) ) { return ''; }

	$avatar_img = '';
	$image_prop = ( $author_type === 'person' ) ? 'image' : 'logo';
	if ( ! empty( $meta['avatar'] ) ) {
		$avatar_url = ksas_normalize_url( $meta['avatar'] );
		if ( $avatar_url ) {
			$avatar_img = '<img src="' . esc_url( $avatar_url ) . '" alt="' . esc_attr( $meta['display_name'] ) . '" class="ksas-avatar" itemprop="' . esc_attr( $image_prop ) . '" loading="lazy" decoding="async" width="80" height="80" />';
		}
	}
	if ( empty( $avatar_img ) && $author_type === 'person' ) {
		$avatar_img = get_avatar( $uid, 80, '', $meta['display_name'], [ 'class' => 'ksas-avatar', 'loading' => 'lazy', 'decoding' => 'async' ] );
	}

	$profile_link = ksas_normalize_url($meta['profile']);
	$link_rel = ( $author_type === 'person' ) ? 'author noopener noreferrer' : 'noopener noreferrer';
	$aria_label_profile = sprintf( '%sの%s', $meta['display_name'], ( $author_type === 'person' ? 'プロフィールページへ' : '公式サイトへ' ) );
	$avatar_html = $profile_link ? '<a href="' . esc_url( $profile_link ) . '" target="_blank" rel="' . esc_attr( $link_rel ) . '" aria-label="'.esc_attr( $aria_label_profile ).'">' . $avatar_img . '</a>' : $avatar_img;
	$name_html = $profile_link ? '<a href="' . esc_url( $profile_link ) . '" class="ksas-name-link" target="_blank" rel="' . esc_attr( $link_rel ) . '">' . esc_html( $meta['display_name'] ) . '</a>' : esc_html( $meta['display_name'] );

	$role_labels = [ 'author'=>'執筆者','supervisor'=>'監修者','admin'=>'管理者' ];
	$role_label = $role_labels[ $meta['role'] ] ?? '執筆者';
	$role_badge = '<span class="ksas-role-badge"><span class="dashicons ' . esc_attr( ksas_role_icon_class( $meta['role'] ) ) . '" aria-hidden="true"></span>' . esc_html( $role_label ) . '</span>';

	$aria_label_section = sprintf('%s %s', $meta['display_name'], ($author_type === 'person' ? 'プロフィール' : ($author_type === 'corporation' ? '法人情報' : '組織情報')));
	$aria_label_links = sprintf('%s 関連リンク', $meta['display_name']);
	$occupation_prop = ($author_type === 'person') ? 'jobTitle' : '';
	$bio_prop = 'description';
	$url_prop = 'url';
	$email_prop = 'email';
	$sameas_prop = 'sameAs';

	ob_start();
	?>
	<section class="ksas-author-status" itemscope itemtype="<?php echo esc_attr( $schema_type_url ); ?>" aria-label="<?php echo esc_attr( $aria_label_section ); ?>">
		<?php if ($profile_link): ?><meta itemprop="<?php echo esc_attr($url_prop); ?>" content="<?php echo esc_url($profile_link); ?>" /><?php endif; ?>
		<div class="ksas-header">
			<?php echo $avatar_html; ?>
			<div class="ksas-central">
				<?php if ( $author_type === 'person' && ! empty( $meta['occupation'] ) ) : ?>
				<p class="ksas-occupation" <?php if ($occupation_prop) echo 'itemprop="'.esc_attr($occupation_prop).'"'; ?>><?php echo esc_html( $meta['occupation'] ); ?></p>
				<?php endif; ?>

				<p class="ksas-name" itemprop="name">
					<?php echo $name_html; ?>
					<?php if ( ! empty( $meta['alternate'] ) ) : ?>
						<span class="ksas-alternate" itemprop="alternateName">(<?php echo esc_html( $meta['alternate'] ); ?>)</span>
					<?php endif; ?>
				</p>
			</div>
		</div>
		<?php if ( ! empty( $meta['bio'] ) ) : ?>
			<p class="ksas-bio" itemprop="<?php echo esc_attr($bio_prop); ?>"><?php echo nl2br( esc_html( $meta['bio'] ) ); ?></p>
		<?php endif; ?>
		<div class="ksas-bottom">
			<?php echo $role_badge; ?>
			<div class="ksas-links" role="navigation" aria-label="<?php echo esc_attr( $aria_label_links ); ?>">
				<?php if ( ! empty( $meta['email'] ) && is_email( $meta['email'] ) ) : ?>
					<a href="mailto:<?php echo antispambot( esc_attr( $meta['email'] ) ); ?>" class="ksas-icon" title="Email" aria-label="Email" itemprop="<?php echo esc_attr($email_prop); ?>">
						<span class="dashicons dashicons-email" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
				<?php foreach ( $meta['sns'] as $sns_url_raw ) :
					$sns_url = ksas_normalize_url( $sns_url_raw );
					if ( ! $sns_url ) continue;
					$sns_host = wp_parse_url( $sns_url, PHP_URL_HOST );
					$sns_name = $sns_host ? str_replace('www.', '', $sns_host) : '';
					$sns_name = $sns_name ? (explode('.', $sns_name)[0] ?? '外部リンク') : '外部リンク';
					$sns_name = ucfirst($sns_name);
					$icon_class = ksas_sns_icon_class( $sns_url );
					?>
					<a href="<?php echo esc_url( $sns_url ); ?>" class="ksas-icon" target="_blank" rel="noopener noreferrer me" title="<?php echo esc_attr( $sns_name ); ?>" aria-label="<?php echo esc_attr( $sns_name ); ?>" itemprop="<?php echo esc_attr($sameas_prop); ?>">
						<span class="dashicons <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function ksas_get_default_author_id(): int {
	$post_id = get_queried_object_id();
	if ( $post_id ) {
		$author_id = (int) get_post_field( 'post_author', $post_id );
		if ( $author_id ) {
			return $author_id;
		}
	}
	
	$users = get_users( [ 'role__in' => [ 'administrator', 'editor', 'author' ], 'number' => 1 ] );
	return ! empty( $users ) ? $users[0]->ID : 0;
}

function ksas_author_shortcode( $atts ): string {
	$atts = shortcode_atts( [
		'user_id' => 0,
		'author' => '',
	], $atts, 'ksas_author' );
	
	$user_id = 0;
	if ( ! empty( $atts['user_id'] ) && is_numeric( $atts['user_id'] ) ) {
		$user_id = (int) $atts['user_id'];
	} elseif ( ! empty( $atts['author'] ) ) {
		$user = get_user_by( 'login', $atts['author'] );
		if ( $user ) {
			$user_id = $user->ID;
		}
	}
	
	if ( ! $user_id ) {
		$user_id = ksas_get_default_author_id();
	}
	
	if ( ! $user_id ) {
		return '';
	}
	
	return ksas_render_author_html( $user_id );
}

add_shortcode( 'ksas_author', 'ksas_author_shortcode' );

function ksas_schema_author_block( string $mode ): string {
	if ( $mode === 'none' ) { return ''; }

	$post_id   = get_queried_object_id();
	$author_id = 0;
	
	if ( $post_id ) {
		$author_id = (int) get_post_field( 'post_author', $post_id );
	}
	
	if ( ! $author_id ) {
		$author_id = ksas_get_default_author_id();
	}
	
	if ( ! $author_id ) { return ''; }

	$author_type_raw = get_user_meta( $author_id, 'asd_author_type', true ) ?: 'person';
	switch ($author_type_raw) {
        case 'corporation':
            $schema_type = 'Corporation';
            $author_type = 'corporation';
            break;
        case 'organization':
            $schema_type = 'Organization';
            $author_type = 'organization';
            break;
        case 'person':
        default:
            $schema_type = 'Person';
            $author_type = 'person';
            break;
    }

	$u          = get_userdata( $author_id );
	$name       = trim( get_user_meta( $author_id, 'asd_display_name', true ) );
	if ( empty($name) && $u ) { $name = $u->display_name; }
	if ( empty($name) ) { return ''; }

	$avatar   = ksas_normalize_url( get_user_meta( $author_id, 'asd_avatar_url', true ) );
	$alt_name = trim( get_user_meta( $author_id, 'asd_alternate_name', true ) );
	$job      = ( $author_type === 'person' ) ? trim( get_user_meta( $author_id, 'asd_occupation', true ) ) : '';
	$org_meta = ( $author_type === 'person' ) ? trim( get_user_meta( $author_id, 'asd_organization', true ) ) : '';
	$email    = trim( get_user_meta( $author_id, 'asd_contact_email', true ) );
	$bio      = trim( get_user_meta( $author_id, 'asd_bio', true ) );
	$plink    = ksas_normalize_url( get_user_meta( $author_id, 'asd_profile_link', true ) );
	$sns_raw  = get_user_meta( $author_id, 'asd_sns_urls', true );
	$same_as  = array_filter( array_map( 'ksas_normalize_url', explode( "\n", $sns_raw ) ) );

	$author_node_common = [ '@type' => $schema_type, 'name' => $name ];
	$author_profile_url = $plink ?: ( ( $author_type === 'person' ) ? get_author_posts_url( $author_id ) : '' );
	$author_node_id = $author_profile_url ? $author_profile_url : home_url('/#' . $author_type . '-' . $author_id);
	$author_node_common['@id'] = $author_node_id;

	if ( $author_profile_url ) { $author_node_common['url'] = $author_profile_url; }
	if ( $avatar ) {
		$image_prop = ( $author_type === 'person' ) ? 'image' : 'logo';
		$author_node_common[ $image_prop ] = [ '@type' => 'ImageObject', 'url' => $avatar ];
	}
	if ( $alt_name ) { $author_node_common['alternateName'] = $alt_name; }
	if ( $email && is_email($email) ) { $author_node_common['email'] = $email; }
	if ( $bio )      { $author_node_common['description'] = $bio; }
	if ( $same_as )  { $author_node_common['sameAs'] = array_values( $same_as ); }

	if ( $author_type === 'person' ) {
		if ( $job ) { $author_node_common['jobTitle'] = $job; }
		if ( $org_meta ) {
			 $author_node_common['worksFor'] = [ '@type'=>'Organization', 'name'=>$org_meta ];
		}
	}

	$author_node = $author_node_common;

	$role_type = get_user_meta( $author_id, 'asd_role_type', true ) ?: 'author';
	$role_labels = [ 'author'=>'執筆者','supervisor'=>'監修者','admin'=>'管理者' ];
	$role_name = $role_labels[ $role_type ] ?? '執筆者';

	$author_prop_value = null;

	if ( $mode === 'author_simple' ) {
		$author_prop_value = $author_node;
		unset($author_prop_value['@id']);
	} elseif ( $mode === 'author_detailed' ) {
		$author_prop_value = [
			'@type'    => 'Role',
			'roleName' => $role_name,
			'author'   => [ '@id' => $author_node_id ],
			'name'     => $name,
		];
		if ( $author_profile_url ) {
			$author_prop_value['url'] = $author_profile_url;
		}
	} else {
		$author_prop_value = [ '@type' => $schema_type, '@id' => $author_node_id ];
	}

	$atype_map = [ 'article'=>'Article','newsarticle'=>'NewsArticle','blogposting'=>'BlogPosting','webpage'=>'WebPage' ];
	$atype_opt   = strtolower( get_option( 'ksas_article_type', 'article' ) );
	$articleType = $atype_map[ $atype_opt ] ?? 'Article';
	
	$page_url = '';
	$post_title = '';
	
	if ( $post_id ) {
		$page_url = get_permalink( $post_id );
		$post_title = get_the_title( $post_id );
	} elseif ( is_category() ) {
		$category = get_queried_object();
		$page_url = get_category_link( $category->term_id );
		$post_title = $category->name;
	} elseif ( is_tag() ) {
		$tag = get_queried_object();
		$page_url = get_tag_link( $tag->term_id );
		$post_title = $tag->name;
	}

	if ( $mode === 'person_ref' ) {
		$anchor = get_option( 'ksas_article_anchor', '' );
        if ( ! empty( $anchor ) && strpos( $anchor, '#' ) !== 0 ) {
            $anchor = '#' . $anchor;
        }
        if ( $anchor === '#' ) {
            $anchor = '';
        }
		$article_id = $page_url . $anchor;
	} else {
		$article_id = $page_url;
	}
	
	$article = [
		'@type'            => $articleType,
		'@id'              => $article_id,
		'mainEntityOfPage' => [ '@type' => 'WebPage', '@id' => $page_url ],
		'headline'         => wp_strip_all_tags( $post_title ),
		'url'              => $page_url,
	];
	
	if ( $post_id ) {
		$article['datePublished'] = get_the_date( 'c', $post_id );
		$article['dateModified'] = get_the_modified_date( 'c', $post_id );
	}

	$publisher_name = get_bloginfo('name');
	$publisher_logo_url = get_site_icon_url( 512, '', 0 ) ?: ( function_exists('get_custom_logo') ? wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ) : '' );
	$publisher_node = null;
	if ($publisher_name) {
		$publisher_id = home_url('/#organization');
		$article['publisher'] = [ '@id' => $publisher_id ];
		$publisher_node = [
			'@type' => 'Organization',
			'@id'   => $publisher_id,
			'name'  => $publisher_name,
			'url'   => home_url('/')
		];
		if ($publisher_logo_url) {
            $publisher_node['logo'] = [
                '@type' => 'ImageObject',
                'url'   => ksas_normalize_url($publisher_logo_url)
            ];
		}
	}

	$image_node = null;
	if ( $post_id && has_post_thumbnail( $post_id ) && ( $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ) ) ) {
		$image_id = $article_id . '#primaryimage';
		$article['image'] = [ '@id' => $image_id ];
		$image_node = [
			'@type'   => 'ImageObject',
			'@id'     => $image_id,
			'url'     => ksas_normalize_url($img[0]),
			'width'   => (int)$img[1],
			'height'  => (int)$img[2],
			'caption' => get_the_post_thumbnail_caption( $post_id ) ?: wp_strip_all_tags( $post_title ),
		];
	}

	if ($author_prop_value) {
		if ( $mode === 'person_ref' ) {
			$link_props = get_option( 'ksas_link_props', [ 'author' ] ) ?: [ 'author' ];
			$valid_link_props = ksas_available_link_props();
			foreach ( array_unique($link_props) as $p ) {
				if ( !empty($p) && array_key_exists($p, $valid_link_props) ) {
					$article[ $p ] = $author_prop_value;
				}
			}
		} else {
			$article[ 'author' ] = $author_prop_value;
		}
	}

	$graph = [];
	$graph[] = $article;
	if ( $mode !== 'author_simple' && $author_node ) {
		$graph[] = $author_node;
	}
	if ($publisher_node) { $graph[] = $publisher_node; }
	if ($image_node) { $graph[] = $image_node; }

	$unique_graph = [];
	$ids_seen = [];
	foreach ($graph as $node) {
		if (isset($node['@id'])) {
			if (!in_array($node['@id'], $ids_seen)) {
				$unique_graph[] = $node;
				$ids_seen[] = $node['@id'];
			}
		}
	}

	$output_data = !empty($unique_graph) ? [ '@context'=>'https://schema.org', '@graph'=>$unique_graph ] : null;

	if ( empty($output_data) ) { return ''; }

	$json_ld_output = '<script type="application/ld+json" class="ksas-schema-graph">' . wp_json_encode( $output_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>';

    if ( defined('WP_DEBUG') && WP_DEBUG ) {
        $debug_comment = sprintf("\n<!-- KSAS Schema Mode: %s | Author Type Raw: %s | Schema Type: %s | Graph Nodes: %d -->\n", esc_html($mode), esc_html($author_type_raw), esc_html($schema_type), count($unique_graph) );
        return $debug_comment . $json_ld_output;
    }
	return $json_ld_output;
}


if ( ! function_exists( 'ksas_output_plugin_schema' ) ) {
	function ksas_output_plugin_schema() {
		if ( ! get_option( 'ksas_schema_plugin_enable', 0 ) ) { return; }
		if ( ! apply_filters( 'ksas_allow_plugin_schema', true ) ) { return; }
		
		$enabled_types = get_option( 'ksas_post_types', [] );
		$display_on_front_page = get_option( 'ksas_display_on_front_page', 0 );
		$display_on_category = get_option( 'ksas_display_on_category', 0 );
		$display_on_tag = get_option( 'ksas_display_on_tag', 0 );
		$display_on_home = get_option( 'ksas_display_on_home', 0 );

		$should_output = false;
		if ( is_front_page() && is_page() && $display_on_front_page ) {
			$should_output = true;
		} elseif ( is_home() && $display_on_home ) {
			$should_output = true;
		} elseif ( is_category() && $display_on_category ) {
			$should_output = true;
		} elseif ( is_tag() && $display_on_tag ) {
			$should_output = true;
		} elseif ( ! empty( $enabled_types ) && is_singular( $enabled_types ) ) {
			$should_output = true;
		}
		
		if ( ! $should_output ) { return; }

		$plugin_data = get_plugin_data( KSAS_ASD_PATH . 'author-status-display.php' );
		$schema = [
			'@context'=>'https://schema.org',
			'@type'=>'SoftwareApplication',
			'@id'=>home_url( '/#kashiwazaki-seo-author-schema-display' ),
			'name'=>$plugin_data['Name'] ?? 'Kashiwazaki SEO Author Schema Display',
			'description'=>$plugin_data['Description'] ?? '著者情報を表示しスキーマを生成するWordPressプラグイン。',
			'applicationCategory'=>'WordPressPlugin',
			'operatingSystem'=>'WordPress',
			'softwareVersion'=>$plugin_data['Version'] ?? KSAS_ASD_VERSION,
			'url'=>$plugin_data['PluginURI'] ?? 'https://www.tsuyoshikashiwazaki.jp/',
			'downloadUrl'=>$plugin_data['PluginURI'] ?? 'https://www.tsuyoshikashiwazaki.jp/',
			'license'=>$plugin_data['LicenseURI'] ?? 'https://www.gnu.org/licenses/gpl-2.0.html',
			'offers'=>[ '@type'=>'Offer', 'price'=>'0', 'priceCurrency'=>'JPY' ],
			'copyrightYear'=>date('Y'),
			'copyrightHolder'=>[ '@type'=>'Person', 'name'=>$plugin_data['AuthorName'] ?? 'Tsuyoshi Kashiwazaki', 'url'=>$plugin_data['AuthorURI'] ?? 'https://www.tsuyoshikashiwazaki.jp/' ],
			'author'=>[ '@type'=>'Person', 'name'=>$plugin_data['AuthorName'] ?? 'Tsuyoshi Kashiwazaki', 'url'=>$plugin_data['AuthorURI'] ?? 'https://www.tsuyoshikashiwazaki.jp/' ],
		];
		echo '<script type="application/ld+json" class="ksas-plugin-schema">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>';
	}
}

add_action( 'wp_head', 'ksas_output_plugin_schema', 6 );

add_action( 'wp_head', function () {
	$schema_mode = get_option( 'ksas_schema_mode', 'none' );
	if ( 'none' === $schema_mode ) {
		return;
	}

	if ( ! ksas_should_display_author() ) {
		return;
	}

	echo ksas_schema_author_block( $schema_mode );
}, 5 );

function ksas_should_display_author(): bool {
	$enabled_types = get_option( 'ksas_post_types', [] );
	$display_on_front_page = get_option( 'ksas_display_on_front_page', 0 );
	$display_on_category = get_option( 'ksas_display_on_category', 0 );
	$display_on_tag = get_option( 'ksas_display_on_tag', 0 );
	$display_on_home = get_option( 'ksas_display_on_home', 0 );

	if ( ! apply_filters( 'ksas_allow_author_display', true ) ) {
		return false;
	}

	if ( is_front_page() && is_page() && $display_on_front_page ) {
		return true;
	} elseif ( is_home() && $display_on_home ) {
		return true;
	} elseif ( is_category() && $display_on_category ) {
		return true;
	} elseif ( is_tag() && $display_on_tag ) {
		return true;
	} elseif ( ! empty( $enabled_types ) && is_singular( $enabled_types ) ) {
		return true;
	}

	return false;
}

function ksas_get_author_box_html(): string {
	if ( ! ksas_should_display_author() ) {
		return '';
	}

	$post_id = get_queried_object_id();
	$author_id = 0;
	
	if ( $post_id ) {
		$author_id = (int) get_post_field( 'post_author', $post_id );
	}
	
	if ( ! $author_id ) {
		$author_id = ksas_get_default_author_id();
	}
	
	if ( ! $author_id ) {
		return '';
	}
	
	return ksas_render_author_html( $author_id );
}

add_filter( 'the_content', function ( $content ) {
	if ( ! ksas_should_display_author() ) {
		return $content;
	}

	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$html = ksas_get_author_box_html();
	if ( empty( trim( $html ) ) ) {
		return $content;
	}
	
	$pos = get_option( 'ksas_position', 'top' );
	
	if ( $pos === 'bottom' ) {
		return $content . $html;
	} elseif ( $pos === 'both' ) {
		return $html . $content . $html;
	} elseif ( in_array( $pos, ['h1', 'h2', 'h3', 'h4'], true ) ) {
		// 見出しタグの直前に挿入
		$pattern = '/<(' . $pos . ')[^>]*>/i';
		if ( preg_match( $pattern, $content ) ) {
			return preg_replace( $pattern, $html . '$0', $content, 1 );
		} else {
			// 指定した見出しが見つからない場合は記事上に表示
			return $html . $content;
		}
	} else {
		// 記事上（デフォルト）
		return $html . $content;
	}
}, 999 );



add_action( 'wp_enqueue_scripts', function () {
	$enabled_types = get_option( 'ksas_post_types', [] );
	$display_on_front_page = get_option( 'ksas_display_on_front_page', 0 );
	$display_on_category = get_option( 'ksas_display_on_category', 0 );
	$display_on_tag = get_option( 'ksas_display_on_tag', 0 );
	$display_on_home = get_option( 'ksas_display_on_home', 0 );

	$should_enqueue = false;
	if ( is_front_page() && is_page() && $display_on_front_page ) {
		$should_enqueue = true;
	} elseif ( is_home() && $display_on_home ) {
		$should_enqueue = true;
	} elseif ( is_category() && $display_on_category ) {
		$should_enqueue = true;
	} elseif ( is_tag() && $display_on_tag ) {
		$should_enqueue = true;
	} elseif ( ! empty( $enabled_types ) && is_singular( $enabled_types ) ) {
		$should_enqueue = true;
	}

	if ( ! $should_enqueue ) { return; }
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'ksas-style', KSAS_ASD_URL . 'assets/style.css', [], KSAS_ASD_VERSION );
}, 10 );