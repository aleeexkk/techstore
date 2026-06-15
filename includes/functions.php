<?php
// =====================================================
// includes/functions.php
// Funções do projeto — Requisitos Tech Forge
// =====================================================

// =====================================================
// 1. ARMAZENAMENTO ESTRUTURADO COM ARRAYS
// Os dados são organizados em arrays associativos,
// nunca em variáveis soltas ($nome, $preco, $id...)
// =====================================================

/**
 * Busca todos os produtos do banco e retorna como array.
 * Cada item é um array associativo com todos os campos.
 */
function getProdutosArray($pdo) {
    $stmt = $pdo->query("
        SELECT p.*, c.nome AS categoria_nome
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.ativo = 1
        ORDER BY p.criado_em DESC
    ");
    return $stmt->fetchAll(); // Retorna array de arrays
}

/**
 * Busca categorias ativas e retorna como array.
 */
function getCategoriasArray($pdo) {
    $stmt = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome");
    return $stmt->fetchAll();
}

/**
 * Busca os itens do carrinho da sessão como array estruturado.
 */
function getCarrinhoArray() {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    return $_SESSION['carrinho'];
}

// =====================================================
// 2. MODULARIZAÇÃO COM FUNÇÕES DE PROCESSAMENTO
// Funções que recebem dados e executam uma lógica específica
// =====================================================

/**
 * Calcula o total de um array de itens do carrinho.
 * Cada item deve ter 'preco' e 'quantidade'.
 *
 * @param array $itens Array de itens do carrinho
 * @return float Total calculado
 */
function calcularTotal($itens) {
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
}

/**
 * Calcula o valor do frete com base no total da compra.
 *
 * @param float $total Valor total do carrinho
 * @return float Valor do frete
 */
function calcularFrete($total) {
    if ($total >= 200) {
        return 0.00;       // Frete grátis acima de R$ 200
    } elseif ($total >= 100) {
        return 15.00;
    } else {
        return 25.00;
    }
}

/**
 * Aplica um desconto percentual sobre um preço.
 *
 * @param float $preco      Preço original
 * @param float $porcentagem Percentual de desconto (0 a 100)
 * @return float Preço com desconto aplicado
 */
function aplicarDesconto($preco, $porcentagem) {
    if ($preco <= 0 || $porcentagem < 0 || $porcentagem > 100) {
        return $preco;
    }
    return $preco * (1 - $porcentagem / 100);
}

/**
 * Formata um valor float como moeda brasileira.
 *
 * @param float $valor Valor a formatar
 * @return string Valor formatado (ex: "R$ 1.299,90")
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// =====================================================
// 3. FLUXO DE DADOS (PARÂMETROS E RETORNO)
// Funções recebem via parâmetros e devolvem via return.
// Sem uso de variáveis globais.
// =====================================================

/**
 * Busca um produto específico dentro de um array de produtos.
 * Recebe o array por parâmetro — não acessa banco diretamente.
 *
 * @param array $produtos Array de produtos
 * @param int   $id       ID do produto a localizar
 * @return array|null     Produto encontrado ou null
 */
function buscarProdutoPorId($produtos, $id) {
    foreach ($produtos as $produto) {
        if ((int)$produto['id'] === (int)$id) {
            return $produto;
        }
    }
    return null;
}

/**
 * Conta o total de itens no carrinho.
 *
 * @param array $carrinho Array do carrinho
 * @return int Total de itens (somando quantidades)
 */
function contarItensCarrinho($carrinho) {
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['quantidade'];
    }
    return $total;
}

// =====================================================
// 4. LÓGICA DE PESQUISA OU FILTRO
// Busca e filtragem dentro de arrays
// =====================================================

/**
 * Filtra um array de produtos conforme critérios recebidos.
 * Suporta filtro por texto, categoria e faixa de preço.
 *
 * @param array $produtos Array com todos os produtos
 * @param array $filtros  Array com critérios: ['busca', 'categoria_id', 'preco_max', 'preco_min']
 * @return array          Array filtrado
 */
