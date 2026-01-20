<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>🏫 Gestão de Escolas</h1>
    <p>Cadastre e gerencie as unidades escolares do sistema.</p>
</div>

<div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e2e8f0;">
    <h3 style="margin-bottom: 20px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Cadastrar Nova Escola</h3>
    <form action="<?= url('admin/school/store') ?>" method="POST" class="modern-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group">
            <label class="modern-label">Nome da Escola</label>
            <input type="text" name="name" required class="modern-input" placeholder="Ex: Escola Municipal João de Barro">
        </div>

        <div class="form-group">
            <label class="modern-label">Localidade (Endereço)</label>
            <input type="text" name="address" class="modern-input" placeholder="Ex: Rua das Flores, 123 - Centro">
        </div>

        <div class="form-group">
            <label class="modern-label">Código INEP (Opcional)</label>
            <input type="text" name="inep_code" class="modern-input" placeholder="Ex: 12345678">
        </div>

        <div class="form-group">
            <label class="modern-label">Nome do Diretor(a)</label>
            <input type="text" name="director_name" class="modern-input" placeholder="Ex: Maria Souza">
        </div>

        <div class="form-group">
            <label class="modern-label">Telefone do Diretor(a)</label>
            <input type="text" name="director_phone" class="modern-input" placeholder="Ex: (11) 99999-9999">
        </div>
        
        <div class="form-group" style="grid-column: 1 / -1; margin-top: 10px;">
            <button type="submit" class="modern-btn">
                <i class="fas fa-plus-circle"></i> Cadastrar Escola
            </button>
        </div>
    </form>
    
    <style>
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
        .modern-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }
    </style>
</div>

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Escolas Cadastradas</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($schools as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td>
                        <a href="<?= url('admin/school/edit?id='.$s['id']) ?>" class="btn-icon" style="color: #f59e0b; margin-right: 10px;" title="Editar"><i class="fas fa-edit"></i></a>
                        <a href="<?= url('admin/school/delete?id='.$s['id']) ?>" class="btn-icon" style="color: red;" title="Excluir" onclick="return confirm('ATENÇÃO: Excluir esta escola pode causar perda de dados vinculados. Deseja continuar?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
