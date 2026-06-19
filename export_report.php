<?php
require 'config.php';

if (!file_exists('fpdf.php')) {
    die("Erro: Biblioteca FPDF (fpdf.php) não encontrada. Por favor, execute o install.php novamente ou baixe o arquivo fpdf.php manualmente e o coloque no mesmo diretório.");
}
require 'fpdf.php';

$type = $_GET['type'] ?? 'financeiro';

// Criação do PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Busca dados da empresa
$settings = ['name' => 'Empresa Padrão'];
try {
    $stmt = $pdo->query("SELECT * FROM company_settings LIMIT 1");
    if ($row = $stmt->fetch()) {
        $settings = $row;
    }
} catch (Exception $e) {}

if ($type === 'financeiro') {
    $pdf->Cell(190, 10, "Relatorio Financeiro", 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(190, 10, mb_convert_encoding($settings['name'], 'ISO-8859-1', 'UTF-8') . ' - Gerado em: ' . date('d/m/Y H:i'), 0, 1, 'C');
    $pdf->Ln(10);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Data', 1);
    $pdf->Cell(70, 10, mb_convert_encoding('Descrição', 'ISO-8859-1', 'UTF-8'), 1);
    $pdf->Cell(40, 10, 'Tipo', 1);
    $pdf->Cell(40, 10, 'Valor (R$)', 1);
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 12);
    try {
        // Since we don't have a direct 'finance' table, we can summarize sales/OS or if 'finances' exists
        $totalSales = 0;
        $totalOS = 0;
        
        $stmt = $pdo->query("SELECT id, date, total, paymentMethod FROM sales ORDER BY date DESC LIMIT 20");
        $sales = $stmt->fetchAll();
        foreach ($sales as $sale) {
            $pdf->Cell(40, 10, date('d/m/Y', strtotime($sale['date'])), 1);
            $pdf->Cell(70, 10, 'Venda Balcao #' . substr($sale['id'], -4), 1);
            $pdf->Cell(40, 10, 'RECEITA', 1);
            $pdf->Cell(40, 10, number_format((float)$sale['total'], 2, ',', '.'), 1);
            $totalSales += (float)$sale['total'];
            $pdf->Ln();
        }
        
        $stmt2 = $pdo->query("SELECT id, createdAt, totalCost FROM service_orders WHERE status IN ('CONCLUIDA', 'ENTREGUE') ORDER BY createdAt DESC LIMIT 20");
        $orders = $stmt2->fetchAll();
        foreach ($orders as $os) {
            $pdf->Cell(40, 10, date('d/m/Y', strtotime(explode(' ', $os['createdAt'] ?? date('Y-m-d'))[0])), 1);
            $pdf->Cell(70, 10, 'OS #' . substr($os['id'], -4), 1);
            $pdf->Cell(40, 10, 'RECEITA', 1);
            $pdf->Cell(40, 10, number_format((float)$os['totalCost'], 2, ',', '.'), 1);
            $totalOS += (float)$os['totalCost'];
            $pdf->Ln();
        }
        
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 14);
        $total = $totalSales + $totalOS;
        $pdf->Cell(190, 10, 'TOTAL RECEITAS: R$ ' . number_format($total, 2, ',', '.'), 0, 1, 'R');
    } catch (Exception $e) {
        $pdf->Cell(190, 10, 'Erro ao gerar relatorio: ' . $e->getMessage(), 1, 1, 'L');
    }
} else if ($type === 'produtividade') {
    $pdf->Cell(190, 10, "Relatorio de Produtividade OS", 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(190, 10, mb_convert_encoding($settings['name'], 'ISO-8859-1', 'UTF-8') . ' - Gerado em: ' . date('d/m/Y H:i'), 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Status', 1);
    $pdf->Cell(40, 10, 'Quantidade', 1);
    $pdf->Cell(100, 10, 'Total Financeiro (R$)', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    try {
        $stmt = $pdo->query("SELECT status, COUNT(*) as qtd, SUM(totalCost) as sumCost FROM service_orders GROUP BY status");
        while ($row = $stmt->fetch()) {
            $status = $row['status'];
            $map = [
                'ABERTA'=>'Aberta', 'PARA_ORCAMENTO'=>'Para Orcamento', 'AGUARDANDO_APROVACAO'=>'Aguard. Aprovacao',
                'APROVADA'=>'Aprovada', 'EM_MANUTENCAO'=>'Em Manutencao', 'FINALIZADA'=>'Finalizada',
                'CONCLUIDA'=>'Concluida', 'ENTREGUE'=>'Entregue'
            ];
            $pdf->Cell(50, 10, $map[$status] ?? $status, 1);
            $pdf->Cell(40, 10, $row['qtd'], 1, 0, 'C');
            $pdf->Cell(100, 10, number_format((float)$row['sumCost'], 2, ',', '.'), 1, 1, 'R');
        }
    } catch (Exception $e) {
        $pdf->Cell(190, 10, 'Erro: ' . $e->getMessage(), 1, 1);
    }
}

$pdf->Output('I', 'relatorio_' . $type . '.pdf');
?>
