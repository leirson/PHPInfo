<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        // Allow default admin entry if no users exist
        $stmtCount = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmtCount->fetchColumn();
        
        if ($count == 0 && $email === 'admin@admin.com' && $password === 'admin') {
            $_SESSION['user_id'] = 'setup_admin';
            $_SESSION['user_name'] = 'Admin Setup';
            $_SESSION['user_role'] = 'ADMIN';
            header("Location: index.php");
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $valid = false;
            if (!empty($user['password'])) {
                if (password_verify($password, $user['password'])) {
                    $valid = true;
                } else if ($password === $user['password']) {
                    $valid = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUpdate = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmtUpdate->execute([$newHash, $user['id']]);
                }
            } else {
                // Senha em branco no banco (legado)
                if (trim($password) === '' || $password === '123456') {
                    $valid = true;
                }
            }
            
            if ($valid) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_theme'] = $user['theme'] ?? 'light';
                header("Location: index.php");
                exit;
            } else {
                 $error = 'Email ou senha inválidos.';
            }
        } else {
            $error = 'Email ou senha inválidos.';
        }
    } catch(Exception $e) {
        $error = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PHPInfo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 transition-colors duration-300">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Bem-vindo de volta</h1>
            <p class="text-slate-500 mt-2">Acesse sua conta para continuar</p>
        </div>
        
        <?php if ($error): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-xl flex items-start space-x-3 mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <span class="text-sm font-medium"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="mail" class="h-5 w-5 text-slate-400"></i>
                    </div>
                    <input type="email" name="email" required class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-700 placeholder-slate-400 transition" placeholder="seu@email.com">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Senha</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="h-5 w-5 text-slate-400"></i>
                    </div>
                    <input type="password" name="password" required class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-slate-700 placeholder-slate-400 transition" placeholder="••••••••">
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-medium py-2.5 px-4 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 transition shadow-sm flex items-center justify-center">
                <span>Entrar</span>
                <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
            </button>
        </form>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
