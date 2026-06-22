<?php
// =====================================================
// config/database.php
// Configuração da conexão com o banco de dados
// =====================================================

define('DB_HOST', '192.168.0.158');
define('DB_PORT', '3306');
define('DB_NAME', 'loja_db');
define('DB_USER', 'loja_user');
define('DB_PASS', 'loja123');           

/**
 * Retorna uma conexão PDO com o banco de dados.
 * Uso de IP (127.0.0.1) ao invés de 'localhost' garante
 * a comunicação via TCP/IP (requisito de IP fixo).
 */
function getConexao() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST
                 . ";port=" . DB_PORT
                 . ";dbname=" . DB_NAME
                 . ";charset=utf8mb4";

            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die("<div style='background:#fee;color:#c00;padding:20px;font-family:sans-serif'>
                <strong>Erro de Conexão:</strong> " . htmlspecialchars($e->getMessage()) . "
                <br><small>Verifique se o MySQL está rodando no XAMPP.</small>
                </div>");
        }
    }

    return $pdo;
}
