<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>👨‍🏫 Gestão de Professores</h1>
    <p>Visualize e gerencie os professores da rede.</p>
</div>

    <style>
        .filter-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
            border: 1px solid #f1f5f9;
        }
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }
        .filter-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        .filter-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #fff;
            color: #334155;
            font-size: 0.95rem;
            transition: all 0.2s;
            appearance: none; /* Remove default arrow for custom styling if desired, but keeping simple for now */
        }
        .filter-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            grid-column: 1 / -1; /* Span full width on mobile, but we'll adjust for desktop */
            justify-content: flex-end;
            margin-top: 10px;
        }
        @media (min-width: 1024px) {
            .filter-form {
                grid-template-columns: 1fr 1fr 1fr 1fr auto; /* Inputs take space, buttons auto */
            }
            .filter-actions {
                grid-column: auto; /* Revert span */
                margin-top: 0;
                justify-content: flex-start;
            }
        }
        .btn-filter {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            height: 42px;
        }
        .btn-filter-primary {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }
        .btn-filter-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }
        .btn-filter-secondary {
            background-color: #f1f5f9;
            color: #64748b;
            text-decoration: none;
        }
        .btn-filter-secondary:hover {
            background-color: #e2e8f0;
            color: #334155;
        }
    </style>

    <div class="filter-container">
        <form method="GET" action="<?= url('admin/professors') ?>" class="filter-form">
            
            <div class="filter-group">
                <label>Escola</label>
                <select name="school_id" class="filter-control" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    <?php foreach($schools as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($filters['school_id'] == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Turma</label>
                <select name="class_id" class="filter-control" onchange="this.form.submit()" <?= empty($filters['school_id']) ? 'disabled' : '' ?>>
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

            <div class="filter-group">
                <label>Função</label>
                <select name="function" class="filter-control" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="titular" <?= ($filters['function'] == 'titular') ? 'selected' : '' ?>>Professor Titular</option>
                    <option value="edfis" <?= ($filters['function'] == 'edfis') ? 'selected' : '' ?>>Professor Ed. Física</option>
                    <option value="monitor" <?= ($filters['function'] == 'monitor') ? 'selected' : '' ?>>Professor Monitor</option>
                </select>
            </div>

             <div class="filter-group">
                <label>Buscar Nome</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Digite o nome..." class="filter-control">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter btn-filter-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                
                <a href="<?= url('admin/professors') ?>" class="btn-filter btn-filter-secondary">
                    Limpar
                </a>
            </div>

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
