<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="main-content">
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Banco de Planejamentos</h1>
                <p>Pesquise e visualize históricos de envios.</p>
            </div>
            <a href="<?= url('admin/dashboard') ?>" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

    <!-- Filters -->
    <style>
        .table th, .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
    </style>
    <div class="dashboard-cards" style="grid-template-columns: 1fr;">
        <div class="card">
            <form method="GET" action="<?= url('admin/history') ?>" class="d-flex gap-2 align-items-end flex-wrap">
                
                <div class="form-group mb-0">
                    <label>Ano Letivo</label>
                    <select name="year" class="form-control">
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y ?>" <?= $filters['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label>Escola</label>
                    <select name="school_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Todas as Escolas</option>
                        <?php foreach ($schools as $school): ?>
                            <option value="<?= $school['id'] ?>" <?= $filters['school_id'] == $school['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($school['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label>Professor</label>
                    <select name="professor_id" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($professors as $prof): ?>
                            <option value="<?= $prof['id'] ?>" <?= $filters['professor_id'] == $prof['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($prof['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="dashboard-cards" style="grid-template-columns: 1fr; margin-top: 20px;">
        <div class="card">
            <h3>Resultados (<?= count($documents) ?>)</h3>
            <?php if (empty($documents)): ?>
                <p class="text-muted">Nenhum documento encontrado com os filtros selecionados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data Envio</th>
                                <th>Escola</th>
                                <th>Professor</th>
                                <th>Título</th>
                                <th>Período</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($doc['submitted_at'])) ?></td>
                                    <td><?= htmlspecialchars($doc['school_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($doc['professor_name']) ?></td>
                                    <td><?= htmlspecialchars($doc['title']) ?></td>
                                    <td><?= htmlspecialchars($doc['period_name']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $doc['status'] == 'aprovado' ? 'success' : ($doc['status'] == 'enviado' ? 'info' : 'warning') ?>">
                                            <?= ucfirst($doc['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= url('uploads/' . $doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
