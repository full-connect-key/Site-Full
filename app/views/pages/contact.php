<?php
/**
 * Contato — hero (pontos e linhas reagem ao cursor) e formulário
 * progressivo: etapa 1 assunto (aceita ?assunto=), etapa 2 dados e
 * consentimento. Com JS: etapas com foco gerenciado, validação no cliente,
 * envio em JSON e sucesso só após confirmação do servidor. Sem JS: as duas
 * etapas aparecem juntas e o envio volta com ?contato=sucesso|erro.
 * Back-end: app/lib/contact.php (POST /contato/enviar).
 */
defined( 'FCK_ROOT' ) || exit;
$f        = g( $entry, 'form' );
$subjects = array_values( array_filter( (array) ( $f['subjects'] ?? array() ), fn( $s ) => isset( fck_contact_subjects()[ $s['value'] ?? '' ] ) && has_text( $s['title'] ?? '' ) ) );
$pre      = query( 'assunto' );
$pre      = in_array( $pre, array_column( $subjects, 'value' ), true ) ? $pre : '';
$status   = query( 'contato' );
$total    = 2;
$step_of  = ui( 'form', 'step_of' );

// Consentimento: o trecho indicado vira link para a Política de Privacidade (só se publicada).
$consent   = e( (string) ( $f['consent'] ?? '' ) );
$priv_id   = (int) db_value( 'SELECT id FROM entries WHERE seed_key = ?', array( 'page:privacy' ) );
$privacy   = $priv_id ? entry_find( $priv_id ) : null;
$link_text = (string) ( $f['consent_link'] ?? '' );
if ( $privacy && has_text( $link_text ) && str_contains( $consent, e( $link_text ) ) ) {
	$consent = str_replace( e( $link_text ), '<a href="' . e( url_for( $privacy ) ) . '">' . e( $link_text ) . '</a>', $consent );
}
$limits = CONTACT_LIMITS;
$field  = static function ( string $name, string $type, bool $required ) use ( $f, $limits ): void {
	$label = (string) ( $f[ $name . '_label' ] ?? '' );
	$ph    = (string) ( $f[ $name . '_placeholder' ] ?? '' );
	$id    = 'cf-' . $name;
	?>
	<div class="field" data-field="<?php echo e( $name ); ?>">
		<label class="field__label" for="<?php echo e( $id ); ?>"><?php echo e( $label ); ?></label>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea class="field__input" id="<?php echo e( $id ); ?>" name="<?php echo e( $name ); ?>" rows="6" maxlength="<?php echo (int) $limits[ $name ]; ?>" placeholder="<?php echo e( $ph ); ?>" aria-describedby="<?php echo e( $id ); ?>-error"<?php echo $required ? ' required' : ''; ?>></textarea>
		<?php else : ?>
			<input class="field__input" id="<?php echo e( $id ); ?>" name="<?php echo e( $name ); ?>" type="<?php echo e( $type ); ?>" maxlength="<?php echo (int) $limits[ $name ]; ?>" placeholder="<?php echo e( $ph ); ?>" aria-describedby="<?php echo e( $id ); ?>-error"<?php echo 'email' === $type ? ' autocomplete="email" inputmode="email"' : ( 'name' === $name ? ' autocomplete="name"' : ' autocomplete="organization"' ); ?><?php echo $required ? ' required' : ''; ?>>
		<?php endif; ?>
		<p class="field__error" id="<?php echo e( $id ); ?>-error" hidden></p>
	</div>
	<?php
};

