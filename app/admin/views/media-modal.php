<?php defined('FCK_ROOT') || exit; ?>
<dialog class="adm-modal" id="media-modal" aria-labelledby="media-modal-title">
	<div class="adm-modal-head">
		<h2 id="media-modal-title">Selecionar mídia</h2>
		<button class="btn-link" type="button" data-modal-close>Fechar</button>
	</div>
	<div class="adm-modal-tools">
		<input type="search" placeholder="Buscar por nome ou texto alternativo" data-media-search aria-label="Buscar mídia">
		<label class="btn-admin">Enviar arquivo<input type="file" multiple hidden data-media-upload accept="image/*,video/mp4,video/webm,application/pdf"></label>
	</div>
	<p class="adm-muted" data-media-status role="status"></p>
	<ul class="adm-media-grid" data-media-grid></ul>
</dialog>
