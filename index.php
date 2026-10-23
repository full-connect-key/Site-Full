<?php
/**
 * Full Connect Key — controlador frontal.
 * Todas as requisições (site e painel) passam por aqui.
 */

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

fck_dispatch();
