<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="list-section">
    <h2 style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Pendências de Entrega (Tempo Real)</h2>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Acompanhe abaixo os professores que ainda não enviaram os planejamentos vigentes ou atrasados.</p>
    
    <?php $showSchool = isset($schools) && count($schools) > 1; ?>
    
    <table class="data-table">
        <thead>
            <tr>
                <?php if($showSchool): ?><th>Escola</th><?php endif; ?>
                <th>Professor</th>
                <th>Turma</th>
                <th>Planejamento Pendente</th>
                <th>Prazo</th>
                <th>Status</th>
                <th>Cobrar</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($pendingSubmissions)): ?>
                <tr><td colspan="<?= $showSchool ? 7 : 6 ?>" style="text-align: center; color: #2ecc71; font-weight: bold; padding: 20px;"><i class="fas fa-check-circle"></i> Parabéns! Nenhuma pendência encontrada.</td></tr>
            <?php else: ?>
                <?php foreach($pendingSubmissions as $p): 
                    $isLate = strtotime($p['deadline']) < time();
                ?>
                    <tr style="<?= $isLate ? 'background-color: #fff5f5;' : '' ?>">
                        <?php if($showSchool): ?>
                            <td><small class="badge" style="background: #e2e8f0; color: #333;"><?= htmlspecialchars($p['school_name']) ?></small></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($p['professor_name']) ?></td>
                        <td><?= htmlspecialchars($p['class_name']) ?></td>
                        <td><?= htmlspecialchars($p['planning_name']) ?></td>
                        <td style="font-weight: bold; <?= $isLate ? 'color: #c0392b;' : '' ?>">
                            <?= date('d/m/Y', strtotime($p['deadline'])) ?>
                        </td>
                        <td>
                            <?php if($isLate): ?>
                                <span class="status-badge status-rejeitado">Atrasado</span>
                            <?php else: ?>
                                <span class="status-badge status-ajustado">Pendente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($p['whatsapp'])): 
                                $phone = preg_replace('/\\D/', '', $p['whatsapp']);
                                if (strlen($phone) >= 10 && substr($phone, 0, 2) != '55') {
                                    $phone = '55' . $phone;
                                }
                                $msg = $isLate 
                                    ? "Olá, " . urlencode($p['professor_name']) . "! Consta em nosso sistema que a entrega do planejamento *" . urlencode($p['planning_name']) . "* está atrasada. O prazo era " . date('d/m/Y', strtotime($p['deadline'])) . ". Poderia verificar?"
                                    : "Olá, " . urlencode($p['professor_name']) . "! Lembrete amigável: o prazo para entrega do planejamento *" . urlencode($p['planning_name']) . "* encerra em " . date('d/m/Y', strtotime($p['deadline'])) . ".";
                            ?>
                                <a href="https://wa.me/<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="whatsapp-btn" style="background: #25D366;">
                                    <i class="fab fa-whatsapp"></i> Cobrar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
