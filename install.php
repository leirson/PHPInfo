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
                1. Informe os dados de acesso ao seu banco MySQL para gerar o <strong>config.php</strong>.<br>
                2. Os arquivos <strong>database.sql</strong> e (opcionalmente) <strong>seed.sql</strong> devem estar na mesma pasta para criar as tabelas e dados.
            </p>
            <form action="?step=2" method="POST" class="space-y-4">
                <input type="hidden" name="step" value="2">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Servidor (Host)</label>
                        <input type="text" name="db_host" value="localhost" placeholder="ex: localhost" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nome do Banco</label>
                        <input type="text" name="db_name" placeholder="ex: sistema_db" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Usuário do DB</label>
                        <input type="text" name="db_user" placeholder="ex: root" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Senha do DB</label>
                        <input type="password" name="db_pass" placeholder="Deixe em branco se não houver" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none">
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-200 mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Dados Iniciais</label>
                    <label class="flex items-start space-x-3 cursor-pointer p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        <input type="radio" name="seedType" value="empty" checked class="mt-1">
                        <div>
                            <span class="block font-medium text-slate-800">Banco de Dados Vazio</span>
                            <span class="block text-sm text-slate-500">Apenas estrutura e usuários padrão (Recomendado para produção)</span>
                        </div>
                    </label>
                    <label class="flex items-start space-x-3 cursor-pointer p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        <input type="radio" name="seedType" value="example" class="mt-1">
                        <div>
                            <span class="block font-medium text-slate-800">Banco de Dados com Exemplos</span>
                            <span class="block text-sm text-slate-500">Inclui clientes, OS, produtos e serviços (Recomendado para testes)</span>
                        </div>
                    </label>
                </div>
                <button type="submit" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition mt-4">Conectar e Instalar</button>
            </form>
        <?php elseif ($step == 2): ?>
            <div class="space-y-2 text-sm bg-slate-50 p-4 rounded-lg border border-slate-200 h-64 overflow-y-auto font-mono">
                <?php
                try {
                    $seedType = 'empty';
                    
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $host = $_POST['db_host'] ?? 'localhost';
                        $db   = $_POST['db_name'] ?? '';
                        $user = $_POST['db_user'] ?? '';
                        $pass = $_POST['db_pass'] ?? '';
                        $seedType = $_POST['seedType'] ?? 'empty';
                        
                        // Testa a conexão
                        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                        $options = [
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES   => false,
                        ];
                        
                        try {
                            $pdo = new PDO($dsn, $user, $pass, $options);
                            echo "<div class='text-green-600'>✓ Conexão com o banco de dados estabelecida.</div>";
                            
                            // Gera config.php
                            $configCode = "<?php\n\$host = '$host';\n\$db   = '$db';\n\$user = '$user';\n\$pass = '$pass';\n\$charset = 'utf8mb4';\n\n\$dsn = \"mysql:host=\$host;dbname=\$db;charset=\$charset\";\n\$options = [\n    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n    PDO::ATTR_EMULATE_PREPARES   => false,\n];\n\nif (!is_dir(__DIR__ . '/backups')) {\n    @mkdir(__DIR__ . '/backups', 0755, true);\n}\n\ntry {\n     \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);\n} catch (\\PDOException \$e) {\n     throw new \\PDOException(\$e->getMessage(), (int)\$e->getCode());\n}\n?>";
                            
                            if (file_put_contents('config.php', $configCode) !== false) {
                                echo "<div class='text-green-600'>✓ Arquivo config.php gerado com sucesso.</div>";
                            } else {
                                echo "<div class='text-yellow-600'>⚠️ Aviso: Não foi possível escrever config.php, verifique permissões.</div>";
                            }
                        } catch (PDOException $e) {
                             throw new Exception("Falha na conexão com o banco ($db): " . $e->getMessage());
                        }
                    } else {
                        if (!file_exists('config.php')) {
                            throw new Exception("Arquivo config.php não encontrado e dados não enviados pelo formulário.");
                        }
                        require 'config.php';
                        echo "<div class='text-green-600'>✓ Conexão com o banco de dados (via config.php) estabelecida.</div>";
                        $seedType = $_GET['seedType'] ?? 'empty';
                    }

                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0755, true);
                        echo "<div class='text-green-600'>✓ Pasta 'uploads' criada com sucesso.</div>";
                    } else {
                        echo "<div class='text-slate-500'>- Pasta 'uploads' já existe.</div>";
                    }

                    if (file_exists('database.sql')) {
                        $sql = file_get_contents('database.sql');
                        $pdo->exec($sql);
                        echo "<div class='text-green-600'>✓ Arquivo database.sql importado. Tabelas base criadas.</div>";
                        
                        // Garante que as novas colunas existam caso o banco seja antigo
                        try { $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(100)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN category VARCHAR(50)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN technicianId VARCHAR(50)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN accessories VARCHAR(255)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN reportedIssue TEXT"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN internalNotes TEXT"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN laborCost DECIMAL(10,2)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE financeiro ADD COLUMN category VARCHAR(100)"); } catch(Exception $e) {}
                        try { $pdo->exec("ALTER TABLE financeiro ADD COLUMN osId VARCHAR(50)"); } catch(Exception $e) {}
                    } else {
                        echo "<div class='text-yellow-600'>Aviso: database.sql não encontrado. Não foi possível criar as tabelas.</div>";
                    }

                    if ($seedType === 'example') {
                        if (file_exists('seed.sql')) {
                            $sql = file_get_contents('seed.sql');
                            $pdo->exec($sql);
                            echo "<div class='text-green-600'>✓ Dados de exemplo importados (seed.sql).</div>";
                        } else {
                            echo "<div class='text-yellow-600'>Aviso: seed.sql não encontrado.</div>";
                        }
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

                    // Ensure password column exists if updating from old version
                    try { $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255)"); } catch(Exception $e) {}
                    try { $pdo->exec("ALTER TABLE users ADD COLUMN theme VARCHAR(50) DEFAULT 'light'"); } catch(Exception $e) {}

                    // Checa usuário admin
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin@php.info'");
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $hash = password_hash('admin', PASSWORD_DEFAULT);
                        $pdo->exec("INSERT INTO users (id, name, email, password, role) VALUES ('u_" . md5(uniqid()) . "', 'Administrador', 'admin@php.info', '{$hash}', 'ADMIN')");
                        echo "<div class='text-green-600 mt-2'>✓ Usuário administrador criado!<br><strong>Login:</strong> admin@php.info<br><strong>Senha:</strong> admin<br><strong>Nível:</strong> ADMIN</div>";
                    }

                    // Checa usuário tecnico
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'tecnico@php.info'");
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $hash = password_hash('tecnico', PASSWORD_DEFAULT);
                        $pdo->exec("INSERT INTO users (id, name, email, password, role) VALUES ('u_" . md5(uniqid()) . "', 'Técnico', 'tecnico@php.info', '{$hash}', 'TECNICO')");
                        echo "<div class='text-green-600 mt-2'>✓ Usuário técnico criado!<br><strong>Login:</strong> tecnico@php.info<br><strong>Senha:</strong> tecnico<br><strong>Nível:</strong> TECNICO</div>";
                    }

                    // Checa usuário caixa
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = 'caixa@php.info'");
                    $stmt->execute();
                    if ($stmt->fetchColumn() == 0) {
                        $hash = password_hash('caixa', PASSWORD_DEFAULT);
                        $pdo->exec("INSERT INTO users (id, name, email, password, role) VALUES ('u_" . md5(uniqid()) . "', 'Caixa', 'caixa@php.info', '{$hash}', 'CAIXA')");
                        echo "<div class='text-green-600 mt-2'>✓ Usuário caixa criado!<br><strong>Login:</strong> caixa@php.info<br><strong>Senha:</strong> caixa<br><strong>Nível:</strong> CAIXA</div>";
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
