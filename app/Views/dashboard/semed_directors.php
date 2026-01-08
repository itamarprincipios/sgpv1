<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    .director-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .director-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .director-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    
    .dir-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .dir-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #34d399); /* Green for Directors */
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
    }

    .dir-info h4 {
        margin: 0;
        font-size: 1.1rem;
        color: #1f2937;
    }

    .dir-info span {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .schools-list {
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    
    .schools-label {
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 8px;
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: #f9fafb;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .btn-whatsapp { color: #25D366; background: rgba(37, 211, 102, 0.1); }
    .btn-edit { color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
    .btn-key { color: #f59e0b; background: rgba(245, 158, 11, 0.1); }
    .btn-delete { color: #ef4444; background: rgba(239, 68, 68, 0.1); }

    /* Form Styles Cleanup */
    .upload-section {
        background: white;
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
</style>

<div class="dashboard-header">
    <h2>Diretores Escolares</h2>
</div>

<div class="content-row" style="display: block;"> 
    
    <div class="upload-section">
        <h3 style="margin-bottom: 20px; color: #4b5563; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-user-tie"></i> Novo Diretor
        </h3>
        <form action="<?= url('semed/director/store') ?>" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
            <div class="form-group" style="margin: 0;">
                <label>Nome Completo</label>
                <input type="text" name="name" required placeholder="Nome do Diretor">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required placeholder="exemplo@sgp.com">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Escola</label>
                <select name="school_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach($schools as $school): ?>
                        <option value="<?= $school['id'] ?>"><?= htmlspecialchars($school['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Telefone/WhatsApp</label>
                <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
            </div>
            <div class="form-group" style="margin: 0;">
                <label>Endereço</label>
                <input type="text" name="address" placeholder="Rua, Número, Bairro...">
            </div>
            <button type="submit" class="btn btn-primary" style="height: 42px;">Salvar</button>
        </form>
        <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 10px;">* Senha padrão: <strong>123456</strong></p>
    </div>
    
    <div class="director-grid">
        <?php foreach($directors as $dir): ?>
            <div class="director-card">
                <div>
                    <div class="dir-header">
                        <div class="dir-avatar">
                            <?= strtoupper(substr($dir['name'], 0, 1)) ?>
                        </div>
                        <div class="dir-info">
                            <h4><?= htmlspecialchars($dir['name']) ?></h4>
                            <span><?= htmlspecialchars($dir['email']) ?></span>
                        </div>
                    </div>

                    <div class="schools-list">
                        <span class="schools-label">Escola</span>
                        <div style="font-weight: 500; font-size: 0.9rem;">
                            <?= htmlspecialchars($dir['school_name'] ?? 'Não vinculada') ?>
                        </div>
                        <?php if(!empty($dir['address'])): ?>
                            <div style="margin-top: 5px; color: #666; font-size: 0.85rem;">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($dir['address']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="<?= url('semed/director/edit?id=' . $dir['id']) ?>" class="action-btn btn-edit" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <?php if (!empty($dir['whatsapp'])): 
                        $phone = preg_replace('/\D/', '', $dir['whatsapp']);
                        if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') $phone = '55' . $phone;
                    ?>
                        <a href="https://wa.me/<?= $phone ?>?text=Olá, Diretor(a) <?= urlencode($dir['name']) ?>!" target="_blank" class="action-btn btn-whatsapp" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php endif; ?>

                    <a href="<?= url('semed/password/reset?id=' . $dir['id'] . '&role=director') ?>" class="action-btn btn-key" title="Resetar Senha (123456)" onclick="return confirm('Resetar senha do diretor para 123456?')">
                        <i class="fas fa-key"></i>
                    </a>

                    <a href="<?= url('semed/director/delete?id=' . $dir['id']) ?>" class="action-btn btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este Diretor?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
