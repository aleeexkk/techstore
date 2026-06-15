<?php
// carrinho.php — Página do carrinho de compras
// Demonstra: arrays, funções, parâmetros/return, validação
$tituloPagina = 'Meu Carrinho';
require_once __DIR__ . '/includes/header.php';

// =====================================================
// Processar ações do carrinho
// =====================================================
$acao = $_GET['acao'] ?? $_POST['acao'] ?? '';

if ($acao === 'adicionar') {
    // Validação de regra de negócio: verifica se os dados são consistentes
    $id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $nome  = htmlspecialchars(trim($_GET['nome'] ?? ''));
    $preco = filter_input(INPUT_GET, 'preco', FILTER_VALIDATE_FLOAT);

    // if/else para validar antes de processar (Tech Forge: Validação)
    if (!$id || empty($nome) || !$preco || $preco <= 0) {
        $_SESSION['mensagem']      = 'Dados do produto inválidos.';
        $_SESSION['tipo_mensagem'] = 'danger';
    } else {
        adicionarAoCarrinho($id, $nome, $preco, 1);
        $_SESSION['mensagem']      = "\"$nome\" adicionado ao carrinho!";
        $_SESSION['tipo_mensagem'] = 'success';
    }
    header('Location: /loja/carrinho.php');
    exit;
}

if ($acao === 'remover') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        removerDoCarrinho($id);
        $_SESSION['mensagem']      = 'Produto removido do carrinho.';
        $_SESSION['tipo_mensagem'] = 'info';
    }
    header('Location: /loja/carrinho.php');
    exit;
}

if ($acao === 'atualizar') {
    $quantidades = $_POST['quantidades'] ?? [];
    foreach ($quantidades as $id => $qtd) {
        $id  = (int)$id;
        $qtd = (int)$qtd;
        // Validação: quantidade deve ser positiva
        if ($qtd <= 0) {
            removerDoCarrinho($id);
        } elseif (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] = $qtd;
        }
    }
    $_SESSION['mensagem']      = 'Carrinho atualizado!';
    $_SESSION['tipo_mensagem'] = 'success';
    header('Location: /loja/carrinho.php');
    exit;
}

if ($acao === 'limpar') {
    $_SESSION['carrinho']      = [];
    $_SESSION['mensagem']      = 'Carrinho esvaziado.';
    $_SESSION['tipo_mensagem'] = 'info';
    header('Location: /loja/carrinho.php');
    exit;
}

// =====================================================
// Busca o carrinho (array estruturado) e valida
// =====================================================
$carrinho  = getCarrinhoArray();
$validacao = validarCarrinho($carrinho);  // Tech Forge: valida o array

