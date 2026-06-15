<?php
// includes/card_produto.php — Card de produto com imagem real
$temPromocao = !empty($produto['preco_promocional']) && $produto['preco_promocional'] < $produto['preco'];
$precoExibir = $temPromocao ? $produto['preco_promocional'] : $produto['preco'];
$desconto    = $temPromocao ? round((1 - $precoExibir / $produto['preco']) * 100) : 0;

// Imagem do produto (do banco ou fallback por categoria)
$imgFallback = [
  '1' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=220&fit=crop&auto=format&q=80',
  '2' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=220&fit=crop&auto=format&q=80',
  '3' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=220&fit=crop&auto=format&q=80',
  '4' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=220&fit=crop&auto=format&q=80',
  '5' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&h=220&fit=crop&auto=format&q=80',
];
$imgSrc = !empty($produto['imagem'])
    ? htmlspecialchars($produto['imagem'])
    : ($imgFallback[$produto['categoria_id']] ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=220&fit=crop&auto=format&q=80');
?>
<div class="card h-100 border-0 shadow-sm produto-card">

  <!-- Imagem com badges -->
  <div class="card-img-wrapper">
    <img src="<?= $imgSrc ?>"
         alt="<?= htmlspecialchars($produto['nome']) ?>"
         loading="lazy"
         onerror="this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=220&fit=crop&q=80'">

    <?php if ($temPromocao): ?>
    <span class="badge-overlay badge-desconto">-<?= $desconto ?>%</span>
    <?php endif; ?>

    <?php if (!empty($produto['destaque'])): ?>
    <span class="badge-destaque badge-destaque-pill">⭐ Destaque</span>
    <?php endif; ?>
  </div>

  <div class="card-body d-flex flex-column">
    <span class="categoria-label"><?= htmlspecialchars($produto['categoria_nome'] ?? '') ?></span>

    <h6 class="card-title mt-1 mb-3"><?= htmlspecialchars($produto['nome']) ?></h6>

    <div class="mt-auto">
      <!-- Preço -->
      <?php if ($temPromocao): ?>
      <div class="preco-original"><?= formatarMoeda($produto['preco']) ?></div>
      <?php endif; ?>
      <div class="preco-atual"><?= formatarMoeda($precoExibir) ?></div>
      <div class="parcelamento mb-2">
        12x de <?= formatarMoeda($precoExibir / 12) ?> sem juros
      </div>

      <!-- Estoque baixo -->
      <?php if ($produto['estoque'] > 0 && $produto['estoque'] <= 5): ?>
      <span class="badge bg-warning text-dark small mb-2">
        ⚠️ Últimas <?= $produto['estoque'] ?> unidades
      </span>
      <?php elseif ($produto['estoque'] == 0): ?>
      <span class="badge bg-secondary small mb-2">Sem estoque</span>
      <?php endif; ?>

      <!-- Botões -->
      <div class="d-grid gap-2 mt-2">
        <?php if ($produto['estoque'] > 0): ?>
        <a href="/loja/carrinho.php?acao=adicionar&id=<?= $produto['id'] ?>&nome=<?= urlencode($produto['nome']) ?>&preco=<?= $precoExibir ?>"
           class="btn btn-carrinho">
          🛒 Adicionar ao Carrinho
        </a>
        <?php else: ?>
        <button class="btn btn-secondary btn-sm" disabled>Indisponível</button>
        <?php endif; ?>
        <a href="/loja/produto.php?id=<?= $produto['id'] ?>" class="btn btn-detalhe">
          Ver detalhes
        </a>
      </div>
    </div>
  </div>
</div>
