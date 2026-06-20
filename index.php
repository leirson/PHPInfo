<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPInfo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        };
    </script>
    <style>
        body.theme-dark { background-color: #0f172a; color: #f8fafc; }
        body.theme-dark .bg-white { background-color: #1e293b; color: #f8fafc; border-color: #334155; }
        body.theme-dark .bg-slate-50 { background-color: #0f172a; }
        body.theme-dark .text-slate-800, body.theme-dark .text-slate-700 { color: #f8fafc; }
        body.theme-dark .text-slate-600, body.theme-dark .text-slate-500 { color: #cbd5e1; }
        body.theme-dark .border-slate-200 { border-color: #334155; }
        body.theme-dark input, body.theme-dark select, body.theme-dark textarea { background-color: #0f172a; color: #f1f5f9; border-color: #334155; }

        body.theme-blue { background-color: #eff6ff; }
        body.theme-blue .bg-white { border-color: #bfdbfe; }
        body.theme-blue aside { background-color: #1e3a8a; }

        body.theme-green { background-color: #f0fdf4; }
        body.theme-green .bg-white { border-color: #bbf7d0; }
        body.theme-green aside { background-color: #14532d; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('app_theme') || 'light';
            if (theme !== 'light') {
                document.documentElement.classList.add('theme-' + theme);
            }
        })();
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex transition-colors duration-300">
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-white flex flex-col fixed inset-y-0 z-40 shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="h-16 flex items-center justify-between border-b border-slate-800 px-6">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i data-lucide="wrench" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xl font-bold tracking-tight">PHPInfo</span>
            </div>
            <button class="md:hidden text-slate-400 hover:text-white" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <button onclick="loadData('dashboard')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-dashboard">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium">Dashboard</span>
            </button>
            <button onclick="loadData('clients')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-clients">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="font-medium">Clientes</span>
            </button>
            <button onclick="loadData('users')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-users">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="font-medium">Usuários</span>
            </button>
            <button onclick="loadData('os')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-os">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span class="font-medium">Ordens de Serviço</span>
            </button>
            <button onclick="loadData('sales')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-sales">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                <span class="font-medium">Caixa / PDV</span>
            </button>
            <button onclick="loadData('history')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-history">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span class="font-medium">Histórico de Vendas</span>
            </button>
            <button onclick="loadData('finance')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-finance">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                <span class="font-medium">Financeiro</span>
            </button>
            <button onclick="loadData('products')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-products">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span class="font-medium">Estoque</span>
            </button>
            <button onclick="loadData('services')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-services">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
                <span class="font-medium">Serviços</span>
            </button>
            <button onclick="loadData('settings')" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-colors" id="btn-settings">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="font-medium">Config. Empresa</span>
            </button>
        </div>
         <div class="p-4 border-t border-slate-800">
             <div class="flex flex-col space-y-2">
                 <div class="flex items-center space-x-3 px-4 py-2">
                     <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold"><?php echo substr($_SESSION['user_name'] ?? 'U', 0, 1); ?></div>
                     <div>
                         <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                         <p class="text-xs text-slate-400"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'role'); ?></p>
                     </div>
                 </div>
                 <button onclick="logout()" class="flex items-center justify-center space-x-2 text-slate-400 hover:text-white px-2 py-1 rounded hover:bg-slate-800 transition">
                     <i data-lucide="log-out" class="w-4 h-4"></i><span class="text-xs font-semibold">Sair / Logout</span>
                 </button>
             </div>
         </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-0 md:ml-64 flex flex-col min-h-screen w-full transition-all duration-300">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-10 shadow-sm">
          <div class="flex items-center space-x-3">
              <button class="md:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition" onclick="toggleSidebar()">
                  <i data-lucide="menu" class="w-6 h-6"></i>
              </button>
              <h1 class="text-xl font-bold text-slate-800 truncate" id="page-title">Ordens de Serviço</h1>
          </div>
          <div class="flex items-center space-x-4">
             <button class="p-2 text-slate-400 hover:bg-slate-100 rounded-full transition relative">
                 <i data-lucide="bell" class="w-5 h-5"></i>
                 <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
             </button>
          </div>
        </header>

        <main class="flex-1 p-8">
            <div id="dashboard-view" class="hidden space-y-6">
                <!-- Dashboard content injected via JS -->
            </div>
            
            <div id="table-controls" class="hidden mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 gap-4 sm:gap-0">
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-72">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" id="search-input" placeholder="Pesquisar..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition" onkeydown="if(event.key === 'Enter') handleSearch()">
                    </div>
                    <button onclick="handleSearch()" class="bg-slate-100 text-slate-700 border border-slate-300 px-4 py-2 rounded-lg font-medium hover:bg-slate-200 transition flex items-center space-x-2 text-sm whitespace-nowrap">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
                <button id="btn-new" onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 hover:bg-blue-700 transition w-full sm:w-auto justify-center sm:justify-start">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span id="btn-new-text">Novo</span>
                </button>
            </div>

            <div id="table-view" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto" id="results">
                    <div class="p-8 text-center text-slate-500">Carregando dados do MySQL...</div>
                </div>
            </div>
        </main>
    </div>

    <div id="modal-overlay" class="fixed inset-0 bg-slate-900/50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div id="modal-content" class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0">
            <div class="flex justify-between items-center mb-6">
                <h2 id="modal-title" class="text-xl font-bold text-slate-800">Novo Registro</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition p-2 rounded-full hover:bg-slate-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="modal-form" onsubmit="submitForm(event)" class="space-y-4">
                <!-- Form fields injected here -->
                <div id="modal-fields"></div>
                <div class="pt-4 flex justify-end space-x-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-slate-600 font-medium hover:bg-slate-100 rounded-lg transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-md">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        lucide.createIcons();

        const titles = {
            'dashboard': 'Dashboard',
            'os': 'Ordens de Serviço',
            'clients': 'Clientes',
            'products': 'Estoque',
            'services': 'Serviços',
            'finance': 'Financeiro',
            'sales': 'Caixa / PDV',
            'history': 'Histórico de Vendas',
            'users': 'Usuários',
            'customer_panel': 'Painel do Cliente',
            'settings': 'Configurações da Empresa'
        };

        const newBtnText = {
            'os': 'Nova OS',
            'clients': 'Novo Cliente',
            'products': 'Novo Produto',
            'services': 'Novo Serviço',
            'sales': 'Nova Venda',
            'finance': 'Novo Lançamento',
            'users': 'Novo Usuário'
        };

        let currentData = [];
        let currentAction = 'dashboard';

        async function openModal(id = null) {
             const m = document.getElementById('modal-overlay');
             const c = document.getElementById('modal-content');
             const f = document.getElementById('modal-fields');
             m.classList.remove('hidden');
             setTimeout(() => { c.classList.remove('scale-95', 'opacity-0'); }, 10);
             
             // store edit ID
             document.getElementById('modal-form').dataset.editId = id || '';
             
             f.innerHTML = '<div class="p-8 text-center text-slate-500 flex justify-center"><i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-500"></i></div>';
             lucide.createIcons();

             let osData = null;
             let clientsData = [];
             let productsData = [];
             let servicesData = [];
             let itemData = null;
             
             try {
                 if (currentAction === 'os') {
                     const [resC, resP, resS] = await Promise.all([
                        fetch('api.php?action=clients'),
                        fetch('api.php?action=products'),
                        fetch('api.php?action=services')
                     ]);
                     clientsData = await resC.json();
                     productsData = await resP.json();
                     servicesData = await resS.json();
                     
                     if (id) {
                         const resO = await fetch(`api.php?action=get_os&id=${id}`);
                         osData = await resO.json();
                     }
                 } else if (id) {
                     if (currentAction === 'clients') {
                         const r = await fetch(`api.php?action=get_client&id=${id}`);
                         itemData = await r.json();
                     } else if (currentAction === 'products') {
                         const r = await fetch(`api.php?action=get_product&id=${id}`);
                         itemData = await r.json();
                     } else if (currentAction === 'users') {
                         const r = await fetch(`api.php?action=get_user&id=${id}`);
                         itemData = await r.json();
                     } else if (currentAction === 'services') {
                         const r = await fetch(`api.php?action=get_service&id=${id}`);
                         itemData = await r.json();
                     }
                 }
             } catch(e) { console.error(e); }
             
             if (currentAction === 'clients') {
                  document.getElementById('modal-title').innerText = id ? 'Editar Cliente' : 'Novo Cliente';
                  f.innerHTML = `
                    <input type="hidden" name="id" value="${itemData ? itemData.id : ''}">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Nome Completo</label><input type="text" name="name" value="${itemData ? itemData.name : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">CPF/CNPJ</label><input type="text" name="document" value="${itemData ? itemData.document : ''}" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label><input type="email" name="email" value="${itemData ? itemData.email : ''}" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Telefone / WhatsApp</label><input type="text" name="phone" value="${itemData ? itemData.phone : ''}" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                  `;
             } else if (currentAction === 'products') {
                  document.getElementById('modal-title').innerText = id ? 'Editar Produto' : 'Novo Produto';
                  f.innerHTML = `
                    <input type="hidden" name="id" value="${itemData ? itemData.id : ''}">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Nome do Produto</label><input type="text" name="name" value="${itemData ? itemData.name : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">SKU</label><input type="text" name="sku" value="${itemData ? itemData.sku : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Preço (R$)</label><input type="number" step="0.01" name="price" value="${itemData ? itemData.price : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Estoque</label><input type="number" name="stock" value="${itemData ? itemData.stock : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    </div>
                  `;
             } else if (currentAction === 'services') {
                  document.getElementById('modal-title').innerText = id ? 'Editar Serviço' : 'Novo Serviço';
                  f.innerHTML = `
                    <input type="hidden" name="id" value="${itemData ? itemData.id : ''}">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Nome do Serviço</label><input type="text" name="name" value="${itemData ? itemData.name : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Valor Padrão (R$)</label><input type="number" step="0.01" name="price" value="${itemData ? itemData.price : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                  `;
             } else if (currentAction === 'users') {
                  document.getElementById('modal-title').innerText = id ? 'Editar Usuário' : 'Novo Usuário';
                  f.innerHTML = `
                    <input type="hidden" name="id" value="${itemData ? itemData.id : ''}">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Nome do Usuário</label><input type="text" name="name" value="${itemData ? itemData.name : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label><input type="email" name="email" value="${itemData ? itemData.email : ''}" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Senha ${id ? '(Deixe em branco para manter)' : ''}</label><input type="password" name="password" ${id ? '' : 'required'} class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Perfil</label>
                        <select name="role" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="TECNICO" ${itemData && itemData.role === 'TECNICO' ? 'selected' : ''}>Técnico</option>
                            <option value="ADMIN" ${itemData && itemData.role === 'ADMIN' ? 'selected' : ''}>Administrador</option>
                            <option value="CAIXA" ${itemData && itemData.role === 'CAIXA' ? 'selected' : ''}>Caixa</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Tema Preferido</label>
                        <select name="theme" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="light" ${itemData && itemData.theme === 'light' ? 'selected' : ''}>Claro Padrão</option>
                            <option value="dark" ${itemData && itemData.theme === 'dark' ? 'selected' : ''}>Escuro</option>
                            <option value="blue" ${itemData && itemData.theme === 'blue' ? 'selected' : ''}>Azul</option>
                            <option value="green" ${itemData && itemData.theme === 'green' ? 'selected' : ''}>Verde</option>
                        </select>
                    </div>
                  `;
             } else if (currentAction === 'os') {
                  document.getElementById('modal-title').innerText = id ? 'Editar OS #' + id.split('-').pop() : 'Nova Ordem de Serviço';
                  let clientOptions = '<option value="">Selecione um cliente...</option>';
                  clientsData.forEach(c => {
                      const selected = (osData && osData.clientId == c.id) ? 'selected' : '';
                      clientOptions += `<option value="${c.id}" ${selected}>${c.name}</option>`;
                  });
                  
                  let itemOptions = '<option value="">Adicionar Produto/Serviço...</option>';
                  productsData.forEach(p => {
                      itemOptions += `<option value="P:${p.id}:${p.price}">${p.name} (Produto) - ${formatCurrency(p.price)}</option>`;
                  });
                  servicesData.forEach(s => {
                      itemOptions += `<option value="S:${s.id}:${s.price}">${s.name} (Serviço) - ${formatCurrency(s.price)}</option>`;
                  });

                  // Restore previously selected items if editing
                  if (osData && osData.items && osData.items.length) {
                      window._tempOsItems = osData.items.map(i => ({ type: i.type, id: i.item_id, name: i.name, price: i.price }));
                  } else {
                      window._tempOsItems = [];
                  }

                  const statusOptions = ['ABERTA', 'AGUARDANDO_APROVACAO', 'EM_MANUTENCAO', 'APROVADA', 'FINALIZADA', 'CONCLUIDA', 'ENTREGUE'].map(st => {
                       const selected = (osData && osData.status === st) ? 'selected' : '';
                       return `<option value="${st}" ${selected}>${getStatusLabel(st)}</option>`;
                  }).join('');

                  f.innerHTML = `
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Cliente</label><select name="clientId" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">${clientOptions}</select></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Aparelho / Dispositivo</label><input type="text" name="device" value="${osData ? osData.device : ''}" required placeholder="Ex: Notebook Dell Inspiron" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Problema Relatado</label><textarea name="issue" rows="2" required class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">${osData ? (osData.issue || '') : ''}</textarea></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Solução do Problema</label><textarea name="solution" rows="2" placeholder="Descreva a solução..." class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">${osData ? (osData.solution || '') : ''}</textarea></div>
                    
                    <div class="border-t border-slate-200 pt-3 mt-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Itens (Produtos e Serviços)</label>
                        <select id="osItemSelect" onchange="addOsItemFromSelect(this)" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 mb-2">${itemOptions}</select>
                        <div id="osItemsList" class="space-y-2 mb-2 max-h-32 overflow-y-auto"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Categoria</label><select name="category" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                             <option ${osData && osData.category == 'Manutenção' ? 'selected' : ''}>Manutenção</option>
                             <option ${osData && osData.category == 'Instalação' ? 'selected' : ''}>Instalação</option>
                             <option ${osData && osData.category == 'Garantia' ? 'selected' : ''}>Garantia</option>
                        </select></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label><select name="status" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            ${statusOptions}
                        </select></div>
                        <div class="col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Valor Total (R$)</label><input type="number" step="0.01" name="totalCost" id="osTotalCost" value="${osData ? osData.totalCost : ''}" placeholder="0.00" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                    </div>
                  `;
                  
                  setTimeout(renderOsItems, 50); // immediate render
             } else {
                  document.getElementById('modal-title').innerText = 'Em breve';
                  f.innerHTML = '<p class="text-slate-500 text-center py-4">Funcionalidade completa integrada apenas na versão Node.js da AI Studio.</p>';
             }
        }

        function closeModal() {
             const m = document.getElementById('modal-overlay');
             const c = document.getElementById('modal-content');
             c.classList.add('scale-95', 'opacity-0');
             setTimeout(() => { m.classList.add('hidden'); }, 200);
        }

        function addOsItemFromSelect(selectEl) {
            const val = selectEl.value;
            if (!val) return;
            const text = selectEl.options[selectEl.selectedIndex].text.split(' - ')[0]; // remove price string
            const [type, id, priceStr] = val.split(':');
            window._tempOsItems.push({
                type: type === 'P' ? 'Produto' : 'Serviço',
                id: id,
                name: text,
                price: parseFloat(priceStr)
            });
            selectEl.value = ''; // reset selection
            renderOsItems();
        }

        function removeOsItem(index) {
            window._tempOsItems.splice(index, 1);
            renderOsItems();
        }

        function renderOsItems() {
            let total = 0;
            let html = '';
            (window._tempOsItems || []).forEach((item, idx) => {
                total += item.price;
                html += `<div class="flex justify-between items-center text-sm bg-slate-50 p-2 rounded border border-slate-100">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-medium text-slate-800 truncate">${item.name}</p>
                        <p class="text-xs text-slate-500">${item.type} • ${formatCurrency(item.price)}</p>
                    </div>
                    <button type="button" onclick="removeOsItem(${idx})" class="text-red-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>`;
            });
            if (!html) html = '<div class="text-xs text-slate-400 text-center py-2">Nenhum item adicionado</div>';
            document.getElementById('osItemsList').innerHTML = html;
            document.getElementById('osTotalCost').value = total.toFixed(2);
            lucide.createIcons();
        }

        async function submitForm(e) {
             e.preventDefault();
             const form = e.target;
             const btn = form.querySelector('button[type="submit"]');
             const oldText = btn.innerText;
             btn.innerText = 'Salvando...';
             btn.disabled = true;

             try {
                 if (currentAction === 'os') {
                     const data = {
                         id: form.dataset.editId || null,
                         clientId: form.clientId.value,
                         device: form.device.value,
                         issue: form.issue.value,
                         solution: form.solution.value,
                         category: form.category.value,
                         status: form.status.value,
                         totalCost: form.totalCost.value || 0,
                         items: window._tempOsItems || []
                     };
                     const res = await fetch('api.php?action=save_os', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify(data)
                     });
                     if (res.ok) { closeModal(); loadData('os'); } else { const errJson = await res.json().catch(()=>({})); alert('Erro ao salvar. ' + (errJson.error || '')); }
                 } else if (currentAction === 'services') {
                     const data = {
                         id: form.id?.value || null,
                         name: form.name.value,
                         price: form.price.value
                     };
                     const res = await fetch('api.php?action=save_service', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify(data)
                     });
                     if (res.ok) { closeModal(); loadData('services'); } else alert('Erro ao salvar.');
                 } else if (currentAction === 'clients') {
                     const data = {
                         id: form.id?.value || null,
                         name: form.name.value,
                         document: form.document.value,
                         email: form.email.value,
                         phone: form.phone.value
                     };
                     const res = await fetch('api.php?action=save_client', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify(data)
                     });
                     if (res.ok) { closeModal(); loadData('clients'); } else alert('Erro ao salvar.');
                 } else if (currentAction === 'products') {
                     const data = {
                         id: form.id?.value || null,
                         name: form.name.value,
                         sku: form.sku.value,
                         price: form.price.value,
                         stock: form.stock.value
                     };
                     const res = await fetch('api.php?action=save_product', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify(data)
                     });
                     if (res.ok) { closeModal(); loadData('products'); } else alert('Erro ao salvar.');
                 } else if (currentAction === 'users') {
                     const data = {
                         id: form.id?.value || null,
                         name: form.name.value,
                         email: form.email.value,
                         role: form.role.value,
                         theme: form.theme?.value || 'light',
                         password: form.password?.value || ''
                     };
                     const res = await fetch('api.php?action=save_user', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify(data)
                     });
                     if (res.ok) { 
                         const resData = await res.json();
                         // Simulando que o usuário editado seja ele mesmo (aplicando o tema)
                         localStorage.setItem('app_theme', resData.theme || data.theme);
                         document.documentElement.classList.remove('theme-light', 'theme-dark', 'theme-blue', 'theme-green');
                         if ((resData.theme || data.theme) !== 'light') {
                             document.documentElement.classList.add('theme-[theme]'.replace('[theme]', resData.theme || data.theme));
                         }

                         closeModal(); 
                         loadData('users'); 
                     } else {
                         alert('Erro ao salvar.');
                     }
                 } else {
                     alert('Salvar não implementado para este módulo no frontend.');
                     closeModal();
                 }
             } catch (err) {
                 alert(err.message);
             } finally {
                 if (btn) {
                     btn.innerText = oldText;
                     btn.disabled = false;
                 }
             }
        }

        function handleSearch() {
            const query = document.getElementById('search-input').value.toLowerCase();
            if (!query) {
                renderTable(currentAction, currentData);
                return;
            }
            const filtered = currentData.filter(row => {
                return Object.values(row).some(val => String(val).toLowerCase().includes(query));
            });
            renderTable(currentAction, filtered);
        }

        async function loadData(action) {
            currentAction = action;
            document.getElementById('page-title').innerText = titles[action] || '';
            document.getElementById('results').innerHTML = '<div class="p-8 text-center text-slate-500 flex justify-center"><i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-500"></i></div>';
            document.getElementById('search-input').value = '';
            lucide.createIcons();

            if (action === 'dashboard' || action === 'customer_panel' || action === 'settings') {
                document.getElementById('table-view').classList.add('hidden');
                document.getElementById('table-controls').classList.add('hidden');
                document.getElementById('dashboard-view').classList.remove('hidden');
                if (action === 'customer_panel') {
                     document.getElementById('dashboard-view').innerHTML = `
                     <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-xl overflow-hidden mt-10 border border-slate-100">
                         <div class="bg-blue-600 px-8 py-10 text-white text-center">
                              <i data-lucide="wrench" class="w-12 h-12 mx-auto mb-4 text-blue-100"></i>
                              <h2 class="text-3xl font-bold mb-2">Acompanhe sua OS</h2>
                              <p class="text-blue-100">Consulte o status do seu serviço online</p>
                         </div>
                         <div class="p-8 space-y-6">
                              <div>
                                  <label class="block text-sm font-medium text-slate-700 mb-2">Código da OS ou CPF/CNPJ</label>
                                  <div class="relative">
                                      <input type="text" placeholder="Ex: OS-12345 ou 000.000.000-00" class="w-full pl-4 pr-12 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-lg">
                                      <i data-lucide="search" class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                                  </div>
                              </div>
                              <button onclick="alert('Consulta será conectada ao banco via api.php futuramente.')" class="w-full bg-blue-600 text-white font-bold py-4 rounded-lg hover:bg-blue-700 transition">Consultar</button>
                         </div>
                     </div>`;
                     lucide.createIcons();
                     return;
                } else if (action === 'settings') {
                     renderSettings();
                     return;
                }
            } else {
                document.getElementById('dashboard-view').classList.add('hidden');
                document.getElementById('table-view').classList.remove('hidden');
                
                document.getElementById('table-controls').classList.remove('hidden');
                
                if (action === 'history') {
                    document.getElementById('btn-new').classList.add('hidden');
                } else {
                    document.getElementById('btn-new').classList.remove('hidden');
                    document.getElementById('btn-new-text').innerText = newBtnText[action] || 'Novo';
                }
            }
            
            // Stylize buttons
            ['dashboard', 'os', 'clients', 'products', 'services', 'sales', 'history', 'finance', 'users', 'customer_panel', 'settings'].forEach(a => {
                const btn = document.getElementById('btn-' + a);
                if (btn) {
                    if (a === action) {
                        btn.classList.add('bg-blue-600', 'text-white');
                        btn.classList.remove('text-slate-400', 'hover:bg-slate-800');
                    } else {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('text-slate-400', 'hover:bg-slate-800');
                    }
                }
            });

            try {
                if (action === 'sales') {
                    renderPOS();
                    return;
                }
                
                if (action === 'finance') {
                    renderFinance();
                    return;
                }

                const res = await fetch(`api.php?action=${action}`);
                const data = await res.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                if (action === 'dashboard') {
                    renderDashboard(data);
                    return;
                }
                
                currentData = data;

                if (data.length === 0) {
                     document.getElementById('results').innerHTML = '<div class="p-8 text-center text-slate-500">Nenhum registro encontrado.</div>';
                     return;
                }

                renderTable(action, data);

            } catch(e) {
                document.getElementById('results').innerHTML = `<div class="p-8 text-center text-red-500">Erro ao carregar dados. Verifique a conexão com o banco e se o script SQL foi importado. (${e.message})</div>`;
            }
        }

        function getStatusColor(status) {
            switch (status) {
                case 'ABERTA': return 'bg-red-100 text-red-800';
                case 'PARA_ORCAMENTO': return 'bg-purple-100 text-purple-800';
                case 'AGUARDANDO_APROVACAO': return 'bg-orange-100 text-orange-800';
                case 'APROVADA': return 'bg-indigo-100 text-indigo-800';
                case 'EM_MANUTENCAO': return 'bg-yellow-100 text-yellow-800';
                case 'FINALIZADA': return 'bg-emerald-100 text-emerald-800';
                case 'CONCLUIDA': return 'bg-green-100 text-green-800';
                case 'ENTREGUE': return 'bg-emerald-100 text-emerald-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusLabel(status) {
            const map = {
                'ABERTA': 'Pendente',
                'PARA_ORCAMENTO': 'Para Orçamento',
                'AGUARDANDO_APROVACAO': 'Aguard. Aprovação',
                'APROVADA': 'Aprovada',
                'EM_MANUTENCAO': 'Em Andamento',
                'FINALIZADA': 'Finalizada',
                'CONCLUIDA': 'Concluído',
                'ENTREGUE': 'Entregue',
            };
            return map[status] || status;
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        }

        function formatDate(dateStr) {
             const date = new Date(dateStr);
             return date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        async function renderPOS() {
            document.getElementById('table-controls').classList.add('hidden');
            document.getElementById('dashboard-view').classList.add('hidden');
            document.getElementById('table-view').classList.remove('hidden');
            document.getElementById('results').innerHTML = `<div class="p-8 text-center text-slate-500 flex justify-center"><i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-500"></i><span class="ml-2">Carregando PDV...</span></div>`;
            lucide.createIcons();
            
            try {
                const [resProds, resServs, resOs, resClients] = await Promise.all([
                    fetch('api.php?action=products'),
                    fetch('api.php?action=services'),
                    fetch('api.php?action=os'),
                    fetch('api.php?action=clients')
                ]);
                const products = await resProds.json();
                const services = await resServs.json();
                const orders = await resOs.json();
                const clients = await resClients.json();
                
                // Expose globally for the search to work
                window.posData = { products, services, orders, clients };
                
                let html = `
                <div class="flex flex-col lg:flex-row h-full">
                    <div class="flex-1 space-y-4 p-6 bg-slate-50">
                        <div class="relative flex space-x-2">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                                <input type="text" id="pos-search" onkeydown="if(event.key === 'Enter') handlePosSearch()" placeholder="Buscar OS, Produto, Serviço ou Cliente..." class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                            </div>
                            <button onclick="handlePosSearch()" class="bg-indigo-600 text-white px-6 py-4 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm whitespace-nowrap flex items-center space-x-2">
                                <i data-lucide="filter" class="w-5 h-5"></i>
                                <span>Filtrar</span>
                            </button>
                        </div>
                        <div class="flex space-x-2 mt-4 overflow-x-auto pb-2 scrollbar-none">
                            <button onclick="setPosFilter('all')" class="pos-filter-btn px-4 py-2 rounded-lg bg-blue-600 text-white font-medium text-sm whitespace-nowrap" id="pos-filter-all">Todos</button>
                            <button onclick="setPosFilter('os')" class="pos-filter-btn px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-sm whitespace-nowrap" id="pos-filter-os">Ordens de Serviço</button>
                            <button onclick="setPosFilter('products')" class="pos-filter-btn px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-sm whitespace-nowrap" id="pos-filter-products">Produtos</button>
                            <button onclick="setPosFilter('services')" class="pos-filter-btn px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-sm whitespace-nowrap" id="pos-filter-services">Serviços</button>
                            <button onclick="setPosFilter('clients')" class="pos-filter-btn px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-sm whitespace-nowrap" id="pos-filter-clients">Clientes</button>
                        </div>
                        <div id="pos-results" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">
                        </div>
                    </div>
                    <div class="w-full lg:w-[400px] bg-white border-l border-slate-200 shadow-xl flex flex-col min-h-[calc(100vh-4rem)] z-10 sticky top-16">
                         <div class="p-6 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-slate-800 flex items-center"><i data-lucide="shopping-cart" class="w-6 h-6 mr-3 text-indigo-600"></i> Caixa</h3>
                            <button onclick="clearPosCart()" class="text-sm text-red-500 font-medium hover:underline">Limpar</button>
                         </div>
                         <div id="pos-cliente-selecionado" class="px-6 py-3 bg-blue-50 border-b border-blue-100 hidden">
                              <p class="text-xs text-blue-600 font-bold uppercase mb-1">Cliente Vinculado</p>
                              <div class="flex justify-between items-center"><p class="font-medium text-blue-900" id="pos-cliente-nome">-</p>
                              <button onclick="removePosClient()" class="text-blue-400 hover:text-red-500"><i data-lucide="x" class="w-4 h-4"></i></button></div>
                         </div>
                         <div id="pos-cart" class="flex-1 overflow-y-auto bg-white p-6 flex flex-col items-center justify-center text-slate-400 border-b border-slate-200">
                              <i data-lucide="package-open" class="w-16 h-16 mb-4 text-slate-200"></i>
                              <p class="font-medium text-lg">Carrinho vazio</p>
                         </div>
                         <div class="p-6 bg-slate-50 space-y-4">
                              <div class="flex justify-between items-center text-slate-500 font-medium pb-4 border-b border-slate-200"><p>Subtotal</p><p id="pos-subtotal">R$ 0,00</p></div>
                              <div class="flex justify-between items-end pb-4"><p class="text-slate-500 font-semibold mb-1">Total</p><p id="pos-total" class="text-3xl font-black text-slate-900 pointer-events-none">R$ 0,00</p></div>
                              
                              <div class="mb-4">
                                  <label class="block text-sm font-medium text-slate-700 mb-1">Forma de Pagamento</label>
                                  <select id="pos-paymentMethod" class="w-full border border-slate-300 px-3 py-2 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                                      <option value="DINHEIRO">Dinheiro</option>
                                      <option value="CARTAO_CREDITO">Cartão de Crédito</option>
                                      <option value="CARTAO_DEBITO">Cartão de Débito</option>
                                      <option value="PIX">PIX</option>
                                  </select>
                              </div>
                              <button onclick="checkoutPos()" id="pos-checkoutBtn" class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl hover:bg-emerald-700 hover:shadow-lg transition flex items-center justify-center text-lg"><i data-lucide="banknote" class="w-6 h-6 mr-2"></i> Registrar Pagamento</button>
                         </div>
                    </div>
                </div>`;
                
                document.getElementById('table-view').classList.remove('p-8');
                document.getElementById('table-view').classList.add('p-0');
                document.getElementById('results').innerHTML = html;
                lucide.createIcons();
                handlePosSearch(); // initial render
            } catch(e) {
                document.getElementById('results').innerHTML = `<div class="p-8 text-center text-red-500 font-medium"><i data-lucide="alert-triangle" class="w-8 h-8 mx-auto mb-3"></i>Erro: ${e.message}</div>`;
                lucide.createIcons();
            }
        }

        let posCart = [];
        let posClient = null;

        function renderPosCart() {
            const cartContainer = document.getElementById('pos-cart');
            if (posCart.length === 0) {
                cartContainer.innerHTML = `
                    <i data-lucide="package-open" class="w-16 h-16 mb-4 text-slate-200"></i>
                    <p class="font-medium text-lg">Carrinho vazio</p>
                `;
                document.getElementById('pos-subtotal').innerText = 'R$ 0,00';
                document.getElementById('pos-total').innerText = 'R$ 0,00';
            } else {
                let html = '<div class="w-full space-y-3">';
                let total = 0;
                posCart.forEach((item, index) => {
                    total += Number(item.price);
                    html += `
                        <div class="flex justify-between items-center bg-white border border-slate-100 p-3 rounded-lg shadow-sm">
                             <div class="flex-1 min-w-0 pr-2">
                                 <p class="font-medium text-sm text-slate-800 truncate">${item.name}</p>
                                 <p class="text-xs text-slate-500">${item.type} • ${formatCurrency(item.price)}</p>
                             </div>
                             <button onclick="removePosItem(${index})" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-500 rounded"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </div>
                    `;
                });
                html += '</div>';
                cartContainer.innerHTML = html;
                document.getElementById('pos-subtotal').innerText = formatCurrency(total);
                document.getElementById('pos-total').innerText = formatCurrency(total);
            }
            lucide.createIcons();
        }

        function removePosItem(index) {
            posCart.splice(index, 1);
            renderPosCart();
        }

        function addPosItem(type, id) {
            const data = window.posData;
            let item = null;
            if (type === 'P') {
                const prod = data.products.find(p => p.id === id);
                if (prod) item = { id: prod.id, name: prod.name, price: prod.price, type: 'Produto' };
            } else if (type === 'S') {
                const srv = data.services.find(s => s.id === id);
                if (srv) item = { id: srv.id, name: srv.name, price: srv.price, type: 'Serviço' };
            } else if (type === 'O') {
                const o = data.orders.find(o => o.id === id);
                if (o) {
                    item = { id: o.id, name: 'OS #' + o.id.split('-').pop() + ' - ' + o.device, price: o.totalCost, type: 'Ordem de Serviço' };
                    if (o.clientId && o.clientName) {
                        setPosClient(o.clientId, o.clientName);
                    }
                }
            }
            if (item) {
                posCart.push(item);
                renderPosCart();
            }
        }

        function setPosClient(id, name) {
            posClient = { id, name };
            document.getElementById('pos-cliente-nome').innerText = name;
            document.getElementById('pos-cliente-selecionado').classList.remove('hidden');
        }

        function removePosClient() {
            posClient = null;
            document.getElementById('pos-cliente-selecionado').classList.add('hidden');
        }

        function clearPosCart() {
            posCart = [];
            removePosClient();
            renderPosCart();
        }

        async function checkoutPos() {
            if (posCart.length === 0) {
                alert('O carrinho está vazio');
                return;
            }
            
            const total = posCart.reduce((sum, item) => sum + Number(item.price), 0);
            const paymentMethod = document.getElementById('pos-paymentMethod').value;
            
            const btn = document.getElementById('pos-checkoutBtn');
            const oldText = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-6 h-6 mr-2 animate-spin"></i> Processando...';
            btn.disabled = true;
            lucide.createIcons();

            try {
                const res = await fetch('api.php?action=checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        total: total,
                        paymentMethod: paymentMethod,
                        clientId: posClient ? posClient.id : null,
                        items: posCart
                    })
                });
                
                if (res.ok) {
                    alert('Pagamento registrado com sucesso!');
                    clearPosCart();
                } else {
                    alert('Erro ao registrar pagamento.');
                }
            } catch (err) {
                alert(err.message);
            } finally {
                btn.innerHTML = oldText;
                btn.disabled = false;
                lucide.createIcons();
            }
        }

        let currentPosFilter = 'all';
        function setPosFilter(filter) {
            currentPosFilter = filter;
            document.querySelectorAll('.pos-filter-btn').forEach(btn => {
                btn.classList.replace('bg-blue-600', 'bg-white');
                btn.classList.replace('text-white', 'text-slate-600');
                btn.classList.add('border', 'border-slate-200');
            });
            
            const activeBtn = document.getElementById('pos-filter-' + filter);
            if(activeBtn) {
                activeBtn.classList.replace('bg-white', 'bg-blue-600');
                activeBtn.classList.replace('text-slate-600', 'text-white');
                activeBtn.classList.remove('border', 'border-slate-200');
            }
            handlePosSearch();
        }

        function handlePosSearch() {
            const query = document.getElementById('pos-search').value.toLowerCase();
            const data = window.posData;
            let html = '';
            
            if (currentPosFilter === 'all' || currentPosFilter === 'os') {
                // Search OS
                const matchedOs = data.orders.filter(o => o.device?.toLowerCase().includes(query) || o.id.toLowerCase().includes(query) || o.clientName?.toLowerCase().includes(query));
                matchedOs.slice(0, currentPosFilter === 'os' ? 20 : 5).forEach(o => {
                    html += `<div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 cursor-pointer hover:border-indigo-400 hover:shadow-md transition" onclick="addPosItem('O', '${o.id}')">
                        <p class="text-xs font-bold text-indigo-600 mb-1">ORDEM DE SERVIÇO</p>
                        <p class="font-bold text-slate-800 truncate">${o.device}</p>
                        <p class="text-xs text-slate-500 mb-2 truncate">${o.clientName}</p>
                        <p class="font-black text-indigo-700">${formatCurrency(o.totalCost)}</p>
                    </div>`;
                });
            }
            
            if (currentPosFilter === 'all' || currentPosFilter === 'products') {
                // Search Products
                const matchedProds = data.products.filter(p => p.name?.toLowerCase().includes(query) || p.sku?.toLowerCase().includes(query));
                matchedProds.slice(0, currentPosFilter === 'products' ? 20 : 10).forEach(p => {
                    html += `<div class="bg-white p-4 rounded-xl border border-slate-200 cursor-pointer hover:border-blue-500 hover:shadow-md transition" onclick="addPosItem('P', '${p.id}')">
                        <p class="text-xs font-bold text-blue-600 mb-1">PRODUTO</p>
                        <p class="font-bold text-slate-800 truncate mb-1">${p.name}</p>
                        <p class="text-xs text-slate-500 mb-2">SKU: ${p.sku}</p>
                        <p class="font-black text-blue-600">${formatCurrency(p.price)}</p>
                    </div>`;
                });
            }
            
            if (currentPosFilter === 'all' || currentPosFilter === 'services') {
                // Search Services
                const matchedServs = data.services.filter(s => s.name?.toLowerCase().includes(query));
                matchedServs.slice(0, currentPosFilter === 'services' ? 20 : 10).forEach(s => {
                    html += `<div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 cursor-pointer hover:border-emerald-400 hover:shadow-md transition" onclick="addPosItem('S', '${s.id}')">
                        <p class="text-xs font-bold text-emerald-600 mb-1">SERVIÇO</p>
                        <p class="font-bold text-slate-800 truncate mb-3">${s.name}</p>
                        <p class="font-black text-emerald-700">${formatCurrency(s.price)}</p>
                    </div>`;
                });
            }
            
            if (currentPosFilter === 'all' || currentPosFilter === 'clients') {
                // Search Clients
                const matchedClients = data.clients.filter(c => c.name?.toLowerCase().includes(query) || c.document?.toLowerCase().includes(query) || c.phone?.toLowerCase().includes(query));
                matchedClients.slice(0, currentPosFilter === 'clients' ? 20 : 5).forEach(c => {
                    html += `<div class="bg-amber-50 p-4 rounded-xl border border-amber-100 cursor-pointer hover:border-amber-400 hover:shadow-md transition" onclick="setPosClient('${c.id}', '${c.name.replace(/'/g, "\\'")}')">
                        <p class="text-xs font-bold text-amber-600 mb-1">CLIENTE (Vincular)</p>
                        <p class="font-bold text-slate-800 truncate">${c.name}</p>
                        <p class="text-xs text-slate-500 truncate">${c.document || 'Sem Doc'} • ${c.phone || ''}</p>
                    </div>`;
                });
            }

            if(!html) {
                 html = `<div class="col-span-full p-8 text-center text-slate-500">Nenhum resultado encontrado para "${query}".</div>`;
            }

            document.getElementById('pos-results').innerHTML = html;
        }

        async function renderFinance() {
            document.getElementById('table-controls').classList.add('hidden');
            document.getElementById('dashboard-view').classList.add('hidden');
            document.getElementById('table-view').classList.remove('hidden');
            document.getElementById('results').innerHTML = `<div class="p-8 text-center text-slate-500 flex justify-center"><i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-500"></i><span class="ml-2">Carregando Financeiro...</span></div>`;
            lucide.createIcons();
            
            try {
                const res = await fetch('api.php?action=financeInfo');
                const data = await res.json();
                
                let html = `
                <div class="space-y-6 pt-2 pb-6 px-4">
                  <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Relatórios Gerenciais</h2>
                    <div class="flex space-x-2">
                        <button onclick="window.open('report.php?type=financeiro', '_blank')" class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium flex items-center space-x-2 hover:bg-slate-50 transition border-r">
                          <i data-lucide="file-text" class="w-4 h-4 text-emerald-600"></i>
                          <span>PDF Financeiro</span>
                        </button>
                        <button onclick="window.open('report.php?type=produtividade', '_blank')" class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium flex items-center space-x-2 hover:bg-slate-50 transition">
                          <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-600"></i>
                          <span>PDF Produtividade</span>
                        </button>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                       <h3 class="text-lg font-semibold text-slate-800 mb-2 flex items-center">
                         <i data-lucide="line-chart" class="w-5 h-5 mr-2 text-indigo-500"></i>
                         Visão Financeira (Mês Atual)
                       </h3>
                       <div class="mt-4 mb-6">
                         <div class="text-4xl font-bold text-slate-900">${formatCurrency(data.totalRevenue)}</div>
                         <p class="text-sm text-emerald-600 font-medium mt-1">+12% em relação ao mês passado</p>
                       </div>
                       <div class="h-48 w-full">
                          <canvas id="financeChart"></canvas>
                       </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                       <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                         <i data-lucide="users" class="w-5 h-5 mr-2 text-blue-500"></i>
                         Produtividade Técnica
                       </h3>
                       <div class="space-y-4 mt-2">
                         `;
                         
                if(data.productivityData && data.productivityData.length > 0) {
                    data.productivityData.forEach(tech => {
                        html += `
                        <div class="flex flex-col space-y-2 border-b border-slate-100 pb-4 last:border-0 last:pb-0">
                          <div class="flex justify-between items-center">
                            <span class="font-medium text-slate-800">${tech.name}</span>
                            <span class="font-bold text-slate-900">${formatCurrency(tech.faturamento || 0)}</span>
                          </div>
                          <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: ${Math.min(100, (tech.osResolvidas || 0) * 10)}%"></div>
                          </div>
                          <div class="text-xs text-slate-500">${tech.osResolvidas} OSs Resolvidas</div>
                        </div>`;
                    });
                } else {
                    html += `<p class="text-slate-500 text-sm">Nenhum dado de técnico disponível ainda.</p>`;
                }
                
                html += `
                       </div>
                    </div>
                  </div>
                </div>`;
                
                document.getElementById('table-view').classList.remove('p-8');
                document.getElementById('table-view').classList.add('p-0');
                document.getElementById('results').innerHTML = html;
                lucide.createIcons();
                
                // Init chart
                const ctx = document.getElementById('financeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                        datasets: [{
                            label: 'Receita',
                            data: [1200, 1900, 1500, parseFloat(data.totalRevenue)],
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { display: false, grid: { display: false } }
                        }
                    }
                });

            } catch(e) {
                document.getElementById('results').innerHTML = `<div class="p-8 text-center text-red-500 font-medium"><i data-lucide="alert-triangle" class="w-8 h-8 mx-auto mb-3"></i>Erro: \${e.message}</div>`;
                lucide.createIcons();
            }
        }

        function renderTable(action, data) {
            let html = '<table class="w-full text-left border-collapse"><thead><tr class="bg-slate-50 border-b border-slate-200">';
            
            if (action === 'clients') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Cliente</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Contato</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">E-mail</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Adicionado em</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Ações</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">${row.name}</div>
                            <div class="text-sm text-slate-500">Doc: ${row.document || '-'}</div>
                        </td>
                        <td class="px-6 py-4"><div class="flex items-center text-sm text-slate-600"><i data-lucide="phone" class="w-4 h-4 mr-2 opacity-50"></i>${row.phone || '-'}</div></td>
                        <td class="px-6 py-4"><div class="flex items-center text-sm text-slate-600"><i data-lucide="mail" class="w-4 h-4 mr-2 opacity-50"></i>${row.email || '-'}</div></td>
                        <td class="px-6 py-4 text-sm text-slate-600">${formatDate(row.createdAt)}</td>
                        <td class="px-6 py-4 text-right">
                             <button onclick="openModal('${row.id}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar"><i data-lucide="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>`;
                });
            } else if (action === 'products') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Produto</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">SKU</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Preço de Venda</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Em Estoque</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Ações</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    const statusClass = row.stock > 10 ? 'bg-emerald-100 text-emerald-800' : (row.stock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800');
                    const statusText = row.stock > 10 ? 'Em Estoque' : (row.stock > 0 ? 'Baixo Estoque' : 'Sem Estoque');
                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">${row.name}</div>
                            <div class="text-sm text-slate-500 truncate max-w-xs">${row.description || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-slate-500">${row.sku}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">${formatCurrency(row.price)}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600">${row.stock} unid.</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ${statusClass}">${statusText}</span></td>
                        <td class="px-6 py-4 text-right">
                             <button onclick="openModal('${row.id}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar"><i data-lucide="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>`;
                });
            } else if (action === 'services') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">ID</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Serviço</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Valor Padrão</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Ações</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4 text-sm font-mono text-slate-500">${row.id.split('-').pop()}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">${row.name}</td>
                        <td class="px-6 py-4 font-bold text-emerald-600">${formatCurrency(row.price)}</td>
                        <td class="px-6 py-4 text-right">
                             <button onclick="openModal('${row.id}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar"><i data-lucide="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>`;
                });
            } else if (action === 'os') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Nº OS</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Cliente / Aparelho</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Categoria</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Data Entrada</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Valor Total</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Ações</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono font-medium text-slate-900">#${row.id.split('-').pop()}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">${row.clientName || 'Cliente Desconhecido'}</div>
                            <div class="text-sm text-slate-500">${row.device}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600">${row.category || '-'}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">${formatDate(row.createdAt)}</td>
                        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(row.status)}">${getStatusLabel(row.status)}</span></td>
                        <td class="px-6 py-4 font-medium text-slate-900">${formatCurrency(row.totalCost)}</td>
                        <td class="px-6 py-4 text-right">
                             <button onclick="printOS('${row.id}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Imprimir OS"><i data-lucide="printer" class="w-4 h-4"></i></button>
                             <button onclick="openModal('${row.id}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar / Ver Detalhes"><i data-lucide="edit" class="w-4 h-4"></i></button>
                        </td>
                    </tr>`;
                });
            } else if (action === 'history') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">ID Venda</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Data</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Cliente</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Produto(s)</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Serviço(s)</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Total</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Pagamento</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Faturamento</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    let faturamentoHtml = `<div class="flex flex-col space-y-2">`;
                    if (row.totalProducts > 0) {
                        if (row.isNfceIssued === '1') {
                            faturamentoHtml += `<div class="flex space-x-1"><span class="px-2 py-1 flex-1 bg-emerald-100 text-emerald-800 text-xs rounded border border-emerald-200 text-center font-medium">NFC-e Emitida</span><button onclick="printNota('${row.id}', 'Nfce')" title="ImprimirDANFE" class="px-2 py-1 bg-slate-100 text-slate-800 text-xs rounded border border-slate-200 hover:bg-slate-200 transition font-medium"><i data-lucide="printer" class="w-3 h-3"></i></button></div>`;
                        } else {
                            faturamentoHtml += `<button onclick="emitirNota('${row.id}', 'Nfce')" class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded border border-amber-200 hover:bg-amber-200 transition font-medium">Emitir NFC-e</button>`;
                        }
                    }
                    if (row.totalServices > 0) {
                        if (row.isNfseIssued === '1') {
                            faturamentoHtml += `<div class="flex space-x-1"><span class="px-2 py-1 flex-1 bg-blue-100 text-blue-800 text-xs rounded border border-blue-200 text-center font-medium">NFS-e Emitida</span><button onclick="printNota('${row.id}', 'Nfse')" title="ImprimirNFSe" class="px-2 py-1 bg-slate-100 text-slate-800 text-xs rounded border border-slate-200 hover:bg-slate-200 transition font-medium"><i data-lucide="printer" class="w-3 h-3"></i></button></div>`;
                        } else {
                            faturamentoHtml += `<button onclick="emitirNota('${row.id}', 'Nfse')" class="px-2 py-1 bg-blue-50 text-blue-800 text-xs rounded border border-blue-200 hover:bg-blue-100 transition font-medium">Emitir NFS-e</button>`;
                        }
                    }
                    faturamentoHtml += `</div>`;

                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4 text-sm font-mono text-slate-500">${row.id.split('-').pop()}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">${formatDate(row.date)}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">${row.clientName || 'Cliente Balcão'}</td>
                        <td class="px-6 py-4 font-medium text-slate-700">${row.totalProducts > 0 ? formatCurrency(row.totalProducts) : '-'}</td>
                        <td class="px-6 py-4 font-medium text-slate-700">${row.totalServices > 0 ? formatCurrency(row.totalServices) : '-'}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">${formatCurrency(row.total)}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-600">${row.paymentMethod}</td>
                        <td class="px-6 py-4">` + faturamentoHtml + `</td>
                    </tr>`;
                });
            } else if (action === 'users') {
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Usuário</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">E-mail</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600">Perfil / Tema</th>';
                html += '<th class="px-6 py-4 text-sm font-semibold text-slate-600 text-right">Ações</th>';
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    const roleColor = row.role === 'ADMIN' ? 'bg-red-100 text-red-800' : (row.role === 'CAIXA' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800');
                    html += `<tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-800">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold border border-slate-200">${row.name.charAt(0)}</div>
                                <span>${row.name}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4"><div class="flex items-center text-sm text-slate-600"><i data-lucide="mail" class="w-4 h-4 mr-2 opacity-50"></i>${row.email}</div></td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1">
                                <span class="px-3 py-1 rounded-full text-xs font-bold flex items-center w-fit ${roleColor}"><i data-lucide="user" class="w-3 h-3 mr-1"></i>${row.role}</span>
                                <span class="text-xs text-slate-400 capitalize">Tema: ${row.theme || 'light'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end space-x-2">
                             <button onclick="openModal('${row.id}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar"><i data-lucide="edit" class="w-4 h-4"></i></button>
                             ${row.role !== 'ADMIN' ? `<button onclick="deleteUser('${row.id}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Excluir"><i data-lucide="trash" class="w-4 h-4"></i></button>` : ''}
                        </td>
                    </tr>`;
                });
            } else {
                const keys = Object.keys(data[0]);
                keys.forEach(k => html += `<th class="px-6 py-4 text-sm font-semibold text-slate-600 uppercase">${k}</th>`);
                html += '</tr></thead><tbody>';
                data.forEach(row => {
                    html += '<tr class="border-b border-slate-100 hover:bg-slate-50 transition">';
                    keys.forEach(k => html += `<td class="px-6 py-4 text-sm text-slate-800">${row[k]}</td>`);
                    html += '</tr>';
                });
            }

            html += '</tbody></table>';
            document.getElementById('results').innerHTML = html;
            lucide.createIcons();
        }

        async function emitirNota(id, tipoNf) {
            if (!confirm(`Deseja realmente emitir a ${tipoNf === 'Nfce' ? 'NFC-e' : 'NFS-e'} para a venda ${id.split('-').pop()}?`)) return;
            
            try {
                const res = await fetch(`api.php?action=issue${tipoNf}&id=${id}`);
                const response = await res.json();
                if (response.success) {
                    alert(`${tipoNf === 'Nfce' ? 'NFC-e' : 'NFS-e'} emitida com sucesso!`);
                    loadData('history');
                } else {
                    alert(`Erro ao emitir ${tipoNf}.`);
                }
            } catch (err) {
                alert(`Erro ao comunicar com o servidor: ${err.message}`);
            }
        }

        async function printOS(id) {
            try {
                const res = await fetch(`api.php?action=get_os&id=${id}`);
                const os = await res.json();
                
                const setRes = await fetch('api.php?action=get_settings');
                const settings = await setRes.json();
                
                let companyHtml = '';
                if (settings.name) {
                    companyHtml = `<div style="display:flex; align-items:center; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 20px;">
                        ${settings.logo ? `<img src="${settings.logo}" style="max-height:80px; max-width: 150px; margin-right: 20px;" />` : ''}
                        <div>
                            <h2 style="margin:0; border:none; padding:0;">${settings.name}</h2>
                            <p style="margin:5px 0 0; font-size:14px; color:#555;">
                                ${settings.address ? settings.address + '<br>' : ''}
                                ${settings.phone ? 'Tel: ' + settings.phone : ''} ${settings.email ? ' | E-mail: ' + settings.email : ''}
                            </p>
                        </div>
                    </div>`;
                }

                let clientHtml = '<p><strong>Cliente:</strong> Não informado</p>';
                if (os.clientId) {
                    const resC = await fetch(`api.php?action=get_client&id=${os.clientId}`);
                    const client = await resC.json();
                    if (client) {
                        clientHtml = `<p><strong>Cliente:</strong> ${client.name} | <strong>Documento:</strong> ${client.document || '-'} | <strong>Telefone:</strong> ${client.phone || '-'}</p>`;
                    }
                }

                let itemsHtml = '<table style="width:100%;border-collapse:collapse;margin-top:20px;margin-bottom:20px;"><thead><tr><th style="border:1px solid #ccc;padding:8px;text-align:left;">Item</th><th style="border:1px solid #ccc;padding:8px;text-align:right;">Preço</th></tr></thead><tbody>';
                if (os.items && os.items.length > 0) {
                    os.items.forEach(item => {
                        itemsHtml += `<tr><td style="border:1px solid #ccc;padding:8px;">${item.name} (${item.type})</td><td style="border:1px solid #ccc;padding:8px;text-align:right;">R$ ${Number(item.price).toFixed(2).replace('.', ',')}</td></tr>`;
                    });
                } else {
                    itemsHtml += `<tr><td colspan="2" style="border:1px solid #ccc;padding:8px;text-align:center;">Nenhum item</td></tr>`;
                }
                itemsHtml += '</tbody></table>';

                const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const publicUrl = `${baseUrl}/public_os.php?id=${id}`;
                const qrCodeImg = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(publicUrl)}`;

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`<html><head><title>Imprimir OS - ${id}</title><style>body{font-family:sans-serif;padding:40px;color:#333;line-height:1.5;}h2{margin-bottom:0;padding-bottom:10px;border-bottom:2px solid #eee;}</style></head><body>
                    ${companyHtml}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <h2 style="border:none;">Ordem de Serviço: ${id}</h2>
                            <br>
                            ${clientHtml}
                        </div>
                        <div style="text-align:center;">
                            <img src="${qrCodeImg}" alt="QR Code" style="width:120px;height:120px;" />
                            <div style="font-size:11px;color:#666;margin-top:5px;">Acompanhe online</div>
                        </div>
                    </div>
                    <p><strong>Aparelho:</strong> ${os.device || '-'}</p>
                    <hr style="border:0;border-top:1px solid #eee;margin:20px 0;">
                    <p><strong>Problema Relatado:</strong> ${os.issue || '-'}</p>
                    <p><strong>Solução do Problema:</strong> ${os.solution || '-'}</p>
                    <hr style="border:0;border-top:1px solid #eee;margin:20px 0;">
                    <h3>Itens da OS</h3>
                    ${itemsHtml}
                    <h3 style="text-align:right;">Total: R$ ${Number(os.totalCost).toFixed(2).replace('.', ',')}</h3>
                    
                    <div style="margin-top:80px;text-align:center;">
                        <div style="border-top:1px solid #333;display:inline-block;width:300px;padding-top:10px;">
                            Assinatura do Cliente
                        </div>
                    </div>
                    <script>setTimeout(()=>window.print(), 1000);<\/script>
                </body></html>`);
                printWindow.document.close();
            } catch (err) {
                alert(`Erro ao carregar OS para impressão: ${err.message}`);
            }
        }

        function printNota(id, tipoNf) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`<html><head><title>Imprimir ${tipoNf === 'Nfce' ? 'DANFE NFC-e' : 'NFS-e'} - ${id}</title><style>body{font-family:sans-serif;padding:20px;}</style></head><body><h2>${tipoNf === 'Nfce' ? 'DANFE NFC-e' : 'NFS-e'} - Venda: ${id}</h2><p>Documento auxiliar em desenvolvimento.</p><script>window.print();<\/script></body></html>`);
            printWindow.document.close();
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        async function performBackup() {
            try {
                const res = await fetch('api.php?action=backup_db');
                const data = await res.json();
                if (data.success) {
                    alert(data.message + '\nArquivo: ' + data.file);
                    window.open(data.file, '_blank');
                } else {
                    alert('Erro ao gerar backup: ' + (data.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                alert('Erro ao gerar backup: ' + err.message);
            }
        }

        async function renderSettings() {
             document.getElementById('dashboard-view').innerHTML = `<div class="p-8 text-center text-slate-500 flex justify-center"><i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-500"></i><span class="ml-2">Carregando Configurações...</span></div>`;
             lucide.createIcons();
             try {
                 const res = await fetch('api.php?action=get_settings');
                 let s = await res.json();
                 let html = `
                 <div class="max-w-4xl mx-auto space-y-6">
                     <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                         <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                             <h3 class="font-bold text-slate-800 text-lg flex items-center"><i data-lucide="building-2" class="w-5 h-5 mr-2 text-blue-600"></i> Dados da Empresa</h3>
                         </div>
                         <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div><label class="block text-sm font-medium text-slate-700 mb-1">Razão Social / Nome</label><input type="text" id="set-name" value="${s.name || ''}" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                             <div><label class="block text-sm font-medium text-slate-700 mb-1">CNPJ</label><input type="text" id="set-cnpj" value="${s.cnpj || ''}" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                             <div><label class="block text-sm font-medium text-slate-700 mb-1">Telefone</label><input type="text" id="set-phone" value="${s.phone || ''}" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                             <div><label class="block text-sm font-medium text-slate-700 mb-1">E-mail</label><input type="text" id="set-email" value="${s.email || ''}" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                             <div class="md:col-span-2">
                                 <label class="block text-sm font-medium text-slate-700 mb-1">Logo da Empresa</label>
                                 <div class="flex items-center space-x-4">
                                     ${s.logo ? `<img src="${s.logo}" alt="Logo" class="h-16 w-16 object-contain rounded border border-slate-200">` : ''}
                                     <input type="file" id="set-logo" accept="image/*" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 outline-none">
                                 </div>
                             </div>
                             <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-700 mb-1">Endereço Completo</label><input type="text" id="set-address" value="${s.address || ''}" class="w-full px-4 py-2 border border-slate-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500"></div>
                         </div>
                     </div>

                     <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                         <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                             <h3 class="font-bold text-slate-800 text-lg flex items-center"><i data-lucide="database" class="w-5 h-5 mr-2 text-blue-600"></i> Sistema & Backup</h3>
                         </div>
                         <div class="p-6">
                            <p class="text-sm text-slate-500 mb-4">Mantenha seus dados seguros exportando periodicamente o banco de dados.</p>
                            <button onclick="performBackup()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center inline-flex mb-6">
                                <i data-lucide="download-cloud" class="w-4 h-4 mr-2"></i> Criar Backup do Banco de Dados
                            </button>
                            
                            <div class="mb-6 p-4 border border-slate-200 rounded-lg bg-slate-50 flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Fazer Upload de Backup (.sql)</label>
                                    <input type="file" id="upload-backup-file" accept=".sql" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 outline-none w-full">
                                </div>
                                <button onclick="uploadBackup()" class="bg-slate-800 text-white px-4 py-2 rounded-lg font-medium hover:bg-slate-900 transition mt-2 md:mt-6 w-full md:w-auto">
                                    Enviar Arquivo
                                </button>
                            </div>

                            <h4 class="font-semibold text-slate-700 mb-2">Últimos Backups</h4>
                            <div id="backup-list" class="space-y-2">
                                <div class="text-sm text-slate-500 flex items-center"><i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Caregando...</div>
                            </div>
                            
                            <div class="mt-6 border-t border-slate-200 pt-6">
                                <h4 class="font-semibold text-slate-700 mb-2 flex items-center"><i data-lucide="github" class="w-4 h-4 mr-2"></i> Atualização do Sistema</h4>
                                <p class="text-sm text-slate-500 mb-4">Verifique se há novas versões do sistema disponíveis no GitHub e atualize os arquivos automaticamente.</p>
                                <div class="flex items-center space-x-4">
                                    <button onclick="checkUpdates()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition flex items-center" id="btn-check-updates">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2" id="update-spinner"></i> <span>Verificar Atualizações</span>
                                    </button>
                                    <button onclick="applyUpdate()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-emerald-700 transition flex items-center hidden" id="btn-apply-update">
                                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> <span>Aplicar Atualização</span>
                                    </button>
                                </div>
                                <div id="update-status" class="mt-3 text-sm font-medium text-slate-600"></div>
                            </div>
                         </div>
                     </div>
                     
                     <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                         <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                             <h3 class="font-bold text-slate-800 text-lg flex items-center"><i data-lucide="file-text" class="w-5 h-5 mr-2 text-emerald-600"></i> Emissão de Notas Fiscais</h3>
                         </div>
                         <div class="p-6 space-y-6">
                             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                 <label class="flex items-center space-x-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                     <input type="checkbox" id="set-emit-nfe" ${s.emit_nfe === '1' ? 'checked' : ''} class="w-5 h-5 text-blue-600 rounded">
                                     <span class="font-medium text-slate-700">Emitir NF-e</span>
                                 </label>
                                 <label class="flex items-center space-x-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                     <input type="checkbox" id="set-emit-nfse" ${s.emit_nfse === '1' ? 'checked' : ''} class="w-5 h-5 text-blue-600 rounded">
                                     <span class="font-medium text-slate-700">Emitir NFS-e (Asten)</span>
                                 </label>
                                 <label class="flex items-center space-x-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                     <input type="checkbox" id="set-emit-danfe" ${s.emit_danfe === '1' ? 'checked' : ''} class="w-5 h-5 text-blue-600 rounded">
                                     <span class="font-medium text-slate-700">Emitir DANFE</span>
                                 </label>
                             </div>
                             
                             <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-4">
                                 <h4 class="font-bold text-slate-700 text-sm">Configuração NFS-e (Sistema Asten)</h4>
                                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Usuário / Token Asten</label><input type="text" id="set-asten-user" value="${s.asten_user || ''}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Senha Asten</label><input type="password" id="set-asten-pass" value="${s.asten_pass || ''}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Ambiente</label>
                                         <select id="set-asten-env" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                             <option value="homologacao" ${s.asten_env === 'homologacao' ? 'selected' : ''}>Homologação</option>
                                             <option value="producao" ${s.asten_env === 'producao' ? 'selected' : ''}>Produção</option>
                                         </select>
                                     </div>
                                 </div>
                             </div>

                             <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-4">
                                 <h4 class="font-bold text-slate-700 text-sm">Configuração NF-e / NFC-e / DANFE</h4>
                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Regime Tributário</label>
                                         <select id="set-regime-tributario" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm">
                                             <option value="Simples Nacional" ${s.regime_tributario === 'Simples Nacional' ? 'selected' : ''}>Simples Nacional</option>
                                             <option value="Lucro Presumido" ${s.regime_tributario === 'Lucro Presumido' ? 'selected' : ''}>Lucro Presumido</option>
                                             <option value="Lucro Real" ${s.regime_tributario === 'Lucro Real' ? 'selected' : ''}>Lucro Real</option>
                                         </select>
                                     </div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Senha do Certificado Digital A1</label><input type="password" id="set-cert-password" value="${s.cert_password || ''}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div class="md:col-span-2"><label class="block text-xs font-medium text-slate-600 mb-1">Certificado Digital (Arquivo .pfx / .p12)</label><input type="file" id="set-cert-file" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm bg-white">
                                     <small class="text-slate-500 mt-1 block">Certificado atual: ${s.cert_file ? s.cert_file : 'Nenhum certificado carregado'}</small></div>
                                 </div>
                                 <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Série NF-e</label><input type="text" id="set-nfe-serie" value="${s.nfe_serie || '1'}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Número NF-e</label><input type="number" id="set-nfe-numero" value="${s.nfe_numero || '1'}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Série NFC-e</label><input type="text" id="set-nfce-serie" value="${s.nfce_serie || '1'}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">Número NFC-e</label><input type="number" id="set-nfce-numero" value="${s.nfce_numero || '1'}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                 </div>
                                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">CSC ID (Token NFC-e)</label><input type="text" id="set-csc-id" value="${s.csc_id || ''}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                     <div><label class="block text-xs font-medium text-slate-600 mb-1">CSC Código (Alfanumérico)</label><input type="text" id="set-csc-token" value="${s.csc_token || ''}" class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:ring-1 focus:ring-blue-500 text-sm"></div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="flex justify-end">
                         <button onclick="saveSettings()" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition flex items-center shadow-lg"><i data-lucide="save" class="w-5 h-5 mr-2"></i> Salvar Configurações</button>
                     </div>
                 </div>`;
                 document.getElementById('dashboard-view').innerHTML = html;
                 
                 try {
                     const bRes = await fetch('api.php?action=list_backups');
                     const backups = await bRes.json();
                     let bHtml = '';
                     if (backups.length === 0) {
                         bHtml = '<div class="text-sm text-slate-500 py-2">Nenhum backup encontrado.</div>';
                     } else {
                         backups.forEach(b => {
                             bHtml += `
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 gap-2">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-800">${b.filename}</span>
                                        <span class="text-xs text-slate-500">${b.date} &bull; ${(b.size / 1024).toFixed(1)} KB</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="backups/${b.filename}" download class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 flex items-center justify-center px-3 py-1 rounded font-medium transition cursor-pointer">
                                            <i data-lucide="download" class="w-3 h-3 mr-1"></i> Baixar
                                        </a>
                                        <button onclick="restoreBackup('${b.filename}')" class="text-xs bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 px-3 py-1 rounded font-medium transition cursor-pointer flex items-center justify-center">
                                            Restaurar
                                        </button>
                                        <button onclick="deleteBackup('${b.filename}')" class="text-xs bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 px-3 py-1 rounded font-medium transition cursor-pointer flex items-center justify-center">
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                             `;
                         });
                     }
                     document.getElementById('backup-list').innerHTML = bHtml;
                 } catch (errBackup) {
                     document.getElementById('backup-list').innerHTML = '<div class="text-xs text-red-500 flex items-center"><i data-lucide="alert-triangle" class="w-4 h-4 mr-1"></i> Erro ao carregar</div>';
                 }

                 lucide.createIcons();
             } catch (e) {
                 document.getElementById('dashboard-view').innerHTML = `<div class="p-8 text-center text-red-500">Erro ao carregar configurações: ${e.message}</div>`;
             }
        }

        async function restoreBackup(filename) {
             if (confirm(`Deseja realmente restaurar o backup '${filename}'? Isso substituirá todos os dados atuais e não pode ser desfeito.`)) {
                 try {
                     const res = await fetch('api.php?action=restore_backup', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify({ filename })
                     });
                     const data = await res.json();
                     if (data.success) {
                         alert('Backup restaurado com sucesso! A página será recarregada.');
                         window.location.reload();
                     } else {
                         alert('Erro ao restaurar: ' + (data.error || 'Erro desconhecido.'));
                     }
                 } catch (err) {
                     alert('Erro ao restaurar backup: ' + err.message);
                 }
             }
        }

        async function deleteBackup(filename) {
            if (confirm(`Deseja realmente excluir o backup '${filename}'? Esta ação não pode ser desfeita.`)) {
                try {
                    const res = await fetch('api.php?action=delete_backup', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ filename })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Backup excluído com sucesso.');
                        renderSettings(); // recarregar lista
                    } else {
                        alert('Erro ao excluir: ' + (data.error || 'Erro desconhecido.'));
                    }
                } catch (err) {
                    alert('Erro ao excluir backup: ' + err.message);
                }
            }
        }

        async function deleteUser(id) {
            if (confirm('Deseja realmente excluir este usuário? Esta ação não pode ser desfeita.')) {
                try {
                    const res = await fetch('api.php?action=delete_user', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        loadData('users');
                    } else {
                        alert('Erro ao excluir usuário: ' + (data.error || 'Erro desconhecido.'));
                    }
                } catch(e) {
                    alert('Erro ao excluir: ' + e.message);
                }
            }
        }

        async function uploadBackup() {
            const fileInput = document.getElementById('upload-backup-file');
            if (!fileInput.files.length) {
                alert('Por favor, selecione um arquivo .sql para enviar.');
                return;
            }
            
            const file = fileInput.files[0];
            if (!file.name.toLowerCase().endsWith('.sql')) {
                alert('Apenas arquivos com extensão .sql são permitidos.');
                return;
            }

            const formData = new FormData();
            formData.append('backup_file', file);

            try {
                const res = await fetch('api.php?action=upload_backup', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    fileInput.value = ''; // clean input
                    renderSettings(); // reload backups
                } else {
                    alert('Erro no envio: ' + (data.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                alert('Erro ao enviar backup: ' + err.message);
            }
        }

        async function saveSettings() {
             const certFile = document.getElementById('set-cert-file').files[0];
             let certName = '';
             if (certFile) certName = certFile.name; // In a full implementation, you'd upload this file via FormData. Using name as a placeholder for now.

             const logoFile = document.getElementById('set-logo')?.files[0];
             let logoBase64 = null;
             if (logoFile) {
                 logoBase64 = await new Promise(resolve => {
                     const reader = new FileReader();
                     reader.onload = e => resolve(e.target.result);
                     reader.readAsDataURL(logoFile);
                 });
             }

             const data = {
                 name: document.getElementById('set-name').value,
                 cnpj: document.getElementById('set-cnpj').value,
                 phone: document.getElementById('set-phone').value,
                 email: document.getElementById('set-email').value,
                 address: document.getElementById('set-address').value,
                 logo: logoBase64,
                 emit_nfe: document.getElementById('set-emit-nfe').checked ? '1' : '0',
                 emit_nfse: document.getElementById('set-emit-nfse').checked ? '1' : '0',
                 emit_danfe: document.getElementById('set-emit-danfe').checked ? '1' : '0',
                 asten_user: document.getElementById('set-asten-user').value,
                 asten_pass: document.getElementById('set-asten-pass').value,
                 asten_env: document.getElementById('set-asten-env').value,
                 regime_tributario: document.getElementById('set-regime-tributario').value,
                 cert_password: document.getElementById('set-cert-password').value,
                 cert_file: certName, // Sending just the name for prototype purposes
                 nfe_serie: document.getElementById('set-nfe-serie').value,
                 nfe_numero: document.getElementById('set-nfe-numero').value,
                 nfce_serie: document.getElementById('set-nfce-serie').value,
                 nfce_numero: document.getElementById('set-nfce-numero').value,
                 csc_id: document.getElementById('set-csc-id').value,
                 csc_token: document.getElementById('set-csc-token').value
             };
             
             try {
                 const res = await fetch('api.php?action=save_settings', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json' },
                     body: JSON.stringify(data)
                 });
                 if (res.ok) {
                     const resData = await res.json();
                     if (resData.success) {
                         alert('Configurações salvas com sucesso!');
                     } else {
                         alert('Erro ao salvar as configurações: ' + (resData.error || 'Desconhecido'));
                     }
                 } else {
                     alert('Erro ao salvar configurações.');
                 }
             } catch (e) {
                 alert('Erro de rede: ' + e.message);
             }
        }

        async function checkUpdates() {
            const btn = document.getElementById('btn-check-updates');
            const spinner = document.getElementById('update-spinner');
            const status = document.getElementById('update-status');
            const applyBtn = document.getElementById('btn-apply-update');
            
            spinner.classList.add('animate-spin');
            btn.disabled = true;
            status.innerHTML = '<span class="text-blue-600">Buscando atualizações no GitHub...</span>';
            applyBtn.classList.add('hidden');
            
            try {
                const res = await fetch('update.php?action=check');
                const data = await res.json();
                if (data.update_available) {
                     status.innerHTML = `<span class="text-emerald-600 font-bold">Atualização encontrada!</span> <span class="text-slate-500">Versão: ${data.latest_version.substring(0, 7)}</span>`;
                     applyBtn.classList.remove('hidden');
                } else {
                     status.innerHTML = '<span class="text-slate-600">O sistema já está na versão mais recente.</span>';
                }
            } catch(e) {
                status.innerHTML = `<span class="text-red-500">Erro ao verificar atualizações: ${e.message}</span>`;
            } finally {
                spinner.classList.remove('animate-spin');
                btn.disabled = false;
            }
        }

        async function applyUpdate() {
            if(!confirm('Tem certeza que deseja atualizar o sistema? Recomendamos fazer um backup antes.')) return;
            
            const btn = document.getElementById('btn-apply-update');
            const status = document.getElementById('update-status');
            
            btn.disabled = true;
            status.innerHTML = '<span class="text-blue-600 flex items-center"><i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Baixando e aplicando atualização, não feche esta página...</span>';
            lucide.createIcons();
            
            try {
                const res = await fetch('update.php?action=apply');
                const data = await res.json();
                if (data.success) {
                     status.innerHTML = '<span class="text-emerald-600 font-bold flex items-center"><i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Sistema atualizado com sucesso! Recarregando...</span>';
                     lucide.createIcons();
                     setTimeout(() => window.location.reload(), 2000);
                } else {
                     status.innerHTML = `<span class="text-red-500 font-bold">Erro na atualização:</span> ${data.error}`;
                     btn.disabled = false;
                }
            } catch(e) {
                status.innerHTML = `<span class="text-red-500 font-bold">Erro de conexão:</span> ${e.message}`;
                btn.disabled = false;
            }
        }

        function renderDashboard(data) {
            let html = `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg"><i data-lucide="dollar-sign" class="w-6 h-6"></i></div>
                    <div><p class="text-sm font-medium text-slate-500">Receitas (Mês)</p><h3 class="text-2xl font-bold text-slate-800">R$ ${parseFloat(data.totalReceitas).toFixed(2)}</h3></div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="clipboard-list" class="w-6 h-6"></i></div>
                    <div><p class="text-sm font-medium text-slate-500">OS em Aberto</p><h3 class="text-2xl font-bold text-slate-800">${data.osAbertas}</h3></div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 text-purple-600 rounded-lg"><i data-lucide="users" class="w-6 h-6"></i></div>
                    <div><p class="text-sm font-medium text-slate-500">Clientes Ativos</p><h3 class="text-2xl font-bold text-slate-800">${data.totalClientes}</h3></div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-4">
                    <div class="p-3 bg-orange-100 text-orange-600 rounded-lg"><i data-lucide="clock" class="w-6 h-6"></i></div>
                    <div><p class="text-sm font-medium text-slate-500">Aguardando Avaliação</p><h3 class="text-2xl font-bold text-slate-800">${data.osAguardandoAprovacao}</h3></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-6">Receita de Serviços e Vendas</h3>
                    <div class="h-72 w-full">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center"><i data-lucide="bell" class="w-5 h-5 mr-2 text-indigo-500"></i> Próximos Alertas</h3>
                    </div>
                    <div class="divide-y divide-slate-100 flex-1 overflow-y-auto">
            `;
            
            if (data.alertas && data.alertas.length > 0) {
                data.alertas.forEach(alerta => {
                    html += `
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition">
                             <div class="flex items-center space-x-4">
                                  <div class="p-2 bg-red-100 text-red-600 rounded-full"><i data-lucide="alert-circle" class="w-4 h-4"></i></div>
                                  <div>
                                      <p class="text-sm font-medium text-slate-800">OS #${alerta.id.split('-').pop()} - ${alerta.clientName || 'Cliente Desconhecido'}</p>
                                      <p class="text-xs text-slate-500">${alerta.device}</p>
                                  </div>
                             </div>
                             <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(alerta.status)}">${getStatusLabel(alerta.status)}</span>
                        </div>
                    `;
                });
            } else {
                html += `<div class="px-6 py-8 text-center text-slate-500 text-sm">Nenhum alerta no momento.</div>`;
            }
            
            html += `</div></div></div>`;
            
            // D3 Services Chart container
            if (data.commonServices && data.commonServices.length > 0) {
                html += `
                <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 mb-6 flex items-center"><i data-lucide="pie-chart" class="w-5 h-5 mr-2 text-indigo-500"></i> Serviços Mais Requisitados</h3>
                    <div id="d3-services-chart" class="w-full h-72 flex justify-center items-center"></div>
                </div>
                `;
            }
            
            document.getElementById('dashboard-view').innerHTML = html;
            lucide.createIcons();
            
            // Init chart
            const ctxDash = document.getElementById('dashboardChart');
            if (ctxDash) {
                new Chart(ctxDash.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Receita',
                            data: [4000, 3000, 5000, 2000, 4500, parseFloat(data.totalReceitas)],
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { grid: { display: false }, border: { display: false } },
                            y: { border: { display: false } }
                        }
                    }
                });
            }
            
            // Render D3 chart
            if (data.commonServices && data.commonServices.length > 0) {
                renderD3ServicesChart(data.commonServices);
            }
        }
        
        function renderD3ServicesChart(services) {
            const container = document.getElementById('d3-services-chart');
            if (!container) return;
            container.innerHTML = '';
            
            const width = container.clientWidth || 500;
            const height = 280;
            const margin = {top: 20, right: 20, bottom: 30, left: 40};
            
            const x = d3.scaleBand()
                .domain(services.map(d => d.name))
                .range([margin.left, width - margin.right])
                .padding(0.1);
            
            const y = d3.scaleLinear()
                .domain([0, d3.max(services, d => parseInt(d.amount))]).nice()
                .range([height - margin.bottom, margin.top]);
                
            const svg = d3.select('#d3-services-chart')
                .append('svg')
                .attr('width', width)
                .attr('height', height);
                
            svg.append('g')
                .attr('fill', '#4f46e5')
                .selectAll('rect')
                .data(services)
                .join('rect')
                .attr('x', d => x(d.name))
                .attr('y', d => y(parseInt(d.amount)))
                .attr('height', d => y(0) - y(parseInt(d.amount)))
                .attr('width', x.bandwidth())
                .attr('rx', 4);
                
            svg.append('g')
                .attr('transform', `translate(0,${height - margin.bottom})`)
                .call(d3.axisBottom(x).tickSizeOuter(0))
                .selectAll("text")  
                .style("text-anchor", "middle")
                .style("fill", "#64748b")
                .style("font-family", "Inter, sans-serif");
                
            svg.append('g')
                .attr('transform', `translate(${margin.left},0)`)
                .call(d3.axisLeft(y).ticks(5))
                .selectAll("text")
                .style("fill", "#64748b")
                .style("font-family", "Inter, sans-serif");
                
            svg.selectAll('.domain, .tick line').attr('stroke', '#e2e8f0');
        }
        
        loadData('dashboard');
    </script>
</body>
</html>
