<?php defined('FCK_ROOT') || exit; ?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($page_title) ?> — Painel Full Connect Key</title>
<link rel="stylesheet" href="<?= e(asset('admin/admin.css')) ?>">
</head>
<body class="adm adm-bare">
<main class="adm-card" id="adm-main">
	<p class="adm-brand">Full Connect Key <span>Painel</span></p>
	<?php foreach (session_status() === PHP_SESSION_ACTIVE ? flashes() : [] as $f) : ?>
		<div class="adm-notice adm-notice--<?= e($f['type']) ?>"><?php foreach ($f['messages'] as $m) : ?><p><?= e($m) ?></p><?php endforeach; ?></div>
	<?php endforeach; ?>
	<?= $content ?>
</main>
</body>
</html>
