<?php
// produto.php — Página de detalhe de um produto
require_once __DIR__ . '/includes/header.php';

// Valida o ID recebido na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['mensagem']      = 'Produto inválido.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: /loja/produtos.php');
    exit;
}

// Busca todos os produtos como array e localiza o produto pelo ID
$todosProdutos = getProdutosArray($pdo);
$produto       = buscarProdutoPorId($todosProdutos, $id);

if (!$produto) {
    $_SESSION['mensagem']      = 'Produto não encontrado.';
    $_SESSION['tipo_mensagem'] = 'warning';
    header('Location: /loja/produtos.php');
    exit;
}

// Define o título da página dinamicamente
$tituloPagina = htmlspecialchars($produto['nome']);

// Calcula preço com possível promoção
$temPromocao = !empty($produto['preco_promocional']) && $produto['preco_promocional'] < $produto['preco'];
$precoExibir = $temPromocao ? $produto['preco_promocional'] : $produto['preco'];
$desconto    = $temPromocao ? round((1 - $precoExibir / $produto['preco']) * 100) : 0;

// Aplica desconto no parcelamento (usa função aplicarDesconto)
$precoParcelado = aplicarDesconto($precoExibir, 5); // 5% no pix

// Calcula frete estimado (usa função calcularFrete)
$frete = calcularFrete($precoExibir);

// Busca produtos relacionados (mesma categoria, usando filtrarProdutos)
$filtroRelacionados = ['categoria_id' => $produto['categoria_id']];
$produtosRelacionados = filtrarProdutos($todosProdutos, $filtroRelacionados);
// Remove o produto atual da lista e limita a 4
$produtosRelacionados = array_filter($produtosRelacionados, fn($p) => $p['id'] != $produto['id']);
$produtosRelacionados = array_slice(array_values($produtosRelacionados), 0, 4);

// Emojis por categoria
$emojis = ['1' => '📱', '2' => '💻', '3' => '🎮', '4' => '🔊', '5' => '📷'];
$emoji  = $emojis[$produto['categoria_id']] ?? '🛒';
$cores  = ['1' => '#e3f2fd', '2' => '#f3e5f5', '3' => '#e8f5e9', '4' => '#fff3e0', '5' => '#fce4ec'];
$cor    = $cores[$produto['categoria_id']] ?? '#f5f5f5';
?>

<div class="container my-4">

    <!-- Breadcrumb (Bootstrap Componente #6) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/loja/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/loja/produtos.php">Produtos</a></li>
            <li class="breadcrumb-item">
                <a href="/loja/produtos.php?categoria_id=<?= $produto['categoria_id'] ?>">
                    <?= htmlspecialchars($produto['categoria_nome'] ?? 'Categoria') ?>
                </a>
            </li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($produto['nome']) ?></li>
        </ol>
    </nav>

    <!-- Detalhe do produto -->
    <div class="row g-5">
        <!-- Imagem -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-center py-5"
                     style="background:<?= $cor ?>;min-height:320px;border-radius:12px">
                    <span style="font-size:8rem"><?= $emoji ?></span>
                </div>
            </div>
            <?php if ($temPromocao): ?>
            <div class="alert alert-danger mt-3 mb-0 text-center py-2">
                <i class="bi bi-fire me-1"></i>
                <strong>Promoção! Economize <?= $desconto ?>% — <?= formatarMoeda($produto['preco'] - $precoExibir) ?></strong>
            </div>
            <?php endif; ?>
        </div>

        <!-- Informações -->
        <div class="col-md-7">
            <small class="text-muted text-uppercase fw-semibold">
                <?= htmlspecialchars($produto['categoria_nome'] ?? '') ?>
            </small>
            <h1 class="fw-bold mt-1 mb-3"><?= htmlspecialchars($produto['nome']) ?></h1>

            <!-- Preço -->
            <div class="mb-4">
                <?php if ($temPromocao): ?>
                <div class="text-muted text-decoration-line-through fs-5">
                    <?= formatarMoeda($produto['preco']) ?>
                </div>
                <?php endif; ?>
                <div class="display-6 fw-bold text-success">
                    <?= formatarMoeda($precoExibir) ?>
                </div>
                <div class="text-muted small mt-1">
                    <i class="bi bi-qr-code me-1"></i>
                    No Pix: <strong><?= formatarMoeda(aplicarDesconto($precoExibir, 5)) ?></strong> (5% off)
                    &nbsp;|&nbsp;
                    <i class="bi bi-credit-card me-1"></i>
                    12x de <strong><?= formatarMoeda($precoExibir / 12) ?></strong>
                </div>
            </div>

            <!-- Frete -->
            <div class="alert <?= $frete == 0 ? 'alert-success' : 'alert-light' ?> py-2 mb-4">
                <i class="bi bi-truck me-1"></i>
                <?php if ($frete == 0): ?>
                <strong>Frete Grátis</strong> para este produto!
                <?php else: ?>
                Frete estimado: <strong><?= formatarMoeda($frete) ?></strong>
                <small class="ms-1">(grátis acima de R$ 200)</small>
                <?php endif; ?>
            </div>

            <!-- Estoque -->
            <div class="mb-4">
                <?php if ($produto['estoque'] > 10): ?>
                <span class="badge bg-success fs-6">
                    <i class="bi bi-check-circle me-1"></i>Em estoque (<?= $produto['estoque'] ?> unidades)
                </span>
                <?php elseif ($produto['estoque'] > 0): ?>
                <span class="badge bg-warning text-dark fs-6">
                    <i class="bi bi-exclamation-triangle me-1"></i>Últimas <?= $produto['estoque'] ?> unidades!
                </span>
                <?php else: ?>
                <span class="badge bg-danger fs-6">
                    <i class="bi bi-x-circle me-1"></i>Produto indisponível
                </span>
                <?php endif; ?>
            </div>

            <!-- Botão Adicionar ao Carrinho -->
            <?php if ($produto['estoque'] > 0): ?>
            <div class="d-grid gap-2 d-md-flex mb-4">
                <a href="/loja/carrinho.php?acao=adicionar&id=<?= $produto['id'] ?>&nome=<?= urlencode($produto['nome']) ?>&preco=<?= $precoExibir ?>"
                   class="btn btn-warning btn-lg fw-bold px-5">
                    <i class="bi bi-cart-plus me-2"></i>Adicionar ao Carrinho
                </a>
                <a href="/loja/produtos.php" class="btn btn-outline-dark btn-lg">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>
            <?php else: ?>
            <div class="d-grid mb-4">
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="bi bi-x-circle me-2"></i>Produto Indisponível
                </button>
            </div>
            <?php endif; ?>

            <!-- Descrição -->
            <?php if (!empty($produto['descricao'])): ?>
            <h5 class="fw-bold mb-2">Descrição</h5>
            <p class="text-secondary"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- =====================================================
         PRODUTOS RELACIONADOS
         ===================================================== -->
    <?php if (!empty($produtosRelacionados)): ?>
    <hr class="my-5">
    <h4 class="fw-bold mb-4">
        <i class="bi bi-grid me-2 text-warning"></i>Produtos Relacionados
    </h4>
    <div class="row g-4">
        <?php foreach ($produtosRelacionados as $produto): ?>
        <div class="col-sm-6 col-lg-3">
            <?php include __DIR__ . '/includes/card_produto.php'; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
