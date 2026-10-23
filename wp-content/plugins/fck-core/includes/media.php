<?php
/**
 * Campos extras de mídia: decorativa, crédito, licença, direito de uso,
 * ponto focal e poster de vídeo. SVG permitido apenas para administradores,
 * com verificação de conteúdo perigoso.
 */

defined( 'ABSPATH' ) || exit;

function fck_media_fields(): array {
	return array(
		'_fck_decorative'      => array( 'Imagem decorativa', 'checkbox', 'Marque só se a imagem não transmite informação (fica sem texto alternativo).' ),
		'_fck_credit'          => array( 'Crédito / autoria', 'text', '' ),
		'_fck_license'         => array( 'Licença / origem', 'text', '' ),
		'_fck_rights_confirmed' => array( 'Direito de uso confirmado', 'checkbox', 'Obrigatório para logos de clientes, veículos de imprensa e imagens de cases aparecerem no site.' ),
		'_fck_focal'           => array( 'Ponto focal (ex.: 50% 30%)', 'text', 'Horizontal e vertical em %. Define o enquadramento quando a imagem é recortada.' ),
		'_fck_poster'          => array( 'Poster do vídeo (ID da imagem)', 'text', 'Somente para vídeos: imagem exibida antes de tocar.' ),
	);
}

add_filter(
	'attachment_fields_to_edit',
	static function ( array $fields, WP_Post $post ): array {
		foreach ( fck_media_fields() as $key => [ $label, $type, $help ] ) {
			if ( '_fck_poster' === $key && ! str_starts_with( (string) $post->post_mime_type, 'video/' ) ) {
				continue;
			}
			$value = get_post_meta( $post->ID, $key, true );
			$name  = 'attachments[' . $post->ID . '][' . $key . ']';
			$html  = 'checkbox' === $type
				? sprintf( '<input type="hidden" name="%1$s" value="0"><input type="checkbox" name="%1$s" value="1" %2$s>', esc_attr( $name ), checked( (bool) $value, true, false ) )
				: sprintf( '<input type="text" class="text" name="%s" value="%s">', esc_attr( $name ), esc_attr( (string) $value ) );
			$fields[ $key ] = array(
				'label' => $label,
				'input' => 'html',
				'html'  => $html,
				'helps' => $help,
			);
		}
		if ( str_starts_with( (string) $post->post_mime_type, 'image/' ) && ! get_post_meta( $post->ID, '_fck_decorative', true ) && ! get_post_meta( $post->ID, '_wp_attachment_image_alt', true ) ) {
			$fields['fck_alt_warning'] = array(
				'label' => '',
				'input' => 'html',
				'html'  => '<strong style="color:#b32d2e">Preencha o texto alternativo ou marque como decorativa.</strong>',
			);
		}
		return $fields;
	},
	10,
	2
);

add_filter(
	'attachment_fields_to_save',
	static function ( array $post, array $attachment ): array {
		foreach ( fck_media_fields() as $key => [ , $type ] ) {
			if ( ! array_key_exists( $key, $attachment ) ) {
				continue;
			}
			$value = 'checkbox' === $type ? (bool) $attachment[ $key ] : sanitize_text_field( (string) $attachment[ $key ] );
			if ( '_fck_focal' === $key && $value && ! preg_match( '/^\d{1,3}% \d{1,3}%$/', $value ) ) {
				continue;
			}
			if ( '_fck_poster' === $key ) {
				$value = absint( $value ) ?: '';
			}
			update_post_meta( (int) $post['ID'], $key, $value );
		}
		return $post;
	},
	10,
	2
);

// Tamanhos de imagem usados pelo tema.
add_action(
	'after_setup_theme',
	static function (): void {
		add_image_size( 'fck-card', 960, 0 );
		add_image_size( 'fck-wide', 1600, 0 );
		add_image_size( 'fck-full', 2400, 0 );
		add_image_size( 'fck-og', 1200, 630, true );
	}
);

// WebP como formato gerado (quando o servidor suporta).
add_filter( 'image_editor_output_format', static fn( array $f ) => array_merge( $f, array( 'image/jpeg' => 'image/webp', 'image/png' => 'image/webp' ) ) );

// SVG: só administradores, e apenas arquivos sem scripts/eventos.
add_filter(
	'upload_mimes',
	static function ( array $mimes ): array {
		if ( current_user_can( 'manage_options' ) ) {
			$mimes['svg'] = 'image/svg+xml';
		}
		return $mimes;
	}
);

add_filter(
	'wp_check_filetype_and_ext',
	static function ( array $data, string $file, string $filename ): array {
		if ( str_ends_with( strtolower( $filename ), '.svg' ) && current_user_can( 'manage_options' ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	},
	10,
	3
);

add_filter(
	'wp_handle_upload_prefilter',
	static function ( array $file ): array {
		if ( ! str_ends_with( strtolower( (string) $file['name'] ), '.svg' ) ) {
			return $file;
		}
		$content = (string) file_get_contents( $file['tmp_name'] ); // phpcs:ignore
		if ( preg_match( '/<script|<foreignObject|\son\w+\s*=|javascript:|<!ENTITY|xlink:href\s*=\s*["\']\s*(?!#)/i', $content ) ) {
			$file['error'] = 'SVG recusado: o arquivo contém scripts, eventos ou referências externas.';
		}
		return $file;
	}
);
