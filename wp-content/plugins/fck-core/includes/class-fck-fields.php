<?php
/**
 * Framework de campos do painel (sem plugins pagos).
 *
 * Um "grupo" é um meta box com campos tipados. Cada grupo é salvo em um único
 * meta `_fck_{id}` (array), registrado com revisões ativas: o histórico de
 * versões, a restauração e o preview do WordPress incluem esses campos.
 *
 * Tipos: text, textarea, number, checkbox, select, image, media, link, post,
 * posts, repeater, heading (apenas visual).
 */

defined( 'ABSPATH' ) || exit;

final class FCK_Fields {

	/** Erros de validação da última gravação (exibidos como aviso). */
	private static array $errors = array();

	// ---------------------------------------------------------------------
	// Renderização
	// ---------------------------------------------------------------------

	public static function render_group( array $group, array $values, string $prefix ): void {
		if ( ! empty( $group['description'] ) ) {
			echo '<p class="fck-group-desc">' . esc_html( $group['description'] ) . '</p>';
		}
		echo '<div class="fck-fields">';
		foreach ( $group['fields'] as $field ) {
			self::render_field( $field, $values[ $field['name'] ?? '' ] ?? null, $prefix );
		}
		echo '</div>';
	}

	public static function render_field( array $field, mixed $value, string $prefix ): void {
		$type = $field['type'] ?? 'text';
		if ( 'heading' === $type ) {
			echo '<h4 class="fck-heading">' . esc_html( $field['label'] ) . '</h4>';
			return;
		}
		$name     = $prefix . '[' . $field['name'] . ']';
		$id       = 'fck-' . md5( $name );
		$label    = $field['label'] ?? $field['name'];
		$help     = $field['help'] ?? '';
		$max      = isset( $field['max'] ) ? (int) $field['max'] : 0;
		$required = ! empty( $field['required'] );
		$value    = $value ?? ( $field['default'] ?? null );

		$width = isset( $field['width'] ) ? ' fck-w-' . $field['width'] : '';
		echo '<div class="fck-field fck-type-' . esc_attr( $type ) . $width . '">';

		if ( 'checkbox' === $type ) {
			printf(
				'<label class="fck-check"><input type="hidden" name="%1$s" value="0"><input type="checkbox" id="%2$s" name="%1$s" value="1" %3$s> %4$s</label>',
				esc_attr( $name ),
				esc_attr( $id ),
				checked( (bool) $value, true, false ),
				esc_html( $label )
			);
			self::help( $help );
			echo '</div>';
			return;
		}

		printf(
			'<label class="fck-label" for="%s">%s%s</label>',
			esc_attr( $id ),
			esc_html( $label ),
			$required ? ' <span class="fck-req" aria-hidden="true">*</span><span class="screen-reader-text">(obrigatório)</span>' : ''
		);

		switch ( $type ) {
			case 'textarea':
				printf(
					'<textarea id="%s" name="%s" rows="%d" %s %s>%s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					(int) ( $field['rows'] ?? ( $max > 400 ? 6 : 3 ) ),
					$max ? 'maxlength="' . $max . '" data-fck-count' : '',
					$required ? 'data-fck-required' : '',
					esc_textarea( (string) $value )
				);
				break;

			case 'richtext':
				wp_editor(
					(string) $value,
					'fck' . preg_replace( '/[^a-z]/', '', md5( $name ) ),
					array(
						'textarea_name' => $name,
						'media_buttons' => false,
						'teeny'         => false,
						'textarea_rows' => 16,
						'quicktags'     => true,
					)
				);
				break;

			case 'number':
				printf(
					'<input type="number" id="%s" name="%s" value="%s" %s %s class="small-text">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					isset( $field['min'] ) ? 'min="' . (int) $field['min'] . '"' : '',
					isset( $field['maxValue'] ) ? 'max="' . (int) $field['maxValue'] . '"' : ''
				);
				break;

			case 'select':
				printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
				if ( empty( $field['no_empty'] ) ) {
					echo '<option value="">—</option>';
				}
				foreach ( $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( (string) $opt_value ),
						selected( (string) $value, (string) $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'image':
			case 'media':
				self::render_media( $id, $name, (int) $value, 'image' === $type ? 'image' : '' );
				break;

			case 'link':
				self::render_link( $id, $name, is_array( $value ) ? $value : array(), $field );
				break;

			case 'post':
				self::render_post_select( $id, $name, (int) $value, (array) $field['post_type'] );
				break;

			case 'posts':
				self::render_posts_select( $id, $name, is_array( $value ) ? array_map( 'intval', $value ) : array(), (array) $field['post_type'] );
				break;

			case 'repeater':
				self::render_repeater( $id, $name, is_array( $value ) ? array_values( $value ) : array(), $field );
				break;

			default:
				printf(
					'<input type="%s" id="%s" name="%s" value="%s" class="widefat" %s %s>',
					'url' === $type ? 'url' : ( 'email' === $type ? 'email' : 'text' ),
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					$max ? 'maxlength="' . $max . '" data-fck-count' : '',
					$required ? 'data-fck-required' : ''
				);
		}

		self::help( $help . ( $max && ! in_array( $type, array( 'repeater', 'link' ), true ) ? ( $help ? ' ' : '' ) . sprintf( 'Até %d caracteres.', $max ) : '' ) );
		echo '</div>';
	}

	private static function help( string $help ): void {
		if ( '' !== $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
	}

	private static function render_media( string $id, string $name, int $attachment_id, string $library ): void {
		$preview = '';
		if ( $attachment_id ) {
			$mime = (string) get_post_mime_type( $attachment_id );
			if ( str_starts_with( $mime, 'image/' ) ) {
				$preview = wp_get_attachment_image( $attachment_id, 'thumbnail', false, array( 'alt' => '' ) );
			} else {
				$preview = '<span class="fck-file">' . esc_html( basename( (string) get_attached_file( $attachment_id ) ) ) . '</span>';
			}
		}
		printf(
			'<div class="fck-media" data-library="%5$s"><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><div class="fck-media-preview">%4$s</div><button type="button" class="button fck-media-select">Selecionar</button> <button type="button" class="button-link fck-media-remove" %6$s>Remover</button></div>',
			esc_attr( $id ),
			esc_attr( $name ),
			$attachment_id ? (int) $attachment_id : '',
			$preview, // phpcs:ignore -- gerado por wp_get_attachment_image / esc_html.
			esc_attr( $library ),
			$attachment_id ? '' : 'hidden'
		);
	}

	/** Tipos de conteúdo que podem ser destino de links internos. */
	public static function linkable_types(): array {
		return array(
			'page'         => 'Páginas',
			'fck_platform' => 'Plataformas',
			'fck_case'     => 'Cases',
			'fck_article'  => 'Artigos',
		);
	}

	private static function post_options( array $types, int $selected, bool $grouped ): string {
		$html = '';
		foreach ( $types as $type ) {
			$posts = get_posts(
				array(
					'post_type'      => $type,
					'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
					'posts_per_page' => 300,
					'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
					'suppress_filters' => false,
				)
			);
			if ( ! $posts ) {
				continue;
			}
			$obj = get_post_type_object( $type );
			if ( $grouped ) {
				$html .= '<optgroup label="' . esc_attr( $obj ? $obj->labels->name : $type ) . '">';
			}
			foreach ( $posts as $p ) {
				$status = 'publish' === $p->post_status ? '' : ' (' . get_post_status_object( $p->post_status )->label . ')';
				$html  .= sprintf(
					'<option value="%d" %s>%s</option>',
					$p->ID,
					selected( $selected, $p->ID, false ),
					esc_html( ( $p->post_title ?: '#' . $p->ID ) . $status )
				);
			}
			if ( $grouped ) {
				$html .= '</optgroup>';
			}
		}
		return $html;
	}

	private static function render_link( string $id, string $name, array $value, array $field ): void {
		$type = $value['type'] ?? 'post';
		echo '<div class="fck-link" data-fck-link>';
		printf(
			'<span class="fck-sub"><label for="%1$s-label">Texto</label><input type="text" id="%1$s-label" name="%2$s[label]" value="%3$s" maxlength="60" data-fck-count class="regular-text"></span>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( (string) ( $value['label'] ?? '' ) )
		);
		printf(
			'<span class="fck-sub"><label for="%1$s-type">Destino</label><select id="%1$s-type" name="%2$s[type]" data-fck-link-type><option value="post" %3$s>Página do site</option><option value="url" %4$s>Endereço (URL)</option></select></span>',
			esc_attr( $id ),
			esc_attr( $name ),
			selected( $type, 'post', false ),
			selected( $type, 'url', false )
		);
		printf(
			'<span class="fck-sub" data-fck-show="post"><label for="%1$s-post">Página</label><select id="%1$s-post" name="%2$s[post]"><option value="">—</option>%3$s</select></span>',
			esc_attr( $id ),
			esc_attr( $name ),
			self::post_options( array_keys( self::linkable_types() ), (int) ( $value['post'] ?? 0 ), true ) // phpcs:ignore -- escapado em post_options.
		);
		printf(
			'<span class="fck-sub" data-fck-show="url"><label for="%1$s-url">URL</label><input type="text" id="%1$s-url" name="%2$s[url]" value="%3$s" class="regular-text" placeholder="/contato?assunto=imprensa ou https://..."></span>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( (string) ( $value['url'] ?? '' ) )
		);
		echo '</div>';
		if ( empty( $field['help'] ) ) {
			echo '<p class="description">Deixe o texto vazio para ocultar o botão. Links para páginas acompanham mudanças de endereço automaticamente.</p>';
		}
	}

	private static function render_post_select( string $id, string $name, int $value, array $types ): void {
		printf(
			'<select id="%s" name="%s"><option value="">—</option>%s</select>',
			esc_attr( $id ),
			esc_attr( $name ),
			self::post_options( $types, $value, count( $types ) > 1 ) // phpcs:ignore -- escapado em post_options.
		);
	}

	private static function render_posts_select( string $id, string $name, array $values, array $types ): void {
		// Lista ordenável: os selecionados aparecem na ordem gravada.
		echo '<div class="fck-posts" data-fck-posts>';
		printf( '<ol class="fck-posts-list" data-name="%s">', esc_attr( $name . '[]' ) );
		foreach ( $values as $pid ) {
			if ( ! get_post( $pid ) ) {
				continue;
			}
			printf(
				'<li><input type="hidden" name="%s[]" value="%d"><span>%s</span> <button type="button" class="button-link fck-up" aria-label="Mover para cima">↑</button> <button type="button" class="button-link fck-down" aria-label="Mover para baixo">↓</button> <button type="button" class="button-link fck-del" aria-label="Remover">×</button></li>',
				esc_attr( $name ),
				(int) $pid,
				esc_html( get_the_title( $pid ) )
			);
		}
		echo '</ol>';
		printf(
			'<select id="%s" class="fck-posts-add"><option value="">+ Adicionar…</option>%s</select>',
			esc_attr( $id ),
			self::post_options( $types, 0, count( $types ) > 1 ) // phpcs:ignore -- escapado.
		);
		echo '</div>';
	}

	private static function render_repeater( string $id, string $name, array $rows, array $field ): void {
		$max = (int) ( $field['max_rows'] ?? 50 );
		printf( '<div class="fck-repeater" data-fck-repeater data-max="%d" data-name="%s">', $max, esc_attr( $name ) );
		echo '<ol class="fck-rows">';
		foreach ( $rows as $index => $row ) {
			self::render_row( $name . '[' . $index . ']', is_array( $row ) ? $row : array(), $field, $index + 1 );
		}
		echo '</ol>';
		// Modelo da linha (clonado via JS; o marcador __i__ vira o índice).
		echo '<template>';
		self::render_row( $name . '[__i__]', array(), $field, 0 );
		echo '</template>';
		printf(
			'<button type="button" class="button fck-add-row">+ %s</button>',
			esc_html( $field['row_label'] ?? 'Adicionar item' )
		);
		echo '</div>';
	}

	private static function render_row( string $prefix, array $row, array $field, int $number ): void {
		echo '<li class="fck-row">';
		echo '<div class="fck-row-bar"><span class="fck-row-num">' . ( $number ? (int) $number : '' ) . '</span>';
		echo '<button type="button" class="button-link fck-up" aria-label="Mover para cima">↑</button> ';
		echo '<button type="button" class="button-link fck-down" aria-label="Mover para baixo">↓</button> ';
		echo '<button type="button" class="button-link fck-del" aria-label="Remover item">Remover</button></div>';
		echo '<div class="fck-row-fields">';
		foreach ( $field['fields'] as $sub ) {
			self::render_field( $sub, $row[ $sub['name'] ?? '' ] ?? null, $prefix );
		}
		echo '</div></li>';
	}

	// ---------------------------------------------------------------------
	// Sanitização
	// ---------------------------------------------------------------------

	/**
	 * Sanitiza os valores enviados de um grupo. Textos acima do limite NÃO são
	 * truncados: o valor anterior é mantido e um erro é exibido ao editor.
	 */
	public static function sanitize_group( array $group, array $input, array $previous ): array {
		$out = array();
		foreach ( $group['fields'] as $field ) {
			if ( 'heading' === ( $field['type'] ?? '' ) ) {
				continue;
			}
			$key         = $field['name'];
			$out[ $key ] = self::sanitize_field( $field, $input[ $key ] ?? null, $previous[ $key ] ?? null );
		}
		return $out;
	}

	public static function sanitize_field( array $field, mixed $raw, mixed $previous ): mixed {
		$type = $field['type'] ?? 'text';
		switch ( $type ) {
			case 'text':
			case 'textarea':
				$value = 'textarea' === $type ? sanitize_textarea_field( (string) $raw ) : sanitize_text_field( (string) $raw );
				$max   = (int) ( $field['max'] ?? 0 );
				if ( $max && mb_strlen( $value ) > $max ) {
					self::$errors[] = sprintf( '"%s" passou de %d caracteres e não foi alterado.', $field['label'], $max );
					return $previous;
				}
				return $value;
			case 'richtext':
				return wp_kses_post( (string) $raw );
			case 'url':
				return esc_url_raw( (string) $raw, array( 'https', 'http' ) );
			case 'email':
				return sanitize_email( (string) $raw );
			case 'number':
				return '' === $raw || null === $raw ? null : (int) $raw;
			case 'checkbox':
				return ! empty( $raw ) && '0' !== $raw;
			case 'select':
				$raw = (string) $raw;
				return array_key_exists( $raw, $field['options'] ) ? $raw : '';
			case 'image':
			case 'media':
			case 'post':
				return absint( $raw ) ?: null;
			case 'posts':
				return array_values( array_filter( array_map( 'absint', (array) $raw ) ) );
			case 'link':
				$raw   = is_array( $raw ) ? $raw : array();
				$label = sanitize_text_field( (string) ( $raw['label'] ?? '' ) );
				$url   = trim( (string) ( $raw['url'] ?? '' ) );
				if ( '' !== $url && ! preg_match( '#^(/|https?://|mailto:|tel:)#', $url ) ) {
					self::$errors[] = sprintf( 'Link "%s": use um caminho que comece com "/" ou uma URL completa.', $field['label'] );
					$url = '';
				}
				return array(
					'label' => mb_substr( $label, 0, 60 ) === $label ? $label : ( $previous['label'] ?? '' ),
					'type'  => 'url' === ( $raw['type'] ?? '' ) ? 'url' : 'post',
					'post'  => absint( $raw['post'] ?? 0 ) ?: null,
					'url'   => '' === $url ? '' : ( str_starts_with( $url, '/' ) ? sanitize_text_field( $url ) : esc_url_raw( $url ) ),
				);
			case 'repeater':
				$rows = array();
				$prev = is_array( $previous ) ? array_values( $previous ) : array();
				foreach ( array_values( is_array( $raw ) ? $raw : array() ) as $i => $row ) {
					if ( ! is_array( $row ) ) {
						continue;
					}
					$clean = self::sanitize_group( array( 'fields' => $field['fields'] ), $row, $prev[ $i ] ?? array() );
					if ( self::row_is_empty( $clean ) ) {
						continue;
					}
					$rows[] = $clean;
				}
				$max = (int) ( $field['max_rows'] ?? 0 );
				return $max ? array_slice( $rows, 0, $max ) : $rows;
		}
		return sanitize_text_field( (string) $raw );
	}

	private static function row_is_empty( array $row ): bool {
		foreach ( $row as $v ) {
			if ( is_array( $v ) ) {
				if ( ! empty( array_filter( $v, fn( $x ) => '' !== $x && null !== $x && 'post' !== $x ) ) ) {
					return false;
				}
			} elseif ( '' !== $v && null !== $v && false !== $v ) {
				return false;
			}
		}
		return true;
	}

	/** Campos obrigatórios vazios (para impedir publicação incompleta). */
	public static function missing_required( array $group, array $values ): array {
		$missing = array();
		foreach ( $group['fields'] as $field ) {
			if ( empty( $field['required'] ) ) {
				continue;
			}
			$v = $values[ $field['name'] ] ?? null;
			if ( null === $v || '' === $v || array() === $v ) {
				$missing[] = $field['label'];
			}
		}
		return $missing;
	}

	public static function take_errors(): array {
		$e            = self::$errors;
		self::$errors = array();
		return $e;
	}
}
