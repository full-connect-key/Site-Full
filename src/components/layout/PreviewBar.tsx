/** Barra visível apenas em preview autenticado (draft mode). Texto técnico do painel. */
export function PreviewBar() {
  return (
    <div
      role="status"
      className="fixed bottom-4 left-1/2 z-[90] flex -translate-x-1/2 items-center gap-4 border border-line bg-dark-card px-4 py-2 text-small text-fg shadow-lg"
    >
      {/* Texto do painel (ferramenta interna), não conteúdo editorial do site. */}
      <span>Preview</span>
      <a href="/next/exit-preview" className="underline decoration-accent underline-offset-4">
        ×
      </a>
    </div>
  )
}
