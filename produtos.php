<?php
// produtos.php — Listagem de produtos com filtro e busca
$tituloPagina = 'Produtos';
require_once __DIR__ . '/includes/header.php';

// Busca todos os produtos como array
$todosProdutos = getProdutosArray($pdo);

// Monta o array de filtros a partir do GET
$filtros = [
    'busca'        => trim($_GET['busca'] ?? ''),
    'categoria_id' => $_GET['categoria_id'] ?? 'todas',
    'preco_min'    => (float)($_GET['preco_min'] ?? 0),
    'preco_max'    => (float)($_GET['preco_max'] ?? 0),
];

// Aplica os filtros usando a função (Tech Forge: filtrarProdutos)
$produtosFiltrados = filtrarProdutos($todosProdutos, $filtros);

// Verifica se tem filtro ativo
$temFiltro = !empty($filtros['busca'])
    || ($filtros['categoria_id'] !== 'todas')
    || $filtros['preco_min'] > 0
    || $filtros['preco_max'] > 0;

// Busca a categoria selecionada para exibição
$categoriaAtiva = null;
if ($filtros['categoria_id'] !== 'todas') {
    foreach ($categorias as $cat) {
        if ($cat['id'] == $filtros['categoria_id']) {
            $categoriaAtiva = $cat;
            break;
        }
    }
}
?>

<div class="container my-4">
    <!-- Título da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">
                <?php if ($categoriaAtiva): ?>
                <i class="bi bi-tag me-2 text-warning"></i><?= htmlspecialchars($categoriaAtiva['nome']) ?>
                <?php elseif (!empty($filtros['busca'])): ?>
                <i class="bi bi-search me-2 text-warning"></i>Resultados para "<?= htmlspecialchars($filtros['busca']) ?>"
                <?php else: ?>
                <i class="bi bi-bag me-2 text-warning"></i>Todos os Produtos
                <?php endif; ?>
            </h2>
            <small class="text-muted">
                <?= count($produtosFiltrados) ?> produto(s) encontrado(s)
                <?php if (count($produtosFiltrados) !== count($todosProdutos)): ?>
                de <?= count($todosProdutos) ?> no total
                <?php endif; ?>
            </small>
        </div>
        <?php if ($temFiltro): ?>
        <a href="/loja/produtos.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-circle me-1"></i>Limpar filtros
        </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- =====================================================
             SIDEBAR DE FILTROS — Bootstrap Accordion (Componente #5)
             ===================================================== -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-top" style="top:80px">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </div>
                <div class="card-body">
                    <form method="GET" action="/loja/produtos.php">

                        <!-- Busca por texto -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Buscar</label>
                            <input type="text" name="busca" class="form-control form-control-sm"
                                   placeholder="Nome do produto..." value="<?= htmlspecialchars($filtros['busca']) ?>">
                        </div>

                        <!-- Filtro por categoria (usando foreach + if) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Categoria</label>
                            <?php foreach ($categorias as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="categoria_id"
                                       value="<?= $cat['id'] ?>"
                                       id="cat_<?= $cat['id'] ?>"
                                       <?= ($filtros['categoria_id'] == $cat['id']) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="cat_<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="radio" name="categoria_id"
                                       value="todas" id="cat_todas"
                                       <?= ($filtros['categoria_id'] === 'todas') ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-bold" for="cat_todas">Todas</label>
                            </div>
                        </div>

                        <!-- Filtro por preço -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Faixa de Preço</label>
                            <div class="row g-1">
                                <div class="col">
                                    <input type="number" name="preco_min" class="form-control form-control-sm"
                                           placeholder="Mín" min="0" step="10"
                                           value="<?= $filtros['preco_min'] ?: '' ?>">
                                </div>
                                <div class="col">
                                    <input type="number" name="preco_max" class="form-control form-control-sm"
                                           placeholder="Máx" min="0" step="10"
                                           value="<?= $filtros['preco_max'] ?: '' ?>">
                                </div>
                            </div>
                            <!-- Filtros rápidos de preço -->
                            <div class="mt-2">
                                <a href="/loja/produtos.php?preco_max=200" class="badge bg-light text-dark border me-1 text-decoration-none">até R$200</a>
                                <a href="/loja/produtos.php?preco_max=500" class="badge bg-light text-dark border me-1 text-decoration-none">até R$500</a>
                                <a href="/loja/produtos.php?preco_max=1000" class="badge bg-light text-dark border text-decoration-none">até R$1000</a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 btn-sm fw-semibold">
                            <i class="bi bi-search me-1"></i>Aplicar Filtros
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- =====================================================
             GRADE DE PRODUTOS
             ===================================================== -->
        <div class="col-lg-9">
            <?php if (!empty($produtosFiltrados)): ?>
            <div class="row g-4">
                <?php foreach ($produtosFiltrados as $produto): ?>
                <div class="col-sm-6 col-xl-4">
                    <?php include __DIR__ . '/includes/card_produto.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Nenhum produto encontrado -->
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhum produto encontrado</h4>
                <p class="text-secondary">
                    <?php if ($temFiltro): ?>
                    Tente ajustar os filtros ou <a href="/loja/produtos.php">ver todos os produtos</a>.
                    <?php else: ?>
                    Não há produtos cadastrados ainda.
                    <?php endif; ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
