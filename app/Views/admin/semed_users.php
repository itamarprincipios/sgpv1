<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🏢 Gestão de Usuários SEMED</h1>
    <p>Visualize e gerencie os técnicos e administradores da SEMED.</p>
</div>

<!-- Tip Block -->
<div style="background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; padding: 15px; border-radius: 4px; margin-bottom: 30px;">
    <p style="margin: 0;"><i class="fas fa-info-circle"></i> Usuários SEMED podem estar vinculados a múltiplas escolas ou ter visão global.</p>
</div>

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

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Usuários SEMED Cadastrados</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escolas Vinculadas</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($semedUsers)): ?>
                <tr><td colspan="3">Nenhum usuário SEMED encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($semedUsers as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td>
                            <?php 
                                echo htmlspecialchars($u['school_name'] ?? '-');
                            ?>
                        </td>
                        <td>
                            <a href="<?= url('admin/user/edit?id='.$u['id']) ?>" class="btn-icon" style="color: #3b82f6; margin-right: 10px;" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="<?= url('admin/user/reset-password?id='.$u['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('admin/user/delete?id='.$u['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
