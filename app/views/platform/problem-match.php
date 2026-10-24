<?php
/**
 * FullMatch · Problema — a mesma oferta replicada para todos (genérico) se
 * adapta a cada ponto com a rolagem (personalizado); alguns pontos se
 * apagam (quem parou de comprar).
 */
defined( 'FCK_ROOT' ) || exit;
?>
<div class="pv pv-match" aria-hidden="true">
	<?php for ( $i = 0; $i < 6; $i++ ) : ?>
		<span class="pv-match__item<?php echo in_array( $i, array( 2, 5 ), true ) ? ' is-fading' : ''; ?>" style="--i:<?php echo (int) $i; ?>"><span class="pv-match__card"></span><span class="pv-match__dot"></span></span>
	<?php endfor; ?>
</div>
