<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="upload-section">
    <h3><i class="fas fa-user-plus"></i> Novo Coordenador</h3>
    <form action="<?= url('school/coordinator/store') ?>" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
        <div class="form-group" style="margin: 0;">
            <label>Nome Completo</label>
            <input type="text" name="name" required placeholder="Nome do Coordenador">
        </div>
        <div class="form-group" style="margin: 0;">
            <label>E-mail (Login)</label>
            <input type="email" name="email" required placeholder="email@sgp.com">
        </div>
        <div class="form-group" style="margin: 0;">
            <label>Escola</label>
            <select name="school_id" required>
                <?php foreach($schools as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label>WhatsApp</label>
            <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
        </div>
        <button type="submit" class="btn btn-primary" style="height: 42px;">Salvar</button>
    </form>
    <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 10px;">* Senha padrão: <strong>123456</strong></p>
</div>

<div class="list-section">
    <h3>Coordenadores da Escola</h3>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Escola</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($coordinators)): ?>
                    <tr><td colspan="4" style="text-align: center; color: #777;">Nenhum coordenador encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach($coordinators as $coord): ?>
                        <tr>
                            <td><?= htmlspecialchars($coord['name']) ?></td>
                            <td><?= htmlspecialchars($coord['email']) ?></td>
                            <td><?= htmlspecialchars($coord['school_name'] ?? 'N/A') ?></td>
                            <td>
                                <a href="<?= url('school/coordinator/edit?id='.$coord['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                
                                <?php if (!empty($coord['whatsapp'])): 
                                    $phone = preg_replace('/\\D/', '', $coord['whatsapp']);
                                    if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') $phone = '55' . $phone;
                                ?>
                                    <a href="https://wa.me/<?= $phone ?>" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                                
                                <a href="<?= url('school/coordinator/delete?id='.$coord['id']) ?>" class="btn-icon" style="color: red;" onclick="return confirm('Excluir coordenador?')"><i class="fas fa-trash"></i></a>
                                
                                <a href="<?= url('school/coordinator/reset-password?id='.$coord['id']) ?>" class="btn-icon" style="color: #f59e0b;" title="Resetar Senha (123456)" onclick="return confirm('Deseja resetar a senha deste coordenador para 123456?')"><i class="fas fa-key"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