function filtrarProdutos($produtos, $filtros) {
    $resultado = [];

    foreach ($produtos as $produto) {
        $incluir = true;

        // Filtro por texto (nome ou descrição)
        if (!empty($filtros['busca'])) {
            $busca = strtolower(trim($filtros['busca']));
            $nome  = strtolower($produto['nome']);
            $desc  = strtolower($produto['descricao'] ?? '');

            if (strpos($nome, $busca) === false && strpos($desc, $busca) === false) {
                $incluir = false;
            }
        }

        // Filtro por categoria
        if (!empty($filtros['categoria_id']) && $filtros['categoria_id'] !== 'todas') {
            if ((int)$produto['categoria_id'] !== (int)$filtros['categoria_id']) {
                $incluir = false;
            }
        }

        // Filtro por preço máximo (ex: mostrar apenas produtos com preço < 500)
        if (!empty($filtros['preco_max']) && $filtros['preco_max'] > 0) {
            if ((float)$produto['preco'] > (float)$filtros['preco_max']) {
                $incluir = false;
            }
        }

        // Filtro por preço mínimo
        if (!empty($filtros['preco_min']) && $filtros['preco_min'] > 0) {
            if ((float)$produto['preco'] < (float)$filtros['preco_min']) {
                $incluir = false;
            }
        }

        if ($incluir) {
            $resultado[] = $produto;
        }
    }

    return $resultado;
}

/**
 * Retorna apenas os produtos em destaque do array.
 *
 * @param array $produtos Array com todos os produtos
 * @param int   $limite   Máximo de produtos a retornar
 * @return array          Produtos em destaque
 */
function getProdutosDestaque($produtos, $limite = 4) {
    $destaque = [];
    foreach ($produtos as $produto) {
        if ($produto['destaque'] == 1) {
            $destaque[] = $produto;
            if (count($destaque) >= $limite) {
                break;
            }
        }
    }
    return $destaque;
}

// =====================================================
// 5. VALIDAÇÃO DE REGRAS DE NEGÓCIO COM CONDICIONAIS
// Usa if/else para validar dados antes de processá-los
// =====================================================

/**
 * Valida o array do carrinho antes de processar o pedido.
 * Verifica: array vazio, preços negativos, quantidades inválidas.
 *
 * @param array $carrinho Array do carrinho de compras
 * @return array ['valido' => bool, 'mensagem' => string]
 */
function validarCarrinho($carrinho) {
    // Verifica se o array está vazio
    if (empty($carrinho)) {
        return [
            'valido'    => false,
            'mensagem'  => 'O carrinho está vazio. Adicione produtos antes de finalizar.'
        ];
    }

    foreach ($carrinho as $item) {
        // Verifica preços negativos ou zerados
        if (!isset($item['preco']) || $item['preco'] <= 0) {
            return [
                'valido'   => false,
                'mensagem' => 'Produto "' . htmlspecialchars($item['nome']) . '" tem preço inválido.'
            ];
        }

        // Verifica quantidade inválida
        if (!isset($item['quantidade']) || $item['quantidade'] <= 0) {
            return [
                'valido'   => false,
                'mensagem' => 'Quantidade inválida para "' . htmlspecialchars($item['nome']) . '".'
            ];
        }
    }

    return ['valido' => true, 'mensagem' => 'Carrinho válido.'];
}

/**
 * Valida os dados de um produto antes de salvar.
 *
 * @param array $dados Dados do produto
 * @return array Lista de erros encontrados (vazia = sem erros)
 */
function validarProduto($dados) {
    $erros = [];

    if (empty($dados['nome'])) {
        $erros[] = 'O nome do produto é obrigatório.';
    }

    if (!isset($dados['preco']) || (float)$dados['preco'] < 0) {
        $erros[] = 'O preço não pode ser negativo.';
    }

    if (!isset($dados['estoque']) || (int)$dados['estoque'] < 0) {
        $erros[] = 'O estoque não pode ser negativo.';
    }

    if (empty($dados['categoria_id'])) {
        $erros[] = 'Selecione uma categoria.';
    }

    return $erros;
}

// =====================================================
// FUNÇÕES AUXILIARES DO CARRINHO
// =====================================================

/**
 * Adiciona um produto ao carrinho (sessão).
 */
function adicionarAoCarrinho($produto_id, $nome, $preco, $quantidade = 1) {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = [
            'id'         => $produto_id,
            'nome'       => $nome,
            'preco'      => $preco,
            'quantidade' => $quantidade,
        ];
    }
}

/**
 * Remove um produto do carrinho.
 */
function removerDoCarrinho($produto_id) {
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
}
