<?php
// includes/header.php — Template de cabeçalho
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$carrinho    = getCarrinhoArray();
$totalItens  = contarItensCarrinho($carrinho);
$pdo         = getConexao();
$categorias  = getCategoriasArray($pdo);
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($tituloPagina ?? 'TechStore') ?> — TechStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="/loja/assets/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top shadow">
  <div class="container">
    <a class="navbar-brand text-warning fw-bold" href="/loja/index.php">
      <i class="bi bi-cpu-fill me-1"></i>TechStore
    </a>
    <button class="navbar-toggler border-secondary" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <form class="d-flex mx-auto" action="/loja/produtos.php" method="GET" style="width:360px">
        <input class="form-control me-2" type="search" name="busca"
               placeholder="Buscar produtos..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
        <button class="btn btn-warning px-3" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </form>
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item">
          <a class="nav-link text-white-50 <?= $paginaAtual === 'index.php' ? 'text-white fw-semibold' : '' ?>"
             href="/loja/index.php">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link text-white-50 dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Categorias
          </a>
          <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
            <li><a class="dropdown-item" href="/loja/produtos.php">
              <i class="bi bi-collection me-2 text-warning"></i>Todos os produtos
            </a></li>
            <li><hr class="dropdown-divider border-secondary"></li>
            <?php foreach ($categorias as $cat): ?>
            <li><a class="dropdown-item" href="/loja/produtos.php?categoria_id=<?= $cat['id'] ?>">
              <?= htmlspecialchars($cat['nome']) ?>
            </a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-warning position-relative ms-2" href="/loja/carrinho.php">
            <i class="bi bi-cart3 fs-5"></i>
            <?php if ($totalItens > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= $totalItens ?>
            </span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div style="margin-top:66px"></div>

<?php if (!empty($_SESSION['mensagem'])): ?>
<div class="container mt-2">
  <div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?? 'info' ?> alert-dismissible fade show rounded-3 shadow-sm">
    <?= htmlspecialchars($_SESSION['mensagem']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); ?>
<?php endif; ?>
