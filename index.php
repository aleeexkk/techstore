<?php
$tituloPagina = 'Home';
require_once __DIR__ . '/includes/header.php';

$todosProdutos    = getProdutosArray($pdo);
$produtosDestaque = getProdutosDestaque($todosProdutos, 4);

$heroSlides = [
  ['bg'=>'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1400&h=500&fit=crop&q=80',
   'title'=>'Tecnologia de ponta', 'sub'=>'Os melhores produtos com os menores preços',
   'btn'=>'Ver ofertas', 'link'=>'/loja/produtos.php'],
  ['bg'=>'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=1400&h=500&fit=crop&q=80',
   'title'=>'Setup Gamer Completo', 'sub'=>'Teclados, mouses, headsets e controles top',
   'btn'=>'Ver Games', 'link'=>'/loja/produtos.php?categoria_id=3'],
  ['bg'=>'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1400&h=500&fit=crop&q=80',
   'title'=>'Smartphones & Notebooks', 'sub'=>'Lançamentos com entrega rápida',
   'btn'=>'Ver Eletrônicos', 'link'=>'/loja/produtos.php?categoria_id=1'],
];
?>

<!-- CAROUSEL -->
<div id="carouselHero" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
  <div class="carousel-indicators">
    <?php foreach ($heroSlides as $i => $_): ?>
    <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="<?= $i ?>"
            <?= $i===0?'class="active"':'' ?>></button>
    <?php endforeach; ?>
  </div>
  <div class="carousel-inner">
    <?php foreach ($heroSlides as $i => $slide): ?>
    <div class="carousel-item <?= $i===0?'active':'' ?>">
      <div style="height:460px;background:url('<?= $slide['bg'] ?>') center/cover no-repeat;position:relative">
        <div class="hero-overlay"></div>
        <div class="hero-content d-flex flex-column align-items-center justify-content-center h-100">
          <h1 class="text-white fw-800 mb-2"><?= $slide['title'] ?></h1>
          <p class="text-white-50 fs-5 mb-4"><?= $slide['sub'] ?></p>
          <a href="<?= $slide['link'] ?>" class="btn btn-warning btn-lg fw-bold px-5 rounded-pill shadow">
            <?= $slide['btn'] ?> <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- CATEGORIAS -->
<div class="container my-5">
  <h2 class="section-title mb-4">
    <i class="bi bi-grid-3x3-gap text-warning me-2"></i>Categorias
  </h2>
  <div class="row g-3">
    <?php
    $catConfig = [
      '1'=>['emoji'=>'📱','cor'=>'#dbeafe','label'=>'Eletrônicos'],
      '2'=>['emoji'=>'💻','cor'=>'#ede9fe','label'=>'Informática'],
      '3'=>['emoji'=>'🎮','cor'=>'#dcfce7','label'=>'Games'],
      '4'=>['emoji'=>'🔊','cor'=>'#fef3c7','label'=>'Áudio'],
      '5'=>['emoji'=>'📷','cor'=>'#fce7f3','label'=>'Fotografia'],
    ];
    foreach ($categorias as $cat):
      $cfg = $catConfig[$cat['id']] ?? ['emoji'=>'🛒','cor'=>'#f5f5f5','label'=>$cat['nome']];
    ?>
    <div class="col-6 col-md-4 col-lg-2">
      <a href="/loja/produtos.php?categoria_id=<?= $cat['id'] ?>" class="text-decoration-none">
        <div class="card text-center border-0 shadow-sm categoria-card h-100">
          <div class="card-body py-4">
            <div class="emoji-icon mb-2" style="font-size:2.4rem"><?= $cfg['emoji'] ?></div>
            <p class="card-text small fw-semibold text-dark mb-0">
              <?= htmlspecialchars($cat['nome']) ?>
            </p>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- BENEFÍCIOS -->
<div class="banner-beneficios my-2">
  <div class="container">
    <div class="row g-4 text-center">
      <?php foreach ([
        ['bi-truck','Frete Grátis','Compras acima de R$ 200'],
        ['bi-shield-check','Compra Segura','Dados 100% protegidos'],
        ['bi-arrow-repeat','30 dias','Troca sem custo'],
        ['bi-credit-card','12x sem juros','No cartão de crédito'],
      ] as [$icon,$title,$sub]): ?>
      <div class="col-6 col-md-3 beneficio-item">
        <i class="bi <?= $icon ?> icon"></i>
        <h6><?= $title ?></h6>
        <small><?= $sub ?></small>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- DESTAQUES -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">
      <i class="bi bi-star-fill text-warning me-2"></i>Destaques
    </h2>
    <a href="/loja/produtos.php" class="btn btn-outline-dark btn-sm rounded-pill px-4">
      Ver todos <i class="bi bi-arrow-right ms-1"></i>
    </a>
  </div>
  <div class="row g-4">
    <?php foreach ($produtosDestaque as $produto): ?>
    <div class="col-sm-6 col-lg-3">
      <?php include __DIR__ . '/includes/card_produto.php'; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- TODOS OS PRODUTOS -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">
      <i class="bi bi-bag text-warning me-2"></i>Todos os Produtos
    </h2>
    <span class="badge bg-dark fs-6 rounded-pill"><?= count($todosProdutos) ?> produtos</span>
  </div>
  <div class="row g-4">
    <?php foreach ($todosProdutos as $produto): ?>
    <div class="col-sm-6 col-lg-3">
      <?php include __DIR__ . '/includes/card_produto.php'; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
