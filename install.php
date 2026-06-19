<?php
$step = $_GET['step'] ?? 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Instalação do Sistema PHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-lg">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Instalador do Sistema</h1>
        
        <?php if ($step == 1): ?>
            <p class="text-slate-600 mb-4">Bem-vindo(a)! Este script irá preparar o ambiente configurando as tabelas e usuários iniciais.</p>
            <p class="text-slate-600 mb-4 text-sm bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                1. Certifique-se de ter configurado o arquivo <strong>config.php</strong> com os dados de acesso ao seu banco MySQL.<br>
                2. Para importar os dados exportados do front-end, veja se o <strong>database.sql</strong> está na mesma pasta.
            </p>
            <a href="?step=2" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">Iniciar Instalação</a>
        <?php elseif ($step == 2): ?>
            <div class="space-y-2 text-sm bg-slate-50 p-4 rounded-lg border border-slate-200 h-64 overflow-y-auto font-mono">
                <?php
                try {
                    if (!file_exists('config.php')) {
                        throw new Exception("Arquivo config.php não encontrado.");
                    }
                    require 'config.php';
                    echo "<div class='text-green-600'>✓ Conexão com o banco de dados estabelecida.</div>";

                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0755, true);
                        echo "<div class='text-green-600'>✓ Pasta 'uploads' criada com sucesso.</div>";
                    } else {
                        echo "<div class='text-slate-500'>- Pasta 'uploads' já existe.</div>";
                    }

                    if (file_exists('database.sql')) {
                        $sql = file_get_contents('database.sql');
                        $pdo->exec($sql);
                        echo "<div class='text-green-600'>✓ Arquivo database.sql importado. Tabelas base criadas (incluindo usuários atuais).</div>";
                    } else {
                        echo "<div class='text-yellow-600 mb-2'>Aviso: database.sql não encontrado.</div>";
                        // Cria as tabelas básicas manualmente
                        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                            id VARCHAR(50) PRIMARY KEY,
                            name VARCHAR(255),
                            email VARCHAR(255),
                            role VARCHAR(50)
                        )");
                        echo "<div class='text-green-600'>✓ Tabela 'users' criada vazia.</div>";
                    }

                    // Download FPDF se não existir
                    if (!file_exists('fpdf.php')) {
                        echo "<div class='text-slate-500'>- Baixando biblioteca FPDF para relatórios em PDF...</div>";
                        $fpdfContent = @file_get_contents('https://raw.githubusercontent.com/Setasign/FPDF/master/fpdf.php');
                        if ($fpdfContent) {
                            file_put_contents('fpdf.php', $fpdfContent);
                            echo "<div class='text-green-600'>✓ FPDF baixado com sucesso.</div>";
                        } else {
                            echo "<div class='text-yellow-600'>Aviso: Não foi possível baixar FPDF. Relatórios PDF podem não funcionar.</div>";
                        }
                    } else {
                        echo "<div class='text-slate-500'>- Biblioteca FPDF já presente.</div>";
                    }

                    // Checa usuário admin
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin'");
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $pdo->exec("INSERT INTO users (id, name, email, role) VALUES ('u_" . md5(uniqid()) . "', 'Administrador Padrão', 'admin', 'ADMIN')");
                        echo "<div class='text-green-600 mt-2'>✓ Usuário padrão criado!<br><strong>Login:</strong> admin<br><strong>Nível:</strong> ADMIN</div>";
                    } else {
                        echo "<div class='text-slate-500'>- Usuário admin já existente no banco.</div>";
                    }

                    echo "<div class='text-blue-600 font-bold mt-4'>Instalação finalizada com sucesso!</div>";
                    $success = true;
                } catch(Exception $e) {
                    echo "<div class='text-red-600 mt-2 font-medium'>Erro: " . $e->getMessage() . "</div>";
                    $success = false;
                }
                ?>
            </div>
            
            <?php if (isset($success) && $success): ?>
                <div class="mt-6 space-y-3">
                    <p class="text-sm text-amber-600 font-medium text-center bg-amber-50 p-2 rounded">
                        ⚠️ Por segurança, exclua ou renomeie este arquivo <strong>install.php</strong> do seu servidor após a instalação.
                    </p>
                    <a href="index.php" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition">Acessar Sistema</a>
                </div>
            <?php else: ?>
                <div class="mt-6 border-t pt-4">
                    <a href="?step=2" class="block w-full text-center bg-slate-600 hover:bg-slate-700 text-white font-semibold py-2 rounded-lg transition">Tentar Novamente</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
