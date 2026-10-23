<?php
/**
 * WP-CLI: wp fck seed [--reset]
 */

defined( 'ABSPATH' ) || exit;

WP_CLI::add_command(
	'fck seed',
	static function ( array $args, array $assoc ): void {
		foreach ( fck_run_seed( ! empty( $assoc['reset'] ) ) as $line ) {
			WP_CLI::log( $line );
		}
		WP_CLI::success( 'Conteúdo inicial verificado.' );
	},
	array(
		'shortdesc' => 'Importa o conteúdo inicial (idempotente). --reset recria tudo (só em ambiente local).',
		'synopsis'  => array(
			array( 'type' => 'flag', 'name' => 'reset', 'optional' => true ),
		),
	)
);

WP_CLI::add_command(
	'fck gaps',
	static function (): void {
		$rows = fck_content_gaps();
		if ( ! $rows ) {
			WP_CLI::success( 'Nenhuma pendência detectada.' );
			return;
		}
		WP_CLI\Utils\format_items( 'table', $rows, array( 'priority', 'item', 'where' ) );
	},
	array( 'shortdesc' => 'Lista pendências de conteúdo detectadas no banco.' )
);
