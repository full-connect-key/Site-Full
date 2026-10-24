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
- **Case**: Cases › Adicionar › preencha:
  - dados: cliente, setor, categoria, headline, resumo e KV;
  - narrativa: O desafio, A ideia, Nossa atuação;
  - galeria: imagens ou vídeos, cada um com o tamanho no grid;
  - resultados: só números reais.

  Depois, Publicar. Ele entra sozinho na listagem, no filtro do setor (os filtros mostram só setores que têm case), na rota `/cases/slug` e no sitemap.
  - Para aparecer na Home, use "Destaque na Home" (1 = grande).
  - "Próximo case" vazio = o seguinte da listagem; a ordem é o campo "Ordem" na lista Cases › Cases.
  - Na página Cases › "Página do case" ficam os rótulos (O desafio, A ideia…). Em "Listagem" ficam o texto do filtro "Todos", "Carregar mais" e quantos aparecem por vez.
- **Artigo**: Insights › Adicionar › preencha título, subtítulo, resumo, capa, categoria, autor e corpo (títulos, listas, citações, imagens e vídeos do YouTube/Vimeo) › Publicar.
  - A leitura estimada é calculada sozinha.
  - Marque "Destaque" para o artigo abrir a página Insights e a seção da Home.
  - "Relacionados" vazio = mais recentes da mesma categoria.
  - Os filtros por categoria usam o slug da categoria (`?categoria=...`).
- **Matéria**: Imprensa › Adicionar › manchete original (título), veículo, data, tipo (entrevista, reportagem ou artigo), link original e resumo próprio. Capa e logo do veículo são opcionais; o logo só aparece com direito de uso confirmado.
  - Na página Full na Mídia, "Mínimo de matérias para exibir arquivo e filtros" define quando aparecem a lista completa e os filtros por tipo. Abaixo disso, só a faixa de destaque.

## Plataformas do Ecossistema
Cada plataforma (Ecossistema › Plataformas) tem 7 blocos, na ordem em que aparecem no site:
1. Hero
2. Problema
3. Como funciona
4. Benefícios
5. Interface
6. Conexões
7. CTA final

Todo bloco tem "Exibir esta seção", e um bloco vazio some sozinho.

- **Trocar o nome de uma plataforma:** mude o título do registro e publique. Nos textos, o nome aparece como `{platform}`, então tudo acompanha: chamada do hero, botões, SEO, cards da Home e do Ecossistema. O endereço só muda se você trocar o slug; o antigo passa a redirecionar sozinho.
- **Ordem:** campo "Ordem" na lista de plataformas (vale para a Home, o Ecossistema e a navegação entre as soluções). Para os menus, ajuste em Configurações › Menus.
- **Hero:** a chamada "nome · verbo" é montada sozinha a partir do campo "Verbo". O botão principal leva ao contato com o assunto "projeto" e a plataforma já marcados.
- **Títulos alternativos:** ficam só no painel, para testar copy. Para usar um deles, copie-o para o "Título principal" e publique.
- **Como funciona:** cada passo vira uma etapa da animação, que acontece conforme a pessoa rola a página. "Animação guiada pela rolagem" desligado mostra os passos empilhados. Os rótulos dos visuais ficam em "3a. Ajustes do visual" e são sempre conceituais, sem números.
- **Interface:** só capturas ou vídeos reais, com "Confirmo que é a interface real" marcado. Sem isso, o bloco não aparece.
- **Conexões:** escolha a plataforma relacionada e escreva como elas se complementam. O site lembra que cada solução pode ser contratada sozinha.
- **Validar uma afirmação ("claim"):** passos, benefícios, nota e conexões têm "Status da afirmação".
  - *A validar*: aparece só no preview e no staging, com o selo "A validar", e **some em produção**.
  - Depois de confirmar com o time de produto, mude para *Confirmado* e publique (ou reescreva o texto).
  - A lista atualizada fica em Início › Pendências de conteúdo e em `docs/CLAIMS_REGISTER.md`.
- **Assinatura da Home:** Configurações › Marca › Assinatura (usada quando o título alternativo da Home está vazio).

## Formulário de contato
- **Textos e opções**: Páginas › Contato. As opções de assunto ficam em "Opções de assunto". Links como `/contato?assunto=imprensa` já abrem com o assunto marcado (assuntos: `projeto`, `imprensa`, `parceria`, `outro`).
- **Consentimento**: o trecho indicado em "Trecho do consentimento que vira link" vira link para a Política de Privacidade quando ela estiver publicada.
- **Mensagens recebidas**: Configurações › Mensagens de contato (só administradores). Toda mensagem fica salva no painel, mesmo se o e-mail falhar. Quando o contato vem de uma página de plataforma, a mensagem mostra a plataforma de origem.
- **Destinatários por assunto e prazo de retenção**: Configurações › Formulário: destinatários.

## Papéis
- **Editor**: conteúdo, mídia, menus e textos.
- **Administrador**: tudo isso, mais usuários, mensagens do formulário, destinatários e ferramentas.
