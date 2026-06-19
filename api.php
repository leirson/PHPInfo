<?php
require 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!file_exists(__DIR__ . '/.db_synced')) {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS clients (id VARCHAR(50) PRIMARY KEY, name VARCHAR(255), document VARCHAR(100), email VARCHAR(255), phone VARCHAR(50), address TEXT, type VARCHAR(50), createdAt VARCHAR(50), updatedAt VARCHAR(50))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS products (id VARCHAR(50) PRIMARY KEY, sku VARCHAR(100), name VARCHAR(255), description TEXT, category VARCHAR(100), price DECIMAL(10,2), cost DECIMAL(10,2), stock INT, minStock INT, createdAt VARCHAR(50), updatedAt VARCHAR(50))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS services (id VARCHAR(50) PRIMARY KEY, name VARCHAR(255), price DECIMAL(10,2))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS service_orders (id VARCHAR(50) PRIMARY KEY, clientId VARCHAR(50), technicianId VARCHAR(50), device VARCHAR(255), accessories VARCHAR(255), category VARCHAR(50), issue TEXT, reportedIssue TEXT, internalNotes TEXT, solution TEXT, status VARCHAR(50), laborCost DECIMAL(10,2), totalCost DECIMAL(10,2), createdAt VARCHAR(50), updatedAt VARCHAR(50))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS service_order_items (id VARCHAR(50) PRIMARY KEY, os_id VARCHAR(50), item_id VARCHAR(50), type VARCHAR(50), name VARCHAR(255), price DECIMAL(10,2), qty INT)");
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (id VARCHAR(50) PRIMARY KEY, name VARCHAR(255), email VARCHAR(255), role VARCHAR(50))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS company_settings (id INT PRIMARY KEY, name VARCHAR(255), cnpj VARCHAR(100), phone VARCHAR(50), email VARCHAR(255), address TEXT, emit_nfe VARCHAR(10), emit_nfse VARCHAR(10), emit_danfe VARCHAR(10), asten_user VARCHAR(255), asten_pass VARCHAR(255), asten_env VARCHAR(50))");
        $pdo->exec("CREATE TABLE IF NOT EXISTS sales (id VARCHAR(50) PRIMARY KEY, date VARCHAR(50), total DECIMAL(10,2), paymentMethod VARCHAR(50), clientId VARCHAR(50), totalProducts DECIMAL(10,2) DEFAULT 0, totalServices DECIMAL(10,2) DEFAULT 0, isNfceIssued VARCHAR(10) DEFAULT '0', isNfseIssued VARCHAR(10) DEFAULT '0')");
        $pdo->exec("CREATE TABLE IF NOT EXISTS sales_items (id VARCHAR(50) PRIMARY KEY, sale_id VARCHAR(50), item_id VARCHAR(50), type VARCHAR(50), name VARCHAR(255), price DECIMAL(10,2), qty INT)");
        
        // Fallbacks
        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN issue TEXT"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE service_orders ADD COLUMN solution TEXT"); } catch (Exception $e) {}
        
        // NFe/NFCe Settings
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN cert_password VARCHAR(255)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN cert_file TEXT"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN nfe_serie VARCHAR(10)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN nfce_serie VARCHAR(10)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN nfe_numero INT DEFAULT 1"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN nfce_numero INT DEFAULT 1"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN csc_id VARCHAR(50)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN csc_token VARCHAR(255)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN regime_tributario VARCHAR(50)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE company_settings ADD COLUMN logo TEXT"); } catch(Exception $e) {}
        
        // Sales modifications
        try { $pdo->exec("ALTER TABLE sales ADD COLUMN clientId VARCHAR(50)"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE sales ADD COLUMN totalProducts DECIMAL(10,2) DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE sales ADD COLUMN totalServices DECIMAL(10,2) DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE sales ADD COLUMN isNfceIssued VARCHAR(10) DEFAULT '0'"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE sales ADD COLUMN isNfseIssued VARCHAR(10) DEFAULT '0'"); } catch (Exception $e) {}
        
        // Users modifications
        try { $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255)"); } catch(Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN theme VARCHAR(50) DEFAULT 'light'"); } catch(Exception $e) {}

        file_put_contents(__DIR__ . '/.db_synced', '1');
    } catch(Exception $e) {}
}

$action = $_GET['action'] ?? 'dashboard';

