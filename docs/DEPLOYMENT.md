# Deploy, ambiente local, backup e rollback

Site em **PHP 8.1+ e MySQL**, com painel próprio em `/admin`. Não usa WordPress nem etapa de build. O repositório inteiro é a pasta pública do site: o `index.php` fica na raiz e o `.htaccess` bloqueia o acesso a `app/`, `storage/`, `docs/`, `tests/` e `scripts/`.

## 1. Hostinger: primeiro deploy (uma vez)

1. **PHP**: em hPanel › Avançado › Configuração do PHP, escolha **PHP 8.1 ou superior** (recomendado 8.3). As extensões necessárias já vêm ativas na Hostinger: `pdo_mysql`, `gd` (com WebP), `fileinfo`, `mbstring` e `iconv`.
2. **Banco**: em hPanel › Bancos de dados › MySQL, crie um banco e um usuário e anote o nome do banco, o usuário, a senha e o host (normalmente `localhost`).
3. **Git**: em hPanel › Avançado › **Git**:
   - Repositório: `git@github.com:full-connect-key/Site-Full.git`. Como o repositório é privado, copie a **chave SSH** que a Hostinger mostra e cadastre-a no GitHub em *Settings › Deploy keys*, só leitura.
   - Branch: a branch publicada (ex.: `main`).
   - Diretório: deixe vazio para instalar em `public_html`. A pasta precisa estar vazia no primeiro deploy; apague o `default.php` da Hostinger, se existir.
   - Clique em **Criar** e depois em **Implantar**.
4. **Configuração**: no Gerenciador de Arquivos, copie `public_html/app/config.sample.php` para `public_html/app/config.php` e preencha:
   - `url`: endereço público com `https://`;
   - `db`: dados do passo 2;
   - `secret`: texto aleatório de 64 caracteres;
   - `env`: `production` só quando o site for ao ar; até lá, `staging`, que deixa o site fora dos buscadores.
5. **Permissões**: as pastas `uploads/` e `storage/` precisam permitir escrita (755 já basta na Hostinger).
6. **Instalação**: acesse `https://SEU-DOMINIO/admin/install`, informe o e-mail e a senha (12+ caracteres) do primeiro administrador e clique em **Instalar**. O instalador cria as tabelas, o usuário e importa o conteúdo inicial: textos aprovados, menus e logos oficiais.
7. **HTTPS**: ative o SSL em hPanel › Segurança › SSL. O `.htaccess` já redireciona http para https.

> `app/config.php`, `uploads/` e `storage/` **não estão no Git**. Os deploys seguintes não apagam esses arquivos, nem o banco.

## 2. Deploys seguintes

- Envie os commits para a branch configurada e clique em **Implantar** no hPanel. Para automatizar, cadastre a URL de *webhook* que a Hostinger mostra em GitHub › Settings › Webhooks.
- Se uma versão trouxer **migrations** (arquivos novos em `app/migrations/`), elas são aplicadas sozinhas no próximo login de um administrador. Também dá para aplicá-las em Painel › Ferramentas › *Aplicar atualizações*. Elas só criam ou acrescentam, nunca apagam dados.

## 3. Rollback

- **Código**: no GitHub, reverta o commit com `git revert` e faça o deploy de novo. Outra opção é apontar o deploy da Hostinger para um commit ou tag anterior.
- **Conteúdo**: cada registro tem **Histórico de versões**, com as 50 últimas, em Painel › editar › *Histórico de versões* › *Restaurar*. A versão restaurada vira rascunho; publique para aplicar no site.
- **Banco inteiro**: restaure um backup (item 4).

## 4. Backup e restauração

- **Banco**: a Hostinger faz backups automáticos (hPanel › Arquivos › Backups). Antes de mudanças grandes, exporte também pelo phpMyAdmin (*Exportar* › SQL). Para restaurar, use phpMyAdmin › *Importar*.
- **Arquivos enviados**: faça backup da pasta `public_html/uploads/` (hPanel › Backups ou download pelo Gerenciador de Arquivos).
- **Configuração**: guarde uma cópia segura do `app/config.php` fora do servidor, por exemplo num gerenciador de senhas.
- Linha de comando (ambiente local ou servidor com SSH):
  ```bash
  mysqldump -u USUARIO -p NOME_DO_BANCO > backup-$(date +%F).sql   # exportar
  mysql -u USUARIO -p NOME_DO_BANCO < backup-2026-10-23.sql         # restaurar
  tar czf uploads-$(date +%F).tgz uploads/
  ```

## 5. E-mail do formulário (só após aprovação)

Preencha `smtp` em `app/config.php` com uma caixa da Hostinger: `smtp.hostinger.com`, porta 465, `ssl`, usuário e senha da caixa. Depois cadastre os destinatários em Painel › Configurações › *Formulário: destinatários*. Sem esses dois passos, as mensagens ficam salvas só em *Mensagens de contato*.

## 6. Ambiente local

Requisitos: PHP 8.1+ (com `pdo_mysql` e `gd`), MySQL ou MariaDB e Node 20+ (só para os testes).

```bash
# 1) banco
mysql -uroot -e "CREATE DATABASE fck_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                 CREATE USER IF NOT EXISTS 'fck'@'localhost' IDENTIFIED BY 'fck_dev';
                 GRANT ALL ON fck_site.* TO 'fck'@'localhost'; GRANT ALL ON fck_site_test.* TO 'fck'@'localhost';"
# 2) configuração
cp app/config.sample.php app/config.php   # env 'local', url http://localhost:8080, db fck_site/fck/fck_dev
# 3) estrutura + conteúdo + admin
php scripts/fck.php migrate
php scripts/fck.php seed
php scripts/fck.php create-admin voce@exemplo.com "uma-senha-longa-123"
# 4) servidor
npm run serve        # http://localhost:8080  (painel: /admin)
```

Alternativa com Docker: `docker compose up -d` (PHP + Apache + MySQL + Mailpit). Nesse caso, use `db.host = "db"` no `app/config.php`.

## 7. Testes

```bash
npm install
npm run check        # cores proibidas, textos fixos nos templates, tokens/contraste
npm run test:db      # recria o banco de TESTE (fck_site_test) com fixtures fictícias
npx playwright test  # e2e: painel, site, acessibilidade (axe), segurança — 375/768/1280/1440
```

Os testes usam um banco separado (`fck_site_test`) e a pasta `uploads-test/`. As fixtures fictícias nunca vão para o banco de revisão nem para a produção.

## 8. Checklist de pré-lançamento

- [ ] `env` = `production` e `url` com o domínio final em `app/config.php`
- [ ] SSL ativo e http redirecionando para https
- [ ] Textos jurídicos aprovados e páginas de Privacidade e Cookies publicadas
- [ ] SMTP configurado, destinatários cadastrados e envio de teste feito
- [ ] Decisão sobre o banner de cookies (Configurações do site › Cookies)
- [ ] Redirecionamentos dos endereços do site atual (`fullconnectkey.com.br`) cadastrados em Painel › Redirecionamentos
- [ ] Senha do administrador forte e usuários editores criados
- [ ] Backup inicial do banco e de `uploads/`
