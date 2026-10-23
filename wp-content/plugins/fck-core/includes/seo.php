<?php
/**
 * SEO: título, descrição, canonical, Open Graph/Twitter, noindex fora de
 * produção, dados estruturados (somente com dados reais) e sitemap nativo.
 */

defined( 'ABSPATH' ) || exit;

/** Só produção pode ser indexada. Staging/local sempre noindex. */
function fck_is_indexable(): bool {
	return 'production' === wp_get_environment_type() && '1' === (string) get_option( 'blog_public' );
}

/** Dados de SEO do objeto atual. */
function fck_current_seo(): array {
	$defaults = fck_site( 'seo_defaults' );
	$seo      = array();
	$title    = '';
	$desc     = '';
	$image    = 0;
	$type     = 'website';

	$post = is_front_page() ? get_post( (int) get_option( 'page_on_front' ) ) : ( is_singular() ? get_queried_object() : null );
	if ( $post instanceof WP_Post ) {
		$seo   = fck_meta( $post->ID, 'seo' );
		$title = $post->post_title;
		if ( 'fck_article' === $post->post_type ) {
			$type  = 'article';
			$desc  = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
			$image = (int) get_post_thumbnail_id( $post );
		} elseif ( 'fck_case' === $post->post_type ) {
			$d     = fck_meta( $post->ID, 'details' );
			$desc  = (string) ( $d['summary'] ?? '' );
			$image = (int) ( $d['kv'] ?? 0 );
		} elseif ( 'fck_platform' === $post->post_type ) {
			$desc = (string) ( fck_meta( $post->ID, 'identity' )['summary'] ?? '' );
		}
	}

	return array(
		'post'        => $post instanceof WP_Post ? $post : null,
		'title'       => (string) ( $seo['title'] ?? '' ),
		'fallback'    => $title,
		'description' => (string) ( ( $seo['description'] ?? '' ) ?: ( $desc ?: ( $defaults['default_description'] ?? '' ) ) ),
		'image'       => (int) ( ( $seo['image'] ?? 0 ) ?: ( $image ?: ( $defaults['default_image'] ?? 0 ) ) ),
		'noindex'     => ! empty( $seo['noindex'] ),
		'type'        => $type,
	);
}

add_filter(
	'pre_get_document_title',
	static function ( string $title ): string {
		$seo = fck_current_seo();
		if ( '' !== $seo['title'] ) {
			return $seo['title']; // título completo definido no painel
		}
		$defaults = fck_site( 'seo_defaults' );
		$brand    = fck_site( 'brand' )['site_name'] ?? get_bloginfo( 'name' );
		if ( is_front_page() || ! $seo['fallback'] ) {
			if ( is_404() ) {
				$nf = fck_ui( 'notfound', 'title' );
				return $nf ? $nf . ' | ' . $brand : $brand;
			}
			return (string) ( ( $defaults['default_title'] ?? '' ) ?: $brand );
		}
		$tpl = (string) ( $defaults['title_template'] ?? '' );
		return str_contains( $tpl, '%s' ) ? str_replace( '%s', $seo['fallback'], $tpl ) : $seo['fallback'];
	}
);

// Canonical próprio (inclui a Home e respeita filtros sem parâmetros).
remove_action( 'wp_head', 'rel_canonical' );

add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		$seo = fck_current_seo();
		if ( ! fck_is_indexable() || $seo['noindex'] || is_404() || is_search() ) {
			return array( 'noindex' => true, 'nofollow' => ! fck_is_indexable() );
		}
		$robots['max-image-preview'] = 'large';
		return $robots;
	}
);

add_action(
	'wp_head',
	static function (): void {
		if ( is_404() ) {
			return;
		}
		$seo      = fck_current_seo();
		$brand    = fck_site( 'brand' )['site_name'] ?? get_bloginfo( 'name' );
		$url      = is_front_page() ? home_url( '/' ) : ( $seo['post'] ? get_permalink( $seo['post'] ) : home_url( add_query_arg( array() ) ) );
		$title    = wp_get_document_title();
		$desc     = wp_strip_all_tags( $seo['description'] );
		$img      = $seo['image'] ? wp_get_attachment_image_src( $seo['image'], 'fck-og' ) : null;
		$img_full = $seo['image'] ? wp_get_attachment_image_src( $seo['image'], 'full' ) : null;
		$img      = $img ?: $img_full;

		echo "\n";
		if ( $desc ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $seo['type'] ) );
		printf( '<meta property="og:locale" content="pt_BR">' . "\n" );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $brand ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		if ( $desc ) {
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		if ( $img ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img[0] ) );
			printf( '<meta property="og:image:width" content="%d">' . "\n", (int) $img[1] );
			printf( '<meta property="og:image:height" content="%d">' . "\n", (int) $img[2] );
			$alt = fck_alt( $seo['image'] );
			if ( $alt ) {
				printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $alt ) );
			}
		}
		if ( 'article' === $seo['type'] && $seo['post'] ) {
			printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c', $seo['post'] ) ) );
		}

		// Dados estruturados — apenas campos reais.
		foreach ( fck_structured_data( $seo, $url, $brand ) as $graph ) {
			printf(
				'<script type="application/ld+json" nonce="%s">%s</script>' . "\n",
				esc_attr( fck_csp_nonce() ),
				wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
			);
		}
	},
	2
);

