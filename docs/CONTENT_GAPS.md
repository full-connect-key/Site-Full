# Pendências de conteúdo, credenciais e ativos

Sem dados fictícios: enquanto um item não chega, a seção correspondente fica oculta ou usa um substituto neutro. O painel (Início › *Pendências de conteúdo*) mostra a lista viva, calculada a partir do banco.

| Prioridade | Item | Onde aparece | Impacto | Substituto atual |
|---|---|---|---|---|
| **P0** | Textos jurídicos aprovados: Política de Privacidade, Política de Cookies, texto de consentimento do formulário e descrições das categorias de cookies | Rodapé, formulário, banner de cookies | Lançamento | Páginas em rascunho (404 para o público); links do rodapé ocultos |
| **P0** | Destinatários do formulário de contato | Contato | Lançamento | Mensagens salvas só no painel |
| **P0** | SMTP aprovado (caixa de e-mail da Hostinger) | Contato | Lançamento | Nenhum e-mail enviado |
| **P0** | Domínio, SSL e `app/config.php` de produção | Site todo | Lançamento | Ambiente de staging (noindex) |
| **P0** | Decisão sobre o banner de cookies (ADR-007) | Site todo | Lançamento | Modo automático (oculto) |
| P1 | KVs e imagens de cases com autorização | Home › Cases, página Cases | Seções ocultas | Seção omitida |
| — | ~~Logos de clientes autorizados~~ (19 logos publicados em 24/10/2026; Brentwood saiu a pedido do cliente) | Home › faixa de logos | Publicada | — |
| P1 | Time: fotos e frases "Conecta X a Y." | Home › O Time | Seção oculta | Seção omitida |
| P1 | Depoimentos reais com autorização registrada | Home › Depoimentos | Seção oculta | Seção omitida |
| P1 | Imagens dos setores (Varejo, Imobiliário, Feiras e Eventos) | Home › Setores | Sem imagem | Só texto |
| P2 | Métricas institucionais e por case verificadas | Home › Cases, página do case | Faixa de indicadores oculta | Omitida |
| P2 | Artigos reais | Home › Insights, página Insights | Seção oculta | Omitida |
| P2 | Matérias de imprensa reais, com links | Full na Mídia | Estado vazio | Texto de estado vazio |
| P2 | Canais oficiais de contato e redes sociais | Rodapé | Sem contatos | Omitidos |
| P2 | Revisão dos canais ilustrativos do simulador FullMix (ADR-009) | Página FullMix | — | Canais genéricos |
| **P0** | Confirmar a grafia oficial dos nomes das plataformas (D1: FullHub, FullDash, FullLeads) e a assinatura "Vire a chave dos seus resultados. Ative o modo Full." (D2) | Site todo, Home | Lançamento | Padrão do adendo aplicado |
| **P0** | Validar as 15 afirmações de `docs/CLAIMS_REGISTER.md` com o time de produto (e a nota de LGPD do FullMatch com o jurídico) | Páginas das plataformas | Itens omitidos em produção | Aparecem só em staging/preview |
| P1 | Capturas ou vídeos **reais** da interface de cada plataforma | Plataformas › bloco Interface | Bloco oculto | Omitido |
| P1 | Confirmar se a Full oferece demonstração comercial (D4) | CTAs das plataformas | — | "Conversar sobre o {plataforma}" |
| P2 | Escolher entre o título principal e os títulos alternativos de cada plataforma (opcional) | Hero das plataformas | — | Título principal do adendo |
| Revisar | Microtextos de interface criados no desenvolvimento (marcados com `(*)` em `app/seed/content.php`, ex.: "Pular para o conteúdo", "(abre em nova aba)") | Acessibilidade e rótulos | — | Editáveis em Configurações › Textos de interface |

**Recebido:** sócios completos (nomes, cargos, bios e retratos de Rita Frare, Emerson Aguiar e Felipe Pelá — publicados); assinatura "powered by 4REAL" no logo do FullMatch confirmada pelo cliente (mantida); logos oficiais da Full (completo e compacto, versões para fundo escuro e claro) e das 5 plataformas (escuro e claro), e a tipografia Google Sans.
