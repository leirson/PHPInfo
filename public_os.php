<?php
require 'config.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    die("ID da Ordem de Serviço não informado.");
}

$stmt = $pdo->prepare("SELECT * FROM service_orders WHERE id = ?");
$stmt->execute([$id]);
$os = $stmt->fetch();

if (!$os) {
    die("Ordem de Serviço não encontrada.");
}

// Buscar cliente
$stmtClient = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmtClient->execute([$os['clientId'] ?? '']);
$client = $stmtClient->fetch();

// Buscar itens
$stmtItems = $pdo->prepare("SELECT * FROM service_order_items WHERE os_id = ?");
$stmtItems->execute([$id]);
$items = $stmtItems->fetchAll();

$settings = ['name' => 'PHPInfo'];
try {
    $stmtSet = $pdo->query("SELECT * FROM company_settings LIMIT 1");
    if ($r = $stmtSet->fetch()) $settings = $r;
} catch (Exception $e) {}

// Determinar cor do status
$statusColors = [
    'ABERTA' => 'bg-yellow-100 text-yellow-800',
    'PARA_ORCAMENTO' => 'bg-slate-100 text-slate-800',
    'AGUARDANDO_APROVACAO' => 'bg-orange-100 text-orange-800',
    'APROVADA' => 'bg-indigo-100 text-indigo-800',
    'EM_MANUTENCAO' => 'bg-purple-100 text-purple-800',
    'FINALIZADA' => 'bg-green-100 text-green-800',
    'CONCLUIDA' => 'bg-emerald-100 text-emerald-800',
    'ENTREGUE' => 'bg-teal-100 text-teal-800',
];
$statusColor = $statusColors[$os['status']] ?? 'bg-slate-100 text-slate-800';

$mapStatus = [
    'ABERTA' => 'Aberta',
    'PARA_ORCAMENTO' => 'Para Orçamento',
    'AGUARDANDO_APROVACAO' => 'Aguardando Aprovação',
    'APROVADA' => 'Aprovada',
    'EM_MANUTENCAO' => 'Em Manutenção',
    'FINALIZADA' => 'Finalizada',
    'CONCLUIDA' => 'Concluída',
    'ENTREGUE' => 'Entregue'
];
$statusName = $mapStatus[$os['status']] ?? $os['status'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Ordem de Serviço</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen p-4 flex justify-center py-10">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-fit">
        <div class="p-6 bg-slate-900 text-white text-center flex flex-col items-center justify-center">
            <?php if (!empty($settings['logo'])): ?>
                <img src="<?= htmlspecialchars($settings['logo']) ?>" alt="Logo" class="max-h-20 mb-4 bg-white/10 p-2 rounded-lg object-contain backdrop-blur">
            <?php endif; ?>
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($settings['name']) ?></h1>
            <p class="text-slate-400 mt-1">Acompanhamento de Ordem de Serviço</p>
        </div>
        
        <div class="p-6 md:p-10 space-y-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-100 pb-6 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800">OS #<?= htmlspecialchars(substr($os['id'], -6)) ?></h2>
                    <p class="text-slate-500 mt-1">Data de Abertura: <?= htmlspecialchars(date('d/m/Y', strtotime($os['createdAt']))) ?></p>
                </div>
                <span class="px-4 py-2 rounded-full font-semibold <?= $statusColor ?>">
                    <?= htmlspecialchars($statusName) ?>
                </span>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-3 flex items-center"><i data-lucide="user" class="w-5 h-5 mr-2 text-blue-500"></i> Informações do Cliente</h3>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2">
                    <p><strong>Nome:</strong> <?= htmlspecialchars($client['name'] ?? 'Não informado') ?></p>
                    <p><strong>Telefone:</strong> <?= htmlspecialchars($client['phone'] ?? 'Não informado') ?></p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-3 flex items-center"><i data-lucide="smartphone" class="w-5 h-5 mr-2 text-indigo-500"></i> Detalhes do Aparelho</h3>
                <div class="space-y-4">
                    <div>
                        <strong class="block text-slate-500 text-sm">Aparelho/Modelo</strong>
                        <p class="text-slate-800"><?= htmlspecialchars($os['device'] ?? '-') ?></p>
                    </div>
                    <div>
                        <strong class="block text-slate-500 text-sm">Problema Relatado</strong>
                        <p class="text-slate-800"><?= htmlspecialchars($os['issue'] ?? '-') ?></p>
                    </div>
                    <?php if (!empty($os['solution'])): ?>
                    <div>
                        <strong class="block text-slate-500 text-sm">Solução do Problema</strong>
                        <p class="text-slate-800"><?= htmlspecialchars($os['solution']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (count($items) > 0): ?>
            <div>
                <h3 class="text-lg font-semibold text-slate-800 mb-3 flex items-center"><i data-lucide="tool" class="w-5 h-5 mr-2 text-emerald-500"></i> Serviços e Peças</h3>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="p-3 font-semibold text-slate-600">Descrição</th>
                                <th class="p-3 font-semibold text-slate-600 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="p-3 text-slate-800"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="p-3 text-slate-800 text-right">R$ <?= number_format((float)$item['price'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td class="p-3 text-slate-600 text-right">Total:</td>
                                <td class="p-3 text-emerald-600 text-right">R$ <?= number_format((float)$os['totalCost'], 2, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        <div class="bg-slate-50 p-6 text-center text-sm text-slate-500 border-t border-slate-200">
            Acompanhamento online gerado por <?= htmlspecialchars($settings['name']) ?>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
