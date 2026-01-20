<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px 15px !important;
        }
        
        .dashboard-header h1 {
            font-size: 1.5rem !important;
        }
        
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .tabs {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .tab-btn {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        /* Formulários responsivos */
        form[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        
        form button[type="submit"] {
            width: 100%;
        }
    }
</style>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🛡️ Painel do Super Admin</h1>
    <p>Controle total do sistema</p>
</div>


<!-- Stats -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #2563eb;">
        <h3>Escolas</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['schools'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #16a34a;">
        <h3>SEMED</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['semed'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #9333ea;">
        <h3>Coordenadores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['coordinators'] ?></div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-left: 5px solid #ca8a04;">
        <h3>Professores</h3>
        <div style="font-size: 2rem; font-weight: bold;"><?= $stats['professors'] ?></div>
    </div>
</div>

<!-- Tab buttons removed -->

<!-- TAB SEMED -->
<div id="tab-semed" class="tab-content active">
    <style>
        .semed-form-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .semed-form-title {
            color: #1e293b;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .modern-form-grid {
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px;
        }
        .modern-label {
            display: block;
            margin-bottom: 8px;
            color: #64748b;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .modern-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            color: #334155;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .modern-input:focus {
            border-color: #3b82f6;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .modern-select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #334155;
            font-family: inherit;
        }
        .modern-select:focus {
            border-color: #3b82f6;
            outline: none;
        }
        .modern-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        .input-hint {
            display: block;
            margin-top: 6px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        @media (max-width: 768px) {
            .modern-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <h3>Gestão SEMED</h3>
    
    <div class="semed-form-card">
        <div class="semed-form-title">
            <i class="fas fa-user-plus" style="margin-right: 10px; color: #3b82f6;"></i> Cadastrar Novo Usuário SEMED
        </div>
        
        <form action="<?= url('admin/user/store') ?>" method="POST" class="modern-form-grid">
            <input type="hidden" name="role" value="semed">
            
            <div class="form-group">
                <label class="modern-label">Nome Completo</label>
                <input type="text" name="name" required class="modern-input" placeholder="Ex: João da Silva">
            </div>
            
            <div class="form-group">
                <label class="modern-label">Email (Login)</label>
                <input type="email" name="email" required class="modern-input" placeholder="email@exemplo.com">
            </div>

            <div class="form-group">
                <label class="modern-label"><i class="fab fa-whatsapp" style="color: #25D366;"></i> WhatsApp</label>
                <input type="text" name="whatsapp" class="modern-input" placeholder="Ex: 5511999999999">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label class="modern-label">Escolas Vinculadas</label>
                
                <div class="school-selector-container" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                    <div style="display: flex; gap: 10px; margin-bottom: 0;">
                        <div id="schools-inputs-container"></div> <!-- Dynamic inputs will be appended here -->
                        
                        <!-- Visible Dropdown acting as "Add Button" -->
                        <div style="position: relative; flex-grow: 1;">
                            <select id="school-select-source" class="modern-select" style="width: 100%;">
                                <option value="" disabled selected>+ Adicionar Escola...</option>
                                <?php foreach($schools as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-name="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" id="btn-add-school" class="modern-btn" style="width: auto; padding: 0 20px; background: #22c55e;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <!-- Selected Schools List -->
                    <div id="selected-schools-list" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; min-height: 40px;">
                        <span style="color: #94a3b8; font-style: italic; font-size: 0.9rem; align-self: center;" id="no-schools-msg">Nenhuma escola vinculada.</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;">
                <button type="submit" class="modern-btn">
                    <i class="fas fa-check-circle"></i> Cadastrar Usuário
                </button>
            </div>
        </form>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sourceSelect = document.getElementById('school-select-source');
            const addBtn = document.getElementById('btn-add-school');
            const listContainer = document.getElementById('selected-schools-list');
            const form = document.querySelector('form.modern-form-grid');
            
            // Set for tracking selected IDs to prevent duplicates
            const selectedIds = new Set();
            
            function addSchool(id, name) {
                if(!id || selectedIds.has(id)) return;
                
                selectedIds.add(id);
                document.getElementById('no-schools-msg').style.display = 'none';
                
                // Hide option from dropdown
                const option = sourceSelect.querySelector(`option[value="${id}"]`);
                if(option) option.hidden = true;
                
                const item = document.createElement('div');
                item.className = 'selected-school-item';
                item.style.cssText = 'background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; animation: fadeIn 0.3s;';
                item.innerHTML = `
                    <span>${name}</span>
                    <button type="button" style="background: none; border: none; color: #e11d48; cursor: pointer; font-size: 1.1rem; padding: 0;" onclick="removeSchool(this, '${id}')">&times;</button>
                    <input type="hidden" name="schools[]" value="${id}">
                `;
                
                listContainer.appendChild(item);
                sourceSelect.value = ""; // Reset dropdown
            }
            
            window.removeSchool = function(btn, id) {
                btn.parentElement.remove();
                selectedIds.delete(id);
                
                // Show option back in dropdown
                const option = sourceSelect.querySelector(`option[value="${id}"]`);
                if(option) option.hidden = false;
                
                if(selectedIds.size === 0) {
                    document.getElementById('no-schools-msg').style.display = 'block';
                }
            };
            
            // Add on click
            addBtn.addEventListener('click', () => {
                const id = sourceSelect.value;
                if(id) {
                    const option = sourceSelect.options[sourceSelect.selectedIndex];
                    const name = option.getAttribute('data-name');
                    addSchool(id, name);
                }
            });
            
            // Add on change (optional, better UX implies clicking Add or just picking)
            sourceSelect.addEventListener('change', () => {
                // Uncomment below if you want auto-add on select
                // const id = sourceSelect.value;
                // const option = sourceSelect.options[sourceSelect.selectedIndex];
                // addSchool(id, option.getAttribute('data-name'));
            });
        });
    </script>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($semedUsers as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="<?= url('admin/user/reset-password?id='.$u['id']) ?>" class="btn-icon" title="Resetar Senha (123456)" onclick="return confirm('Resetar senha para 123456?')"><i class="fas fa-key"></i></a>
                        <a href="<?= url('admin/user/edit?id='.$u['id']) ?>" class="btn-icon" title="Editar Dados/Escolas" style="color: #3b82f6;"><i class="fas fa-edit"></i></a>
                        <a href="<?= url('admin/user/delete?id='.$u['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('Excluir este usuário?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // No tabs needed anymore
</script>

<div class="card">
    <h3>Configurações do Sistema</h3>
    <p>Ações avançadas de manutenção.</p>
    <form action="<?= url('admin/reset-year') ?>" method="POST" onsubmit="return confirm('ATENÇÃO: Isso irá desvincular TODAS as turmas dos professores para iniciar um novo ano letivo. O histórico de planejamentos será mantido. Tem certeza absoluta?');">
        <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-exclamation-triangle"></i> Iniciar Novo Ano Letivo</button>
    </form>
    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
        <a href="<?= url('admin/reports') ?>" class="btn btn-primary btn-block">Ver Relatórios</a>
        <a href="<?= url('admin/history') ?>" class="btn btn-secondary btn-block">Banco de Planejamentos</a>
    </div>
</div>

<!-- IANNE AI Widget -->
<?php require __DIR__ . '/../partials/superadmin_ai_widget.php'; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
