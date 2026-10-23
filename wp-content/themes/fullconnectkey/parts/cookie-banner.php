<?php
/**
 * Banner e central de preferências de cookies.
 * Nada opcional é carregado sem consentimento; hoje não há ferramentas
 * opcionais aprovadas (assets/js/modules/consent.js → OPTIONAL_TOOLS vazio).
 */

defined( 'ABSPATH' ) || exit;

$c = static fn( string $k ): string => fck_ui( 'cookies', $k );
if ( ! $c( 'title' ) && ! $c( 'text' ) ) {
	return;
}
$mode       = (string) ( fck_site( 'cookies' )['banner_mode'] ?? 'auto' );
// A política de cookies é a página jurídica cujo seed é "page:cookies" (ou a primeira jurídica publicada).
$policy = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'meta_key' => '_fck_seed_key', 'meta_value' => 'page:cookies', 'posts_per_page' => 1 ) ); // phpcs:ignore
$policy_url = $policy ? get_permalink( $policy[0] ) : null;

$row = static function ( string $key, bool $locked = false ) use ( $c ): void {
	$id = 'cookie-pref-' . $key;
	?>
	<div class="pref-row">
		<div>
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $c( $key ) ); ?></label>
			<?php if ( $c( $key . '_description' ) ) : ?><p><?php echo esc_html( $c( $key . '_description' ) ); ?></p><?php endif; ?>
		</div>
		<div class="pref-row__control">
			<?php if ( $locked && $c( 'always_on' ) ) : ?><span><?php echo esc_html( $c( 'always_on' ) ); ?></span><?php endif; ?>
			<input type="checkbox" role="switch" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" <?php echo $locked ? 'checked disabled' : ''; ?>>
		</div>
	</div>
	<?php
};
?>
<div data-module="cookies" data-mode="<?php echo esc_attr( $mode ); ?>">
	<section class="cookie-banner" data-theme="dark" aria-labelledby="cookie-title" data-cookie-banner hidden>
		<h2 id="cookie-title"><?php echo esc_html( $c( 'title' ) ); ?></h2>
		<p>
			<?php echo esc_html( $c( 'text' ) ); ?>
			<?php if ( $policy_url && $c( 'policy_link' ) ) : ?>
				<a href="<?php echo esc_url( $policy_url ); ?>"><?php echo esc_html( $c( 'policy_link' ) ); ?></a>
			<?php endif; ?>
		</p>
		<div class="cookie-actions">
			<button class="btn btn--primary btn--sm" type="button" data-cookie-accept><?php echo esc_html( $c( 'accept_all' ) ); ?></button>
			<button class="btn btn--secondary btn--sm" type="button" data-cookie-reject><?php echo esc_html( $c( 'reject_optional' ) ); ?></button>
			<button class="btn btn--ghost btn--sm" type="button" data-cookie-open><?php echo esc_html( $c( 'customize' ) ); ?></button>
		</div>
	</section>

	<dialog class="cookie-dialog" data-theme="dark" aria-labelledby="cookie-prefs-title" data-cookie-dialog>
		<form method="dialog" data-cookie-form>
			<h2 id="cookie-prefs-title" class="display-xs"><?php echo esc_html( $c( 'preferences_title' ) ); ?></h2>
			<fieldset>
				<legend class="screen-reader-text"><?php echo esc_html( $c( 'preferences_title' ) ); ?></legend>
				<?php $row( 'necessary', true ); ?>
				<?php $row( 'analytics' ); ?>
				<?php $row( 'marketing' ); ?>
			</fieldset>
			<div class="cookie-actions">
				<button class="btn btn--primary btn--sm" type="submit" data-cookie-save><?php echo esc_html( $c( 'save' ) ); ?></button>
				<button class="btn btn--secondary btn--sm" type="button" data-cookie-accept><?php echo esc_html( $c( 'accept_all' ) ); ?></button>
				<button class="btn btn--ghost btn--sm" type="button" data-cookie-reject><?php echo esc_html( $c( 'reject_optional' ) ); ?></button>
			</div>
		</form>
	</dialog>
</div>
