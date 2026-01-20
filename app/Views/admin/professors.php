<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>👨‍🏫 Gestão de Professores</h1>
    <p>Visualize e gerencie os professores da rede.</p>
</div>

    <div class="list-section filter-section" style="margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <form method="GET" action="<?= url('admin/professors') ?>" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; color: #555; font-size: 0.9rem;">Escola</label>
                <select name="school_id" class="form-control" onchange="this.form.submit()" style="width: 100%;">
                    <option value="">Todas as Escolas</option>
                    <?php foreach($schools as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($filters['school_id'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #555; font-size: 0.9rem;">Turma</label>
                <select name="class_id" class="form-control" onchange="this.form.submit()" style="width: 100%;" <?= empty($filters['school_id']) ? 'disabled' : '' ?>>
                    <option value="">Todas as Turmas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filters['class_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="font-weight: bold; color: #555; font-size: 0.9rem;">Função</label>
                <select name="function" class="form-control" onchange="this.form.submit()" style="width: 100%;">
                    <option value="">Todas</option>
                    <option value="titular" <?= ($filters['function'] == 'titular') ? 'selected' : '' ?>>Professor Titular (Regente)</option>
                    <option value="edfis" <?= ($filters['function'] == 'edfis') ? 'selected' : '' ?>>Professor Ed. Física</option>
                    <option value="monitor" <?= ($filters['function'] == 'monitor') ? 'selected' : '' ?>>Professor Monitor</option>
                </select>
            </div>

             <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; color: #555; font-size: 0.9rem;">Buscar Nome</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Nome do professor..." class="form-control" style="width: 100%;">
            </div>

            <button type="submit" class="btn btn-primary" style="height: 42px;">
                <i class="fas fa-search"></i> Filtrar
            </button>
            
            <a href="<?= url('admin/professors') ?>" class="btn btn-secondary" style="height: 42px; line-height: 28px; text-decoration: none;">
                Limpar
            </a>

        </form>
    </div>

    <div class="list-section">
        <h3 style="margin-bottom: 20px; color: #1e293b;">
            Professores Encontrados 
            <span style="font-size: 0.8em; color: #777; font-weight: normal;">(Total: <?= count($professors) ?>)</span>
        </h3>
        
        <table class="data-table" id="profTable">
            <thead>
                <tr>
                    <th>Nome / Função</th>
                    <th>Escola</th>
                    <th>Turma</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($professors)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 20px;">Nenhum professor encontrado com os filtros selecionados.</td></tr>
                <?php else: ?>
                    <?php foreach($professors as $p): ?>
                        <tr>
                            <td>
                                <div style="font-weight: bold;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size: 0.85rem; margin-top: 4px;">
                                    <?php if (!empty($p['is_monitor'])): ?>
                                        <span style="background-color: #17a2b8; color: #fff; padding: 2px 6px; border-radius: 4px;">Monitor</span>
                                    <?php elseif (!empty($p['is_physical_education'])): ?>
                                        <span style="background-color: #28a745; color: #fff; padding: 2px 6px; border-radius: 4px;">Ed. Física</span>
                                    <?php else: ?>
                                        <span style="background-color: #6c757d; color: #fff; padding: 2px 6px; border-radius: 4px;">Titular</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['school_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($p['class_name'] ?? '-') ?></td>
                            <td>
                                <a href="<?= url('admin/user/reset-password?id='.$p['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                                <a href="<?= url('admin/user/delete?id='.$p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<script>
// Mantendo script antigo se necessário, mas o filtro PHP substitui a busca JS local
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