// Calcula totais usando funções com parâmetros e return
$subtotal = calcularTotal($carrinho);     // Tech Forge: função com return
$frete    = calcularFrete($subtotal);     // Tech Forge: função com return
$total    = $subtotal + $frete;
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-cart3 me-2 text-warning"></i>Meu Carrinho
            <?php if (!empty($carrinho)): ?>
            <span class="badge bg-secondary fs-6 ms-1"><?= contarItensCarrinho($carrinho) ?> itens</span>
            <?php endif; ?>
        </h2>
        <a href="/loja/produtos.php" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Continuar Comprando
        </a>
    </div>

    <?php if (empty($carrinho)): ?>
    <!-- =====================================================
         CARRINHO VAZIO
         ===================================================== -->
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h4 class="mt-3">Seu carrinho está vazio</h4>
        <p class="text-secondary">Adicione produtos para começar suas compras!</p>
        <a href="/loja/produtos.php" class="btn btn-warning btn-lg mt-2">
            <i class="bi bi-bag me-2"></i>Ver Produtos
        </a>
    </div>

    <?php else: ?>
    <!-- =====================================================
         CARRINHO COM ITENS
         ===================================================== -->
    <form method="POST" action="/loja/carrinho.php">
        <input type="hidden" name="acao" value="atualizar">
        <div class="row g-4">

            <!-- Tabela de itens -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <!-- Bootstrap Componente: Table responsive -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4">Produto</th>
                                        <th class="text-center">Qtd.</th>
                                        <th class="text-end">Preço</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrinho as $item): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-semibold"><?= htmlspecialchars($item['nome']) ?></span>
                                        </td>
                                        <td class="text-center" style="width:120px">
                                            <input type="number"
                                                   name="quantidades[<?= $item['id'] ?>]"
                                                   value="<?= $item['quantidade'] ?>"
                                                   min="0" max="99"
                                                   class="form-control form-control-sm text-center mx-auto"
                                                   style="width:70px">
                                        </td>
                                        <td class="text-end"><?= formatarMoeda($item['preco']) ?></td>
                                        <td class="text-end fw-bold">
                                            <?= formatarMoeda($item['preco'] * $item['quantidade']) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="/loja/carrinho.php?acao=remover&id=<?= $item['id'] ?>"
                                               class="btn btn-outline-danger btn-sm"
                                               onclick="return confirm('Remover este produto?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="/loja/carrinho.php?acao=limpar"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('Esvaziar o carrinho?')">
                            <i class="bi bi-trash me-1"></i>Esvaziar
                        </a>
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i>Atualizar Quantidades
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumo do pedido -->
            <div class="col-lg-4">
                <!-- Bootstrap Componente: Card de resumo -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-receipt me-1"></i>Resumo do Pedido
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Subtotal</span>
                            <span><?= formatarMoeda($subtotal) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">
                                <i class="bi bi-truck me-1"></i>Frete
                            </span>
                            <?php if ($frete == 0): ?>
                            <span class="text-success fw-semibold">Grátis</span>
                            <?php else: ?>
                            <span><?= formatarMoeda($frete) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Progresso para frete grátis -->
                        <?php if ($frete > 0): ?>
                        <?php $faltaFrete = 200 - $subtotal; ?>
                        <div class="my-2">
                            <small class="text-muted">
                                Falta <?= formatarMoeda($faltaFrete) ?> para frete grátis!
                            </small>
                            <div class="progress mt-1" style="height:6px">
                                <div class="progress-bar bg-success"
                                     style="width:<?= min(100, ($subtotal/200)*100) ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <hr>
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <span>Total</span>
                            <span class="text-success"><?= formatarMoeda($total) ?></span>
                        </div>
                        <small class="text-muted d-block mt-1">
                            ou 12x de <?= formatarMoeda($total / 12) ?> no cartão
                        </small>

                        <!-- Botão finalizar -->
                        <?php if ($validacao['valido']): ?>
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-warning btn-lg fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#modalFinalizar">
                                <i class="bi bi-bag-check me-2"></i>Finalizar Compra
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-danger small mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <?= htmlspecialchars($validacao['mensagem']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info segurança -->
                <div class="card border-0 bg-light mt-3">
                    <div class="card-body py-2 small text-center text-muted">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i>
                        Compra 100% segura e protegida
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- =====================================================
         MODAL DE FINALIZAÇÃO — Bootstrap Componente #7
         ===================================================== -->
    <div class="modal fade" id="modalFinalizar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-bag-check me-1"></i>Finalizar Pedido
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success py-2 mb-3">
                        <i class="bi bi-check-circle me-1"></i>
                        <strong>Total: <?= formatarMoeda($total) ?></strong>
                        (<?= $frete == 0 ? 'Com frete grátis!' : 'Frete: ' . formatarMoeda($frete) ?>)
                    </div>
                    <p class="text-secondary small">
                        Este é um projeto acadêmico. O processamento real de pagamento não está implementado.
                    </p>
                    <!-- Resumo dos itens no modal (usa FOREACH) -->
                    <ul class="list-group list-group-flush">
                        <?php foreach ($carrinho as $item): ?>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="small">
                                <?= htmlspecialchars($item['nome']) ?>
                                <span class="text-muted">×<?= $item['quantidade'] ?></span>
                            </span>
                            <span class="small fw-bold">
                                <?= formatarMoeda($item['preco'] * $item['quantidade']) ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="/loja/carrinho.php?acao=limpar" class="btn btn-warning fw-bold">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Pedido
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