view( 'parts/page-hero', array( 'args' => array( 'data' => g( $entry, 'hero' ) ) ) );
?>
<section class="section contact" id="formulario" data-theme="dark" aria-labelledby="contact-step-1-title">
	<div class="container contact__inner">
		<div class="contact-success" data-success tabindex="-1"<?php echo 'sucesso' === $status ? '' : ' hidden'; ?>>
			<p class="contact-success__icon" aria-hidden="true"><?php echo icon( 'arrow' ); // phpcs:ignore ?></p>
			<h2 class="display-m contact-success__title"><?php echo e( $f['success_title'] ?? '' ); ?></h2>
			<?php text_el( $f['success_text'] ?? '', 'p', 'lead contact-success__text' ); ?>
			<?php if ( has_text( $f['success_cta'] ?? '' ) ) : ?>
				<a class="btn btn--primary" href="<?php echo e( url( '/' ) ); ?>"><span><?php echo e( $f['success_cta'] ); ?></span><?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>

		<form class="contact-form" action="<?php echo e( url( '/contato/enviar' ) ); ?>" method="post" novalidate data-module="contact-form" data-msg-required="<?php echo e( ui( 'form', 'required' ) ); ?>" data-msg-email="<?php echo e( ui( 'form', 'invalid_email' ) ); ?>" data-msg-consent="<?php echo e( ui( 'form', 'consent_required' ) ); ?>" data-msg-too-long="<?php echo e( ui( 'form', 'too_long' ) ); ?>" data-msg-error="<?php echo e( ui( 'form', 'send_error' ) ); ?>" data-msg-sending="<?php echo e( ui( 'form', 'sending' ) ); ?>"<?php echo 'sucesso' === $status ? ' hidden' : ''; ?>>
			<input type="hidden" name="_token" value="<?php echo e( contact_token() ); ?>">
			<div class="contact-form__hp" aria-hidden="true"><input id="cf-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>

			<p class="contact-form__alert" role="alert" data-alert<?php echo 'erro' === $status ? '' : ' hidden'; ?>><?php echo 'erro' === $status ? e( ui( 'form', 'send_error' ) ) : ''; ?></p>

			<fieldset class="contact-step" data-step="1" aria-describedby="contact-step-1-text">
				<legend class="contact-step__legend">
					<?php if ( has_text( $step_of ) ) : ?><span class="eyebrow contact-step__count"><?php echo e( fill( $step_of, array( 'n' => 1, 'total' => $total ) ) ); ?></span><?php endif; ?>
					<span class="display-s contact-step__title" id="contact-step-1-title" tabindex="-1"><?php echo e( $f['step1_title'] ?? '' ); ?></span>
				</legend>
				<?php text_el( $f['step1_text'] ?? '', 'p', 'muted contact-step__text', array( 'id' => 'contact-step-1-text' ) ); ?>
				<div class="subject-grid" data-field="subject">
					<?php foreach ( $subjects as $i => $s ) : ?>
						<label class="subject">
							<input class="subject__input" type="radio" name="subject" value="<?php echo e( $s['value'] ); ?>"<?php echo $pre === $s['value'] ? ' checked' : ''; ?> aria-describedby="cf-subject-error" required>
							<span class="subject__box">
								<span class="subject__title"><?php echo e( $s['title'] ); ?></span>
								<?php text_el( $s['description'] ?? '', 'span', 'subject__desc small' ); ?>
							</span>
						</label>
					<?php endforeach; ?>
					<p class="field__error" id="cf-subject-error" hidden></p>
				</div>
				<div class="contact-step__actions" data-js-only hidden>
					<button class="btn btn--primary" type="button" data-next><span><?php echo e( $f['continue'] ?? '' ); ?></span><?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></button>
				</div>
			</fieldset>

			<fieldset class="contact-step" data-step="2" aria-describedby="contact-step-2-text">
				<legend class="contact-step__legend">
					<?php if ( has_text( $step_of ) ) : ?><span class="eyebrow contact-step__count"><?php echo e( fill( $step_of, array( 'n' => 2, 'total' => $total ) ) ); ?></span><?php endif; ?>
					<span class="display-s contact-step__title" id="contact-step-2-title" tabindex="-1"><?php echo e( $f['step2_title'] ?? '' ); ?></span>
				</legend>
				<?php text_el( $f['step2_text'] ?? '', 'p', 'muted contact-step__text', array( 'id' => 'contact-step-2-text' ) ); ?>
				<div class="contact-fields">
					<?php $field( 'name', 'text', true ); ?>
					<?php $field( 'email', 'email', true ); ?>
					<?php $field( 'company', 'text', false ); ?>
					<?php $field( 'message', 'textarea', true ); ?>
				</div>
				<div class="field field--check" data-field="consent">
					<input class="check__input" id="cf-consent" name="consent" type="checkbox" value="1" required aria-describedby="cf-consent-error">
					<label class="check__label small" for="cf-consent"><?php echo $consent; // phpcs:ignore -- texto escapado acima; só o link é marcação. ?></label>
					<p class="field__error" id="cf-consent-error" hidden></p>
				</div>
				<div class="contact-step__actions">
					<button class="btn btn--ghost" type="button" data-back data-js-only hidden><?php echo icon( 'arrow-left', 'btn__arrow' ); // phpcs:ignore ?><span><?php echo e( $f['back'] ?? '' ); ?></span></button>
					<button class="btn btn--primary" type="submit" data-submit><span><?php echo e( $f['submit'] ?? '' ); ?></span><?php echo icon( 'arrow', 'btn__arrow' ); // phpcs:ignore ?></button>
				</div>
				<p class="contact-form__status small" role="status" data-status></p>
			</fieldset>
		</form>
	</div>
</section>
