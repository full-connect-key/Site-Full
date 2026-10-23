# Guia do editor — painel do site

Acesse **`/admin`** (ex.: `https://fullconnectkey.com.br/admin`) com seu e-mail e senha.

## Regra de ouro: rascunho × publicado
- **Salvar rascunho** guarda a alteração sem mudar o site.
- **Visualizar rascunho** abre o site como ele vai ficar. Só você, logado, vê essa prévia.
- **Publicar** coloca a alteração no ar.
- Todo registro tem **Histórico de versões**, com as 50 últimas. Em *Restaurar*, a versão escolhida volta como rascunho; depois é só publicar.

## Onde fica cada coisa
| Quero editar… | Vá em… |
|---|---|
| Textos da Home | Páginas › **Home** (cada seção tem "Exibir esta seção") |
| S&M, Ecossistema, Cases, Insights, Full na Mídia, Contato | Páginas › (página) |
| Resumos e páginas das plataformas | Ecossistema › **Plataformas** |
| Setores (Varejo, Imobiliário…) | Home › **Setores** |
| Cases e clientes | Cases › **Cases** / **Clientes** |
| Artigos, categorias e autores | Insights |
| Matérias de imprensa | Imprensa › **Full na Mídia** |
| Sócios, time e depoimentos | Pessoas |
| Imagens e vídeos | Mídia › **Biblioteca de mídia** |
| Logo, menus, rodapé, contatos, SEO padrão | Configurações › **Marca, menus, rodapé e SEO** |
| Botões, mensagens de erro, textos de acessibilidade, cookies, 404 | Configurações › **Textos de interface** |
| Endereços antigos que devem redirecionar | Configurações › **Redirecionamentos** |

## Boas práticas
- **Limites de caracteres**: o contador mostra quanto falta. Se passar do limite, o texto não é salvo e o painel avisa. O site nunca corta texto pela metade.
- **Imagens**: na Biblioteca, preencha o **texto alternativo**, que descreve a imagem para quem usa leitor de tela, ou marque "decorativa". Clique na imagem para definir o **ponto focal**, a área que nunca é cortada.
- **Direito de uso**: logos de clientes e de veículos de imprensa só aparecem com **"Direito de uso confirmado"** marcado.
- **Nada inventado**: depoimentos só com "Autorização registrada"; métricas só com "Número validado". Sem conteúdo real, a seção some sozinha, sem deixar buraco.
- **Links**: em botões, escolha "Página do site" sempre que possível. Assim o link continua certo mesmo se o endereço da página mudar.
- **Slug (endereço)**: ao mudar e publicar, o endereço antigo passa a redirecionar automaticamente.

## Novos conteúdos
- **Case**: Cases › Adicionar › preencha dados, KV, narrativa, galeria e resultados (se houver números reais) › Publicar. Ele entra sozinho na listagem, nos filtros e no sitemap. Para aparecer na Home, use "Destaque na Home" (1 = grande).
- **Artigo**: Insights › Adicionar › preencha título, resumo, capa, categoria e corpo › Publicar. Marque "Destaque" para abrir a página Insights.
- **Matéria**: Imprensa › Adicionar › veículo, data, tipo, link original e resumo próprio.

## Papéis
- **Editor**: conteúdo, mídia, menus e textos.
- **Administrador**: tudo isso, mais usuários, mensagens do formulário, destinatários e ferramentas.
