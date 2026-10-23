<?php
/**
 * CONFIGURAÇÃO DO SERVIDOR — copie este arquivo para app/config.php e
 * preencha. O app/config.php NÃO vai para o Git (contém senhas) e não é
 * apagado pelos deploys.
 */

return [
	// Ambiente: 'production' (indexável), 'staging' ou 'local' (sempre noindex).
	'env' => 'production',

	// Endereço público do site, sem barra no final.
	'url' => 'https://fullconnectkey.com.br',

	// Banco MySQL criado no hPanel da Hostinger (Bancos de dados › MySQL).
	'db' => [
		'host'     => 'localhost',
		'port'     => 3306,
		'name'     => '',
		'user'     => '',
		'password' => '',
	],

	// Chave secreta (gere uma longa e aleatória; não reutilize).
	// Ex.: https://www.random.org/strings/ ou: php -r "echo bin2hex(random_bytes(32));"
	'secret' => '',

	// E-mail de envio do formulário de contato (SMTP do e-mail da Hostinger).
	// Configure somente após aprovação. Vazio = mensagens ficam só no painel.
	'smtp' => [
		'host'       => '', // ex.: smtp.hostinger.com
		'port'       => 465,
		'secure'     => 'ssl', // 'ssl' (465) ou 'tls' (587)
		'user'       => '',
		'password'   => '',
		'from'       => '',
		'from_name'  => 'Full Connect Key',
	],
];
