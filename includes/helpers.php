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
		'github.com'            => 'dashicons-github',
		'gist.github.com'       => 'dashicons-github',
		'gitlab.com'            => 'dashicons-gitlab',
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