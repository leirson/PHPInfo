-- TechManager Database SQL Export
-- Generated on 2026-06-20T15:52:20.827Z

CREATE TABLE IF NOT EXISTS clients (
    id VARCHAR(50) PRIMARY KEY, 
    name VARCHAR(255), 
    document VARCHAR(100), 
    email VARCHAR(255), 
    phone VARCHAR(50), 
    address TEXT, 
    type VARCHAR(50), 
    createdAt VARCHAR(50), 
    updatedAt VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(50) PRIMARY KEY, 
    sku VARCHAR(100), 
    name VARCHAR(255), 
    description TEXT, 
    category VARCHAR(100), 
    price DECIMAL(10,2), 
    cost DECIMAL(10,2), 
    stock INT, 
    minStock INT, 
    createdAt VARCHAR(50), 
    updatedAt VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS services (
    id VARCHAR(50) PRIMARY KEY, 
    name VARCHAR(255), 
    price DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS service_orders (
    id VARCHAR(50) PRIMARY KEY, 
    clientId VARCHAR(50), 
    technicianId VARCHAR(50), 
    device VARCHAR(255), 
    accessories VARCHAR(255), 
    category VARCHAR(50), 
    issue TEXT, 
    reportedIssue TEXT, 
    internalNotes TEXT, 
    solution TEXT, 
    status VARCHAR(50), 
    laborCost DECIMAL(10,2), 
    totalCost DECIMAL(10,2), 
    createdAt VARCHAR(50), 
    updatedAt VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS service_order_items (
    id VARCHAR(50) PRIMARY KEY, 
    os_id VARCHAR(50), 
    item_id VARCHAR(50), 
    type VARCHAR(50), 
    name VARCHAR(255), 
    price DECIMAL(10,2), 
    qty INT
);

CREATE TABLE IF NOT EXISTS company_settings (
    id INT PRIMARY KEY, 
    name VARCHAR(255), 
    cnpj VARCHAR(100), 
    phone VARCHAR(50), 
    email VARCHAR(255), 
    address TEXT, 
    emit_nfe VARCHAR(10), 
    emit_nfse VARCHAR(10), 
    emit_danfe VARCHAR(10), 
    asten_user VARCHAR(255), 
    asten_pass VARCHAR(255), 
    asten_env VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS sales (
    id VARCHAR(50) PRIMARY KEY, 
    date VARCHAR(50), 
    total DECIMAL(10,2), 
    paymentMethod VARCHAR(50), 
    clientId VARCHAR(50), 
    totalProducts DECIMAL(10,2) DEFAULT 0, 
    totalServices DECIMAL(10,2) DEFAULT 0, 
    isNfceIssued VARCHAR(10) DEFAULT '0', 
    isNfseIssued VARCHAR(10) DEFAULT '0'
);

CREATE TABLE IF NOT EXISTS sales_items (
    id VARCHAR(50) PRIMARY KEY, 
    sale_id VARCHAR(50), 
    item_id VARCHAR(50), 
    type VARCHAR(50), 
    name VARCHAR(255), 
    price DECIMAL(10,2), 
    qty INT
);

CREATE TABLE IF NOT EXISTS financeiro (
    id VARCHAR(50) PRIMARY KEY, 
    description VARCHAR(255), 
    amount DECIMAL(10,2), 
    type VARCHAR(20), 
    category VARCHAR(100), 
    date VARCHAR(50), 
    status VARCHAR(50), 
    method VARCHAR(50), 
    osId VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS service_order_logs (
    id VARCHAR(50) PRIMARY KEY,
    os_id VARCHAR(50),
    action VARCHAR(255),
    user_name VARCHAR(100),
    timestamp VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(50),
    theme VARCHAR(50) DEFAULT 'light'
);

INSERT INTO users (id, name, email, password, role, theme) VALUES 
('u_admin123', 'Administrador', 'admin@php.info', 'admin', 'ADMIN', 'light'),
('u_tecnico123', 'Técnico', 'tecnico@php.info', 'tecnico', 'TECNICO', 'light'),
('u_caixa123', 'Caixa', 'caixa@php.info', 'caixa', 'CAIXA', 'light')
ON DUPLICATE KEY UPDATE email=VALUES(email);
