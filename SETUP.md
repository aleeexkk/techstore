# GUIA DE CONFIGURAÇÃO — TechStore
# Requisitos: XAMPP + Apache + MySQL

## ─────────────────────────────────────────────
## 1. IMPORTAR O BANCO DE DADOS
## ─────────────────────────────────────────────

1. Abra o XAMPP Control Panel e inicie: Apache + MySQL
2. Acesse: http://localhost/phpmyadmin
3. Clique em "Importar" (aba superior)
4. Selecione o arquivo: `loja_db.sql`
5. Clique em "Executar"

✅ O banco loja_db será criado com todas as 7 tabelas e dados de exemplo.


## ─────────────────────────────────────────────
## 2. INSTALAR O PROJETO
## ─────────────────────────────────────────────

1. Copie a pasta `/loja` para: `C:\xampp\htdocs\loja`
2. Acesse: http://localhost/loja


## ─────────────────────────────────────────────
## 3. CONFIGURAR A PORTA 8080 NO APACHE
## ─────────────────────────────────────────────

Arquivo: C:\xampp\apache\conf\httpd.conf

Localize a linha:
    Listen 80
Altere para:
    Listen 8080

Localize também:
    ServerName localhost:80
Altere para:
    ServerName localhost:8080

Salve o arquivo e reinicie o Apache no XAMPP.

Acesse agora em: http://localhost:8080/loja


## ─────────────────────────────────────────────
## 4. CONFIGURAR DNS LOCAL (arquivo hosts)
## ─────────────────────────────────────────────

Abra o Bloco de Notas como ADMINISTRADOR e abra o arquivo:
    C:\Windows\System32\drivers\etc\hosts

Adicione ao final:
    127.0.0.1   techstore.local

Salve o arquivo.

Agora adicione o domínio no Apache:
Arquivo: C:\xampp\apache\conf\extra\httpd-vhosts.conf

Adicione ao final:
    <VirtualHost *:8080>
        DocumentRoot "C:/xampp/htdocs/loja"
        ServerName techstore.local
        <Directory "C:/xampp/htdocs/loja">
            Options -Indexes
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>

Reinicie o Apache e acesse: http://techstore.local:8080


## ─────────────────────────────────────────────
## 5. IP FIXO PARA O MYSQL
## ─────────────────────────────────────────────

O projeto já usa 127.0.0.1 (IP fixo) em vez de 'localhost'
no arquivo config/database.php:
    define('DB_HOST', '127.0.0.1');

Isso força a conexão via TCP/IP com IP fixo, não via socket Unix.

Para garantir que o MySQL sempre responda nesse IP:
Arquivo: C:\xampp\mysql\bin\my.ini

Localize/adicione em [mysqld]:
    bind-address = 127.0.0.1

Reinicie o MySQL no XAMPP.


## ─────────────────────────────────────────────
## 6. MÁQUINAS SEPARADAS (aplicação e banco)
## ─────────────────────────────────────────────

OPÇÃO A — Com dois computadores na mesma rede:
1. No PC com o MySQL (banco): anote o IP (ex: 192.168.1.100)
2. Libere a porta 3306 no firewall do PC banco
3. No MySQL, crie um usuário com acesso remoto:
       CREATE USER 'loja_user'@'%' IDENTIFIED BY 'senha123';
       GRANT ALL PRIVILEGES ON loja_db.* TO 'loja_user'@'%';
       FLUSH PRIVILEGES;
4. No PC com o Apache (aplicação), edite config/database.php:
       define('DB_HOST', '192.168.1.100');  // IP do PC banco
       define('DB_USER', 'loja_user');
       define('DB_PASS', 'senha123');

OPÇÃO B — Docker (dois containers = duas "máquinas"):
Instale o Docker Desktop, crie um arquivo docker-compose.yml:

    version: '3.8'
    services:
      app:
        image: php:8.2-apache
        ports:
          - "8080:80"
        volumes:
          - ./loja:/var/www/html
        depends_on:
          - db

      db:
        image: mysql:8.0
        environment:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: loja_db
        ports:
          - "3306:3306"
        networks:
          - loja_net

    networks:
      loja_net:

Execute com: docker-compose up -d
No config/database.php, use: define('DB_HOST', 'db');


## ─────────────────────────────────────────────
## 7. VERIFICAR OS REQUISITOS DA RUBRICA
## ─────────────────────────────────────────────

✅ DER (Entidade-Relacionamento): ver loja_db.sql — 7 tabelas
✅ Mínimo 3 tabelas: categorias, produtos, usuarios, pedidos, itens_pedido, tags, produto_tags (7 tabelas)
✅ Chaves primárias: todas as tabelas têm id AUTO_INCREMENT PRIMARY KEY
✅ Chave estrangeira: produtos.categoria_id, pedidos.usuario_id, itens_pedido.pedido_id/produto_id
✅ Relacionamento N:N: produtos ↔ tags (via produto_tags) | pedidos ↔ produtos (via itens_pedido)
✅ Proibir listagem: .htaccess com Options -Indexes
✅ Porta 8080: httpd.conf (Listen 8080)
✅ DNS local: arquivo hosts + VirtualHost
✅ IP fixo no banco: define('DB_HOST', '127.0.0.1')
✅ Máquinas separadas: ver Opção A ou B acima
✅ Layout PHP dinâmico: index.php, produtos.php, produto.php, carrinho.php
✅ Template PHP: includes/header.php + footer.php (require_once)
✅ Bootstrap 3+ componentes: Navbar, Carousel, Cards, Modal, Accordion, Breadcrumb, Table, Badge
✅ Conexão com banco: config/database.php com PDO
✅ Dados do banco na tela: produtos carregados do MySQL via getProdutosArray()
✅ IF, WHILE, FOREACH: usados em todos os arquivos
✅ Arrays: getProdutosArray(), getCarrinhoArray(), filtrarProdutos()
✅ Funções de processamento: calcularTotal(), aplicarDesconto(), calcularFrete()
✅ Parâmetros e return: todas as funções em functions.php
✅ Busca/Filtro: filtrarProdutos() com múltiplos critérios
✅ Validação: validarCarrinho(), validarProduto()
