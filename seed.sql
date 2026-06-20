-- Arquivo de Exemplo (Seed) - Produtos, Serviços, Clientes e OS

INSERT INTO products (id, sku, name, description, category, price, cost, stock, minStock, createdAt, updatedAt) VALUES
('PROD-001', 'SKU-001', 'Película de Vidro iPhone 13', 'Película protetora temperada', 'Acessórios', 30.00, 5.00, 100, 10, '2023-01-01', '2023-01-01'),
('PROD-002', 'SKU-002', 'Cabo USB-C Baseus 1M', 'Cabo de carregamento rápido 100W', 'Acessórios', 45.00, 15.00, 50, 10, '2023-01-01', '2023-01-01'),
('PROD-003', 'SKU-003', 'Cabo Lightining Original', 'Cabo de carregamento iPhone', 'Acessórios', 80.00, 25.00, 40, 10, '2023-01-01', '2023-01-01'),
('PROD-004', 'SKU-004', 'Carregador Turbo 20W', 'Fonte carregador tipo C', 'Acessórios', 60.00, 20.00, 60, 10, '2023-01-01', '2023-01-01'),
('PROD-005', 'SKU-005', 'Capa Silicone iPhone 12', 'Capa protetora flexível', 'Acessórios', 40.00, 10.00, 80, 5, '2023-01-01', '2023-01-01'),
('PROD-006', 'SKU-006', 'Fone Bluetooth TWS', 'Fone de ouvido sem fio', 'Acessórios', 120.00, 45.00, 30, 5, '2023-01-01', '2023-01-01'),
('PROD-007', 'SKU-007', 'Bateria iPhone 8 Plus', 'Bateria de reposição', 'Peças', 150.00, 50.00, 20, 5, '2023-01-01', '2023-01-01'),
('PROD-008', 'SKU-008', 'Tela LCD iPhone 11', 'Display touch screen de reposição', 'Peças', 250.00, 120.00, 15, 3, '2023-01-01', '2023-01-01'),
('PROD-009', 'SKU-009', 'Tela AMOLED Samsung S20', 'Display touch screen de reposição', 'Peças', 600.00, 350.00, 5, 2, '2023-01-01', '2023-01-01'),
('PROD-010', 'SKU-010', 'Conector de Carga Tipo C', 'Placa conector de carga', 'Peças', 45.00, 15.00, 25, 5, '2023-01-01', '2023-01-01');

INSERT INTO services (id, name, price) VALUES
('SERV-001', 'Troca de Tela iPhone', 150.00),
('SERV-002', 'Troca de Tela Samsung', 180.00),
('SERV-003', 'Troca de Bateria iPhone', 100.00),
('SERV-004', 'Troca de Bateria Android', 120.00),
('SERV-005', 'Reparo em Placa (Curto)', 350.00),
('SERV-006', 'Desoxidação em Banho Químico', 200.00),
('SERV-007', 'Troca de Conector de Carga', 120.00),
('SERV-008', 'Restauração de Software (Formatação)', 80.00),
('SERV-009', 'Remoção de Conta Google / iCloud', 150.00),
('SERV-010', 'Instalação de Película Protetora', 15.00);

INSERT INTO clients (id, name, document, email, phone, address, type, createdAt, updatedAt) VALUES
('CLI-001', 'João da Silva', '111.222.333-44', 'joao.silva@email.com', '(11) 98888-7777', 'Rua das Flores, 123, Centro', 'Física', '2023-01-01T10:00:00.000Z', '2023-01-01T10:00:00.000Z'),
('CLI-002', 'Maria Oliveira', '222.333.444-55', 'maria.oliveira@email.com', '(11) 97777-6666', 'Av. Paulista, 1000, Bela Vista', 'Física', '2023-01-02T11:30:00.000Z', '2023-01-02T11:30:00.000Z'),
('CLI-003', 'Tech Solutions Ltda', '12.345.678/0001-90', 'contato@techsolutions.com', '(11) 3333-4444', 'Rua da Inovação, 500, Pinheiros', 'Jurídica', '2023-01-05T09:15:00.000Z', '2023-01-05T09:15:00.000Z');

INSERT INTO service_orders (id, clientId, technicianId, device, accessories, category, issue, reportedIssue, internalNotes, solution, status, laborCost, totalCost, createdAt, updatedAt) VALUES
('OS-1001', 'CLI-001', 'u_tecnico123', 'iPhone 11 Preto 64GB', 'Capa, Carregador', 'Smartphone', 'Tela quebrada, touch falhando', 'Caiu no chão e trincou a tela', 'Verificar FaceID após a troca', 'Troca da Tela LCD', 'CONCLUIDA', 150.00, 400.00, '2023-02-01T08:00:00.000Z', '2023-02-02T15:30:00.000Z'),
('OS-1002', 'CLI-002', 'u_tecnico123', 'Samsung Galaxy S20', 'Nenhum', 'Smartphone', 'Não liga, não carrega', 'Aparelho não dá sinal de vida', '', '', 'EM_MANUTENCAO', 120.00, 165.00, '2023-02-10T14:20:00.000Z', '2023-02-10T14:20:00.000Z'),
('OS-1003', 'CLI-003', 'u_tecnico123', 'Notebook Dell Inspiron', 'Carregador', 'Computador', 'Lento, travando', 'Windows muito lento e aquecendo', 'Fazer limpeza interna e formatar', '', 'AGUARDANDO_APROVACAO', 80.00, 80.00, '2023-02-15T09:10:00.000Z', '2023-02-15T12:00:00.000Z');

INSERT INTO service_order_items (id, os_id, item_id, type, name, price, qty) VALUES
('ITEM-001', 'OS-1001', 'SERV-001', 'S', 'Troca de Tela iPhone', 150.00, 1),
('ITEM-002', 'OS-1001', 'PROD-008', 'P', 'Tela LCD iPhone 11', 250.00, 1),
('ITEM-003', 'OS-1002', 'SERV-007', 'S', 'Troca de Conector de Carga', 120.00, 1),
('ITEM-004', 'OS-1002', 'PROD-010', 'P', 'Conector de Carga Tipo C', 45.00, 1),
('ITEM-005', 'OS-1003', 'SERV-008', 'S', 'Restauração de Software (Formatação)', 80.00, 1);

-- FIM DA SEED
