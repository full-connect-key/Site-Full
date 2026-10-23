<?php
/**
 * Banner e central de preferências de cookies.
 * Nada opcional é carregado sem consentimento; hoje não há ferramentas
 * opcionais aprovadas (assets/js/modules/consent.js → OPTIONAL_TOOLS vazio).
 */

defined( 'FCK_ROOT' ) || exit;

$c = static fn( string $k ): string => ui( 'cookies', $k );
if ( ! $c( 'title' ) && ! $c( 'text' ) ) {
	return;
}
$mode       = (string) ( site( 'cookies' )['banner_mode'] ?? 'auto' );
// A política de cookies é a página jurídica cujo seed é "page:cookies" (ou a primeira jurídica publicada).
$policy_id  = (int) db_value( "SELECT id FROM entries WHERE seed_key = 'page:cookies'" );
$policy     = $policy_id ? entry_find( $policy_id ) : null;
$policy_url = $policy ? url_for( $policy ) : null;

$row = static function ( string $key, bool $locked = false ) use ( $c ): void {
	$id = 'cookie-pref-' . $key;
	?>
	<div class="pref-row">
		<div>
			<label for="<?php echo e( $id ); ?>"><?php echo e( $c( $key ) ); ?></label>
			<?php if ( $c( $key . '_description' ) ) : ?><p><?php echo e( $c( $key . '_description' ) ); ?></p><?php endif; ?>
		</div>
		<div class="pref-row__control">
			<?php if ( $locked && $c( 'always_on' ) ) : ?><span><?php echo e( $c( 'always_on' ) ); ?></span><?php endif; ?>
			<input type="checkbox" role="switch" id="<?php echo e( $id ); ?>" name="<?php echo e( $key ); ?>" <?php echo $locked ? 'checked disabled' : ''; ?>>
		</div>
	</div>
	<?php
};
?>
<div data-module="cookies" data-mode="<?php echo e( $mode ); ?>">
	<section class="cookie-banner" data-theme="dark" aria-labelledby="cookie-title" data-cookie-banner hidden>
		<h2 id="cookie-title"><?php echo e( $c( 'title' ) ); ?></h2>
		<p>
			<?php echo e( $c( 'text' ) ); ?>
			<?php if ( $policy_url && $c( 'policy_link' ) ) : ?>
				<a href="<?php echo e( $policy_url ); ?>"><?php echo e( $c( 'policy_link' ) ); ?></a>
			<?php endif; ?>
		</p>
		<div class="cookie-actions">
			<button class="btn btn--primary btn--sm" type="button" data-cookie-accept><?php echo e( $c( 'accept_all' ) ); ?></button>
			<button class="btn btn--secondary btn--sm" type="button" data-cookie-reject><?php echo e( $c( 'reject_optional' ) ); ?></button>
			<button class="btn btn--ghost btn--sm" type="button" data-cookie-open><?php echo e( $c( 'customize' ) ); ?></button>
		</div>
	</section>

	<dialog class="cookie-dialog" data-theme="dark" aria-labelledby="cookie-prefs-title" data-cookie-dialog>
		<form method="dialog" data-cookie-form>
			<h2 id="cookie-prefs-title" class="display-xs"><?php echo e( $c( 'preferences_title' ) ); ?></h2>
			<fieldset>
				<legend class="screen-reader-text"><?php echo e( $c( 'preferences_title' ) ); ?></legend>
				<?php $row( 'necessary', true ); ?>
				<?php $row( 'analytics' ); ?>
				<?php $row( 'marketing' ); ?>
			</fieldset>
			<div class="cookie-actions">
				<button class="btn btn--primary btn--sm" type="submit" data-cookie-save><?php echo e( $c( 'save' ) ); ?></button>
				<button class="btn btn--secondary btn--sm" type="button" data-cookie-accept><?php echo e( $c( 'accept_all' ) ); ?></button>
				<button class="btn btn--ghost btn--sm" type="button" data-cookie-reject><?php echo e( $c( 'reject_optional' ) ); ?></button>
			</div>
		</form>
	</dialog>
</div>
