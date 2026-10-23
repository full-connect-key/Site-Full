<?php
/**
 * Menus do painel (Aparência › Menus) convertidos em árvore para os templates.
 * Itens que apontam para conteúdo não publicado não aparecem.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array{label:string,href:string,current:bool,external:bool,product:string,children:array}>
 */
function fck_menu_tree( string $location ): array {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	if ( ! $items ) {
		return array();
	}
	_wp_menu_item_classes_by_context( $items );

	$nodes = array();
	foreach ( $items as $item ) {
		if ( ! empty( $item->_invalid ) ) {
			continue;
		}
		if ( 'post_type' === $item->type && ! fck_is_visible( (int) $item->object_id ) ) {
			continue;
		}
		$href    = '#' === $item->url ? '' : (string) $item->url;
		$product = '';
		if ( 'fck_platform' === $item->object ) {
			$product = (string) ( fck_meta( (int) $item->object_id, 'identity' )['product'] ?? '' );
		}
		$nodes[ $item->ID ] = array(
			'label'    => (string) $item->title,
			'href'     => $href,
			'current'  => in_array( 'current-menu-item', (array) $item->classes, true ),
			'ancestor' => in_array( 'current-menu-ancestor', (array) $item->classes, true ) || in_array( 'current-menu-parent', (array) $item->classes, true ),
			'external' => '' !== $href && fck_is_external( $href ),
			'product'  => $product,
			'parent'   => (int) $item->menu_item_parent,
			'children' => array(),
		);
	}
	$tree = array();
	foreach ( $nodes as $id => &$node ) {
		if ( $node['parent'] && isset( $nodes[ $node['parent'] ] ) ) {
			$nodes[ $node['parent'] ]['children'][] = &$node;
		} elseif ( ! $node['parent'] ) {
			$tree[] = &$node;
		}
	}
	unset( $node );
	return $tree;
}