try {
    if ($action === 'dashboard') {
        $stmtOsAbertas = $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status NOT IN ('FINALIZADA', 'CONCLUIDA', 'ENTREGUE')");
        $osAbertas = $stmtOsAbertas->fetchColumn();

        $stmtVendas = $pdo->query("SELECT SUM(total) FROM sales");
        $totalVendas = $stmtVendas->fetchColumn() ?: 0;
        
        $stmtServicos = $pdo->query("SELECT SUM(totalCost) FROM service_orders WHERE status IN ('FINALIZADA', 'CONCLUIDA', 'ENTREGUE')");
        $totalServicos = $stmtServicos->fetchColumn() ?: 0;

        $stmtClientes = $pdo->query("SELECT COUNT(*) FROM clients");
        $totalClientes = $stmtClientes->fetchColumn();

        $stmtAguardando = $pdo->query("SELECT COUNT(*) FROM service_orders WHERE status = 'AGUARDANDO_APROVACAO'");
        $osAguardandoAprovacao = $stmtAguardando->fetchColumn();
        
        $stmtAlertas = $pdo->query("
            SELECT o.id, c.name AS clientName, o.device, o.status, o.createdAt 
            FROM service_orders o
            LEFT JOIN clients c ON o.clientId = c.id
            WHERE o.status IN ('ABERTA', 'AGUARDANDO_APROVACAO')
            ORDER BY o.createdAt DESC LIMIT 5
        ");
        $alertas = $stmtAlertas->fetchAll();

        echo json_encode([
            'osAbertas' => $osAbertas,
            'totalVendas' => $totalVendas,
            'totalServicos' => $totalServicos,
            'totalReceitas' => $totalVendas + $totalServicos,
            'totalClientes' => $totalClientes,
            'osAguardandoAprovacao' => $osAguardandoAprovacao,
            'alertas' => $alertas
        ]);
        exit;
    }

    if ($action === 'financeInfo') {
        $stmtVendas = $pdo->query("SELECT SUM(total) FROM sales");
        $totalVendas = $stmtVendas->fetchColumn() ?: 0;
        
        $stmtServicos = $pdo->query("SELECT SUM(totalCost) FROM service_orders WHERE status IN ('FINALIZADA', 'CONCLUIDA', 'ENTREGUE')");
        $totalServicos = $stmtServicos->fetchColumn() ?: 0;
        
        $totalRevenue = $totalVendas + $totalServicos;
        
        $stmtTechs = $pdo->query("
            SELECT u.name, 
                   COUNT(o.id) as osResolvidas, 
                   SUM(o.laborCost) as faturamento
            FROM users u
            LEFT JOIN service_orders o ON u.name = o.technicianId AND o.status IN ('FINALIZADA', 'CONCLUIDA', 'ENTREGUE')
            WHERE u.role = 'TECNICO' OR u.role = 'ADMIN'
            GROUP BY u.id
        ");
        $productivityData = $stmtTechs->fetchAll();

        echo json_encode([
            'totalRevenue' => $totalRevenue,
            'productivityData' => $productivityData
        ]);
        exit;
    }

    if ($action === 'clients') {
        $stmt = $pdo->query("SELECT id, name, email, phone, document, createdAt FROM clients ORDER BY createdAt DESC");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'get_client') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($action === 'save_client') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? '';
        $document = $data['document'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE clients SET name=?, document=?, email=?, phone=? WHERE id=?");
            $stmt->execute([$name, $document, $email, $phone, $id]);
        } else {
            $id = 'CLI-' . date('YmdHis');
            $createdAt = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO clients (id, name, document, email, phone, createdAt) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $name, $document, $email, $phone, $createdAt]);
        }
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'products') {
        $stmt = $pdo->query("SELECT id, name, description, price, stock, sku FROM products");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'get_product') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($action === 'save_product') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? '';
        $sku = $data['sku'] ?? '';
        $price = $data['price'] ?? 0;
        $stock = $data['stock'] ?? 0;
        $description = $data['description'] ?? '';
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, sku=?, price=?, stock=?, description=? WHERE id=?");
            $stmt->execute([$name, $sku, $price, $stock, $description, $id]);
        } else {
            $id = 'PROD-' . date('YmdHis');
            $stmt = $pdo->prepare("INSERT INTO products (id, name, sku, price, stock, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $name, $sku, $price, $stock, $description]);
        }
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'os') {
        $stmt = $pdo->query("
            SELECT o.id, c.name AS clientName, o.clientId, o.device, o.category, o.status, o.totalCost, o.createdAt 
            FROM service_orders o
            LEFT JOIN clients c ON o.clientId = c.id
            ORDER BY o.createdAt DESC
        ");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'get_os') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM service_orders WHERE id = ?");
        $stmt->execute([$id]);
        $os = $stmt->fetch();
        
        $stmtItems = $pdo->prepare("SELECT * FROM service_order_items WHERE os_id = ?");
        $stmtItems->execute([$id]);
        $os['items'] = $stmtItems->fetchAll();
        
        echo json_encode($os);
        exit;
    }

    if ($action === 'save_os') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? 'ABERTA';
        $totalCost = $data['totalCost'] ?? 0;
        $device = $data['device'] ?? '';
        $issue = $data['issue'] ?? '';
        $solution = $data['solution'] ?? '';
        $category = $data['category'] ?? 'Manutenção';
        $clientId = $data['clientId'] ?? '';
        $items = $data['items'] ?? [];
        
        $laborCost = 0;
        foreach ($items as $item) {
            $price = isset($item['price']) ? (float)$item['price'] : 0;
            if (isset($item['type']) && $item['type'] === 'Serviço') {
                $laborCost += $price;
            }
        }
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE service_orders SET status = ?, totalCost = ?, laborCost = ?, device = ?, issue = ?, solution = ?, category = ?, clientId = ? WHERE id = ?");
            $stmt->execute([$status, $totalCost, $laborCost, $device, $issue, $solution, $category, $clientId, $id]);
        } else {
            $id = 'OS-' . date('YmdHis');
            $stmt = $pdo->prepare("INSERT INTO service_orders (id, clientId, device, issue, solution, category, status, totalCost, laborCost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $clientId, $device, $issue, $solution, $category, $status, $totalCost, $laborCost]);
        }
        
        // Robust fallback for table creation
        $pdo->exec("CREATE TABLE IF NOT EXISTS service_order_items (
            id VARCHAR(50) PRIMARY KEY,
            os_id VARCHAR(50),
            item_id VARCHAR(50),
            type VARCHAR(50),
            name VARCHAR(255),
            price DECIMAL(10,2),
            qty INT
        )");

        // Save items
        $pdo->prepare("DELETE FROM service_order_items WHERE os_id = ?")->execute([$id]);
        foreach ($items as $item) {
            $itemId = 'OSI-' . uniqid();
            $stmt = $pdo->prepare("INSERT INTO service_order_items (id, os_id, item_id, type, name, price, qty) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$itemId, $id, $item['id'], $item['type'], $item['name'], $item['price'], 1]);
        }
        
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'get_settings') {
        $stmt = $pdo->query("SELECT * FROM company_settings WHERE id = 1");
        $settings = $stmt->fetch() ?: [];
        echo json_encode($settings);
        exit;
    }

    if ($action === 'save_settings') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM company_settings WHERE id = 1");
            $exists = $stmt->fetchColumn() > 0;
            
            if ($exists) {
                $stmt = $pdo->query("SELECT cert_file, logo FROM company_settings WHERE id = 1");
                $currentData = $stmt->fetch();
                $certFile = !empty($data['cert_file']) ? $data['cert_file'] : $currentData['cert_file'];
                $logo = !empty($data['logo']) ? $data['logo'] : $currentData['logo'];

                $stmt = $pdo->prepare("UPDATE company_settings SET name=?, cnpj=?, phone=?, email=?, address=?, emit_nfe=?, emit_nfse=?, emit_danfe=?, asten_user=?, asten_pass=?, asten_env=?, cert_password=?, cert_file=?, nfe_serie=?, nfce_serie=?, nfe_numero=?, nfce_numero=?, csc_id=?, csc_token=?, regime_tributario=?, logo=? WHERE id = 1");
                $stmt->execute([
                    $data['name'] ?? '', $data['cnpj'] ?? '', $data['phone'] ?? '', $data['email'] ?? '', $data['address'] ?? '',
                    $data['emit_nfe'] ?? '0', $data['emit_nfse'] ?? '0', $data['emit_danfe'] ?? '0',
                    $data['asten_user'] ?? '', $data['asten_pass'] ?? '', $data['asten_env'] ?? 'homologacao',
                    $data['cert_password'] ?? '', $certFile ?? '', $data['nfe_serie'] ?? '',
                    $data['nfce_serie'] ?? '', $data['nfe_numero'] ?? 1, $data['nfce_numero'] ?? 1,
                    $data['csc_id'] ?? '', $data['csc_token'] ?? '', $data['regime_tributario'] ?? 'Simples Nacional', $logo
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO company_settings (id, name, cnpj, phone, email, address, emit_nfe, emit_nfse, emit_danfe, asten_user, asten_pass, asten_env, cert_password, cert_file, nfe_serie, nfce_serie, nfe_numero, nfce_numero, csc_id, csc_token, regime_tributario, logo) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['name'] ?? '', $data['cnpj'] ?? '', $data['phone'] ?? '', $data['email'] ?? '', $data['address'] ?? '',
                    $data['emit_nfe'] ?? '0', $data['emit_nfse'] ?? '0', $data['emit_danfe'] ?? '0',
                    $data['asten_user'] ?? '', $data['asten_pass'] ?? '', $data['asten_env'] ?? 'homologacao',
                    $data['cert_password'] ?? '', $data['cert_file'] ?? '', $data['nfe_serie'] ?? '',
                    $data['nfce_serie'] ?? '', $data['nfe_numero'] ?? 1, $data['nfce_numero'] ?? 1,
                    $data['csc_id'] ?? '', $data['csc_token'] ?? '', $data['regime_tributario'] ?? 'Simples Nacional', $data['logo'] ?? ''
                ]);
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'checkout') {
        $data = json_decode(file_get_contents('php://input'), true);
        $total = $data['total'] ?? 0;
        $paymentMethod = $data['paymentMethod'] ?? 'DINHEIRO';
        $clientId = $data['clientId'] ?? null;
        $id = 'SALE-' . date('YmdHis') . '-' . rand(100,999);
        $date = date('Y-m-d H:i:s');
        
        $items = $data['items'] ?? [];
        $totalProducts = 0;
        $totalServices = 0;
        foreach ($items as $item) {
            $price = isset($item['price']) ? (float)$item['price'] : 0;
            if (isset($item['type']) && $item['type'] === 'Produto') {
                $totalProducts += $price;
            } else if (isset($item['type']) && ($item['type'] === 'Ordem de Serviço')) {
                $osId = $item['id'] ?? '';
                $stmt = $pdo->prepare("SELECT laborCost, totalCost FROM service_orders WHERE id = ?");
                $stmt->execute([$osId]);
                $osData = $stmt->fetch();
                if ($osData) {
                    $labor = (float)$osData['laborCost'];
                    $totalCost = (float)$osData['totalCost'];
                    $totalServices += $labor;
                    $totalProducts += ($totalCost - $labor);
                } else {
                    $totalServices += $price;
                }
            } else {
                $totalServices += $price;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO sales (id, date, total, paymentMethod, clientId, totalProducts, totalServices, isNfceIssued, isNfseIssued) VALUES (?, ?, ?, ?, ?, ?, ?, '0', '0')");
        $stmt->execute([$id, $date, $total, $paymentMethod, $clientId, $totalProducts, $totalServices]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'history') {
        $stmt = $pdo->query("
            SELECT s.*, c.name as clientName 
            FROM sales s 
            LEFT JOIN clients c ON s.clientId = c.id 
            ORDER BY s.date DESC
        ");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'issueNfce') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("UPDATE sales SET isNfceIssued = '1' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'issueNfse') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("UPDATE sales SET isNfseIssued = '1' WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'finance') {
        $stmt = $pdo->query("SELECT id, date, total, paymentMethod, isNfseIssued FROM sales ORDER BY date DESC");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'services') {
        $stmt = $pdo->query("SELECT id, name, price FROM services");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'get_service') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT id, name, price FROM services WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($action === 'save_service') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE services SET name=?, price=? WHERE id=?");
            $stmt->execute([$name, $price, $id]);
        } else {
            $id = 'SRV-' . date('YmdHis');
            $stmt = $pdo->prepare("INSERT INTO services (id, name, price) VALUES (?, ?, ?)");
            $stmt->execute([$id, $name, $price]);
        }
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'backup_db') {
        $tables = ['clients', 'products', 'services', 'service_orders', 'service_order_items', 'users', 'sales', 'sales_items', 'settings'];
        $sql = "-- Backup gerado em " . date('Y-m-d H:i:s') . "\n\n";
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT * FROM $table");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($rows) > 0) {
                    $sql .= "-- Tabela $table\n";
                    foreach ($rows as $row) {
                        $keys = array_keys($row);
                        $values = array_values($row);
                        $keysStr = implode(", ", $keys);
                        $valuesStr = implode(", ", array_map(function($val) use ($pdo) {
                            if ($val === null) return 'NULL';
                            return $pdo->quote($val);
                        }, $values));
                        $sql .= "INSERT INTO $table ($keysStr) VALUES ($valuesStr);\n";
                    }
                    $sql .= "\n";
                }
            } catch (Exception $e) {
                // Ignore if table doesn't exist
            }
        }
        if (!is_dir(__DIR__ . '/backups')) {
            @mkdir(__DIR__ . '/backups', 0755, true);
        }
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $filepath = __DIR__ . '/backups/' . $filename;
        if (file_put_contents($filepath, $sql) !== false) {
            echo json_encode(['success' => true, 'file' => 'backups/' . $filename, 'message' => 'Backup gerado com sucesso!']);
        } else {
            echo json_encode(['error' => 'Não foi possível salvar o arquivo de backup. Verifique as permissões da pasta "backups".']);
        }
        exit;
    }

    if ($action === 'list_backups') {
        if (!is_dir(__DIR__ . '/backups')) {
            @mkdir(__DIR__ . '/backups', 0755, true);
        }
        $files = glob(__DIR__ . '/backups/*.sql') ?: [];
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        $latestFiles = array_slice($files, 0, 5);
        $backups = array_map(function($file) {
            return [
                'filename' => basename($file),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'size' => filesize($file)
            ];
        }, $latestFiles);
        echo json_encode($backups);
        exit;
    }

    if ($action === 'restore_backup') {
        $data = json_decode(file_get_contents('php://input'), true);
        $filename = $data['filename'] ?? '';
        $filepath = __DIR__ . '/backups/' . basename($filename);
        if (file_exists($filepath)) {
            $sql = file_get_contents($filepath);
            try {
                $pdo->exec($sql);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['error' => 'Erro ao restaurar: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'Arquivo não encontrado']);
        }
        exit;
    }

    if ($action === 'delete_backup') {
        $data = json_decode(file_get_contents('php://input'), true);
        $filename = $data['filename'] ?? '';
        $filepath = __DIR__ . '/backups/' . basename($filename);
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Erro ao excluir o arquivo']);
            }
        } else {
            echo json_encode(['error' => 'Arquivo não encontrado']);
        }
        exit;
    }

    if ($action === 'upload_backup') {
        if (!is_dir(__DIR__ . '/backups')) {
            @mkdir(__DIR__ . '/backups', 0755, true);
        }
        if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['backup_file']['tmp_name'];
            $name = basename($_FILES['backup_file']['name']);
            
            // Allow only SQL files
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'sql') {
                echo json_encode(['error' => 'Apenas arquivos .sql são permitidos']);
                exit;
            }

            $filepath = __DIR__ . '/backups/' . $name;
            if (move_uploaded_file($tmpName, $filepath)) {
                echo json_encode(['success' => true, 'message' => 'Upload realizado com sucesso!']);
            } else {
                echo json_encode(['error' => 'Falha ao salvar o arquivo submetido.']);
            }
        } else {
            echo json_encode(['error' => 'Nenhum arquivo válido enviado.']);
        }
        exit;
    }

    if ($action === 'users') {
        $stmt = $pdo->query("SELECT id, name, email, role, theme FROM users");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($action === 'get_user') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("SELECT id, name, email, role, theme FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch());
        exit;
    }

    if ($action === 'save_user') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $role = $data['role'] ?? 'TECNICO';
        $theme = $data['theme'] ?? 'light';
        $password = $data['password'] ?? '';
        
        if ($id) {
            // Keep theme in session or simply update logic (localStorage usage assumes no true session)
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, theme=?, password=? WHERE id=?");
                $stmt->execute([$name, $email, $role, $theme, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, theme=? WHERE id=?");
                $stmt->execute([$name, $email, $role, $theme, $id]);
            }
        } else {
            $id = 'USR-' . date('YmdHis');
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (id, name, email, role, theme, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $name, $email, $role, $theme, $hash]);
        }
        echo json_encode(['success' => true, 'id' => $id, 'theme' => $theme]);
        exit;
    }

    echo json_encode(['error' => 'Invalid action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
