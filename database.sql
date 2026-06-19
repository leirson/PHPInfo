-- TechManager Database SQL Export
-- Generated on 2026-06-19T17:47:33.828Z

CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  role VARCHAR(50)
);
INSERT INTO users (id, name, email, role) VALUES ('u1', 'Admin Master', 'admin@techmanager.com', 'ADMIN');
INSERT INTO users (id, name, email, role) VALUES ('u2', 'João Técnico', 'joao@techmanager.com', 'TECNICO');
INSERT INTO users (id, name, email, role) VALUES ('u3', 'Maria Caixa', 'maria@techmanager.com', 'CAIXA');

CREATE TABLE IF NOT EXISTS clients (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255),
  phone VARCHAR(50),
  email VARCHAR(255),
  document VARCHAR(50),
  createdAt VARCHAR(50)
);
INSERT INTO clients (id, name, phone, email, document, createdAt) VALUES ('c1', 'Empresa Alpha Ltda', '(11) 99999-1111', 'contato@alpha.com', '12.345.678/0001-90', '2026-05-20T15:26:11.856Z');
INSERT INTO clients (id, name, phone, email, document, createdAt) VALUES ('c2', 'Carlos Almeida', '(11) 98888-2222', 'carlos@gmail.com', '123.456.789-00', '2026-06-04T15:26:11.856Z');

CREATE TABLE IF NOT EXISTS products (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255),
  description TEXT,
  price DECIMAL(10,2),
  stock INT,
  sku VARCHAR(50)
);
INSERT INTO products (id, name, description, price, stock, sku) VALUES ('p1', 'SSD Kingston 480GB', 'SSD SATA 3', 250, 15, 'SSD-480-KNG');
INSERT INTO products (id, name, description, price, stock, sku) VALUES ('p2', 'Memória RAM DDR4 8GB', 'DDR4 2666MHz Crucial', 180, 20, 'RAM-8GB-DDR4');
INSERT INTO products (id, name, description, price, stock, sku) VALUES ('p3', 'Fonte ATX 500W', 'Fonte 80 Plus Bronze', 320, 8, 'FONTE-500W');

CREATE TABLE IF NOT EXISTS service_orders (
  id VARCHAR(50) PRIMARY KEY,
  clientId VARCHAR(50),
  technicianId VARCHAR(50),
  device VARCHAR(255),
  accessories VARCHAR(255),
  category VARCHAR(50),
  reportedIssue TEXT,
  internalNotes TEXT,
  solution TEXT,
  status VARCHAR(50),
  laborCost DECIMAL(10,2),
  totalCost DECIMAL(10,2),
  createdAt VARCHAR(50),
  updatedAt VARCHAR(50)
);
INSERT INTO service_orders (id, clientId, technicianId, device, accessories, category, reportedIssue, internalNotes, solution, status, laborCost, totalCost, createdAt, updatedAt) VALUES ('os-1001', 'c2', 'u2', 'Notebook Dell Inspiron 15', '', '', 'Tela piscando e aparelho esquentando muito após meia hora de uso.', 'Necessário limpar cooler e trocar pasta térmica. Tela pode estar com cabo flat solto.', '', 'ABERTA', 150, 150, '2026-06-19T15:26:11.856Z', '2026-06-19T15:26:11.856Z');
INSERT INTO service_orders (id, clientId, technicianId, device, accessories, category, reportedIssue, internalNotes, solution, status, laborCost, totalCost, createdAt, updatedAt) VALUES ('os-1000', 'c1', 'u2', 'PC Desktop RH', '', '', 'Não liga, bipa 3 vezes.', 'Memória queimada. Substituída.', '', 'AGUARDANDO_APROVACAO', 100, 280, '2026-06-17T15:26:11.856Z', '2026-06-18T15:26:11.856Z');

CREATE TABLE IF NOT EXISTS sales (
  id VARCHAR(50) PRIMARY KEY,
  date VARCHAR(50),
  cashierId VARCHAR(50),
  total DECIMAL(10,2),
  paymentMethod VARCHAR(50),
  isNfseIssued BOOLEAN
);
INSERT INTO sales (id, date, cashierId, total, paymentMethod, isNfseIssued) VALUES ('s-1', '2026-06-14T15:26:11.856Z', 'u3', 250, 'PIX', 1);
