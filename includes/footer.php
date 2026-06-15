<?php // includes/footer.php — Template de rodapé reutilizável ?>

<footer class="bg-dark text-light mt-5 pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <!-- Sobre -->
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-warning mb-3">
                    <i class="bi bi-cpu-fill me-1"></i>TechStore
                </h5>
                <p class="text-secondary small">
                    Sua loja de tecnologia com os melhores preços e produtos selecionados.
                    Qualidade garantida e entrega para todo o Brasil.
                </p>
            </div>

            <!-- Categorias -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-warning mb-3">Categorias</h6>
                <ul class="list-unstyled small">
                    <li><a href="/loja/produtos.php?categoria_id=1" class="text-secondary text-decoration-none">Eletrônicos</a></li>
                    <li><a href="/loja/produtos.php?categoria_id=2" class="text-secondary text-decoration-none">Informática</a></li>
                    <li><a href="/loja/produtos.php?categoria_id=3" class="text-secondary text-decoration-none">Games</a></li>
                    <li><a href="/loja/produtos.php?categoria_id=4" class="text-secondary text-decoration-none">Áudio</a></li>
                    <li><a href="/loja/produtos.php?categoria_id=5" class="text-secondary text-decoration-none">Fotografia</a></li>
                </ul>
            </div>

            <!-- Links úteis -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-warning mb-3">Links Úteis</h6>
                <ul class="list-unstyled small">
                    <li><a href="/loja/index.php" class="text-secondary text-decoration-none">Home</a></li>
                    <li><a href="/loja/produtos.php" class="text-secondary text-decoration-none">Produtos</a></li>
                    <li><a href="/loja/carrinho.php" class="text-secondary text-decoration-none">Carrinho</a></li>
                </ul>
            </div>

            <!-- Contato -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-warning mb-3">Contato</h6>
                <ul class="list-unstyled small text-secondary">
                    <li><i class="bi bi-envelope me-1"></i> contato@techstore.com</li>
                    <li><i class="bi bi-whatsapp me-1"></i> (44) 99999-0000</li>
                    <li><i class="bi bi-geo-alt me-1"></i> Campo Mourão - PR</li>
                    <li class="mt-2">
                        <span class="badge bg-success">
                            <i class="bi bi-shield-check me-1"></i>Compra Segura
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap small text-secondary">
            <span>&copy; <?= date('Y') ?> TechStore. Todos os direitos reservados.</span>
            <span>
                <i class="bi bi-credit-card me-1"></i>Pix
                <i class="bi bi-credit-card-2-back ms-2 me-1"></i>Cartão
                <i class="bi bi-bank ms-2 me-1"></i>Boleto
            </span>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS (necessário para dropdown, modal, collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts após 4 segundos
document.querySelectorAll('.alert').forEach(function(alert) {
    setTimeout(function() {
        var a = new bootstrap.Alert(alert);
        a.close();
    }, 4000);
});
</script>
</body>
</html>
