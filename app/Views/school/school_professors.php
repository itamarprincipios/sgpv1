<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="content-row">
    <div class="upload-section">
        <h3>Cadastrar Professor</h3>
        <form action="<?= url('school/professor/store') ?>" method="POST">
            <?php if(count($schools) > 1): ?>
                <div class="form-group">
                    <label>Escola</label>
                    <select name="school_id" required class="form-control" style="width: 100%; margin-bottom: 10px;">
                        <?php foreach($schools as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="school_id" value="<?= $schools[0]['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>E-mail (Login)</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" placeholder="Ex: 5511999999999">
            </div>
            <div class="form-group">
                <label>Vincular a Turma</label>
                <select name="class_id">
                    <option value="">Selecione uma turma...</option>
                    <?php 
                    $showSchool = isset($schools) && count($schools) > 1;
                    foreach($classes as $c): 
                    ?>
                        <option value="<?= $c['id'] ?>">
                            <?php if($showSchool) echo '[' . htmlspecialchars($c['school_name'] ?? '') . '] '; ?>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                <input type="checkbox" name="is_physical_education" id="prof_is_pe" value="1" style="width: 18px; height: 18px;">
                <label for="prof_is_pe" style="margin: 0; cursor: pointer;">Professor de Educação Física?</label>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar Professor</button>
        </form>
    </div>
    <div class="list-section">
        <h3>Professores da Escola</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                    <th>Nome</th>
                    <th>Turma</th>
                    <th>WhatsApp</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($professors as $prof): ?>
                    <tr>
                        <?php if($showSchool): ?>
                            <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($prof['school_name'] ?? '') ?></small></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($prof['name']) ?></td>
                        <td>
                            <?php 
                            if ($prof['is_physical_education'] == 1) {
                                echo '<span style="color: #10b981; font-weight: 600;">Educação Física</span>';
                            } elseif ($prof['class_name']) {
                                echo htmlspecialchars($prof['class_name']);
                            } else {
                                echo '<span style="color:red">Sem Turma</span>';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($prof['whatsapp']) ?></td>
                        <td>
                            <a href="<?= url('school/professor/edit?id='.$prof['id']) ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                            <?php if (!empty($prof['whatsapp'])): 
                                $phone = preg_replace('/\\D/', '', $prof['whatsapp']);
                                if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                    $phone = '55' . $phone;
                                }
                            ?>
                                <a href="https://wa.me/<?= $phone ?>?text=Olá, professor(a) <?= urlencode($prof['name']) ?>!" target="_blank" class="btn-icon" style="color: #25D366;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <?php endif; ?>
                            <a href="<?= url('school/professor/reset-password?id='.$prof['id']) ?>" class="btn-icon" style="color: #f59e0b;" title="Resetar Senha" onclick="return confirm('Resetar a senha do professor <?= htmlspecialchars($prof['name']) ?> para \'professor123\'?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('school/professor/delete?id='.$prof['id']) ?>" class="btn-icon" style="color: red;" onclick="return confirm('Tem certeza que vai excluir o professor? (Esta ação não pode ser desfeita)')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