function fck_structured_data( array $seo, string $url, string $brand ): array {
	$out = array();
	if ( is_front_page() ) {
		$org      = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => $brand,
			'url'      => home_url( '/' ),
		);
		$logo     = (int) ( fck_site( 'brand' )['logo_light'] ?? 0 );
		$contacts = fck_site( 'contacts' );
		if ( $logo && ( $src = wp_get_attachment_url( $logo ) ) ) {
			$org['logo'] = $src;
		}
		$same = array_values( array_filter( array_map( fn( $s ) => $s['url'] ?? '', (array) ( $contacts['socials'] ?? array() ) ) ) );
		if ( $same ) {
			$org['sameAs'] = $same;
		}
		if ( ! empty( $contacts['email'] ) ) {
			$org['email'] = $contacts['email'];
		}
		$out[] = $org;
		$out[] = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'WebSite',
			'name'       => $brand,
			'url'        => home_url( '/' ),
			'inLanguage' => 'pt-BR',
		);
		return $out;
	}
	$post = $seo['post'];
	if ( ! $post ) {
		return $out;
	}

	// Breadcrumb: Home › (página-base) › item
	$crumbs = array( array( 'name' => fck_site( 'brand' )['site_name'] ?? $brand, 'url' => home_url( '/' ) ) );
	$bases  = array( 'fck_platform' => 'ecosystem', 'fck_case' => 'cases', 'fck_article' => 'insights' );
	if ( isset( $bases[ $post->post_type ] ) && ( $base = fck_role_page( $bases[ $post->post_type ] ) ) ) {
		$crumbs[] = array( 'name' => $base->post_title, 'url' => get_permalink( $base ) );
	}
	$crumbs[] = array( 'name' => $post->post_title, 'url' => $url );
	$out[]    = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array_map(
			fn( $c, $i ) => array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url'] ),
			$crumbs,
			array_keys( $crumbs )
		),
	);

	if ( 'fck_article' === $post->post_type ) {
		$article = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => $post->post_title,
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'mainEntityOfPage' => $url,
			'inLanguage'       => 'pt-BR',
			'publisher'        => array( '@type' => 'Organization', 'name' => $brand ),
		);
		$author = (int) ( fck_meta( $post->ID, 'details' )['author'] ?? 0 );
		if ( $author && fck_is_visible( $author ) ) {
			$article['author'] = array( '@type' => 'Person', 'name' => get_the_title( $author ) );
		}
		if ( $seo['description'] ) {
			$article['description'] = wp_strip_all_tags( $seo['description'] );
		}
		$thumb = get_post_thumbnail_id( $post );
		if ( $thumb ) {
			$article['image'] = wp_get_attachment_image_url( $thumb, 'fck-wide' );
		}
		$out[] = $article;
	}
	return $out;
}

// ---------------------------------------------------------------------------
// Sitemap nativo (/wp-sitemap.xml)
// ---------------------------------------------------------------------------

add_filter(
	'wp_sitemaps_add_provider',
	static fn( $provider, string $name ) => in_array( $name, array( 'users', 'taxonomies' ), true ) ? false : $provider,
	10,
	2
);

add_filter(
	'wp_sitemaps_post_types',
	static fn( array $types ) => array_intersect_key( $types, array_flip( array( 'page', 'fck_platform', 'fck_case', 'fck_article' ) ) )
);

// Exclui do sitemap o que está marcado como noindex.
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( array $args ): array {
		$args['meta_query'] = array( // phpcs:ignore
			'relation' => 'OR',
			array( 'key' => '_fck_seo', 'compare' => 'NOT EXISTS' ),
			array( 'key' => '_fck_seo', 'value' => '"noindex";b:1', 'compare' => 'NOT LIKE' ),
		);
		return $args;
	}
);

add_filter( 'wp_sitemaps_enabled', static fn() => fck_is_indexable() || 'production' !== wp_get_environment_type() );

add_filter(
	'robots_txt',
	static function ( string $output, bool $public ): string {
		if ( ! fck_is_indexable() ) {
			return "User-agent: *\nDisallow: /\n";
		}
		return $output . "Disallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n";
	},
	10,
	2
);
