<?php defined('FCK_ROOT') || exit; $me = current_user(); $path = request_path(); ?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($page_title) ?> — Painel Full Connect Key</title>
<link rel="stylesheet" href="<?= e(asset('admin/admin.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('admin/vendor/quill.snow.css')) ?>">
</head>
<body class="adm">
<a class="adm-skip" href="#adm-main">Pular para o conteúdo</a>
<aside class="adm-side" id="adm-side">
	<a class="adm-brand" href="<?= e(url('/admin')) ?>">Full Connect Key <span>Painel</span></a>
	<nav aria-label="Painel">
		<a class="adm-nav-link<?= $path === '/admin' ? ' is-active' : '' ?>" href="<?= e(url('/admin')) ?>">Início</a>
		<?php foreach (admin_nav() as $group => $links) : ?>
			<p class="adm-nav-group"><?= e($group) ?></p>
			<?php foreach ($links as $l) : ?>
				<a class="adm-nav-link<?= str_starts_with($path, $l['href']) ? ' is-active' : '' ?>" href="<?= e(url($l['href'])) ?>"><?= e($l['label']) ?></a>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</nav>
</aside>
<div class="adm-wrap">
	<header class="adm-top">
		<button class="adm-menu-btn" type="button" aria-controls="adm-side" aria-expanded="false" data-adm-menu>Menu</button>
		<a href="<?= e(url('/')) ?>" target="_blank" rel="noopener">Ver site ↗</a>
		<span class="adm-user"><a href="<?= e(url('/admin/profile')) ?>"><?= e($me['name'] ?: $me['email']) ?></a> · <?= $me['role'] === 'admin' ? 'Administrador' : 'Editor' ?></span>
		<form method="post" action="<?= e(url('/admin/logout')) ?>"><?= csrf_field() ?><button class="btn-link" type="submit">Sair</button></form>
	</header>
	<main class="adm-main" id="adm-main" tabindex="-1">
		<?php foreach (flashes() as $f) : ?>
			<div class="adm-notice adm-notice--<?= e($f['type']) ?>" role="status">
				<?php if (count($f['messages']) === 1) : ?><p><?= e($f['messages'][0]) ?></p><?php else : ?><ul><?php foreach ($f['messages'] as $m) : ?><li><?= e($m) ?></li><?php endforeach; ?></ul><?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?= $content ?>
	</main>
</div>
<script src="<?= e(asset('admin/vendor/quill.js')) ?>"></script>
<script src="<?= e(asset('admin/admin.js')) ?>" data-csrf="<?= e(csrf_token()) ?>" data-base="<?= e(url('/')) ?>" id="adm-script"></script>
</body>
</html>
