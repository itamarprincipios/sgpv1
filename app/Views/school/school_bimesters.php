<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="list-section">
    <h2><i class="fas fa-calendar-alt"></i> Organização por Bimestres</h2>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Associe os planejamentos cadastrados aos bimestres correspondentes para melhor organização.</p>
    
    <?php $showSchool = isset($schools) && count($schools) > 1; ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php 
        $bimestres = [
            ['id' => 1, 'name' => '1º Bimestre', 'color' => '#3b82f6'],
            ['id' => 2, 'name' => '2º Bimestre', 'color' => '#10b981'],
            ['id' => 3, 'name' => '3º Bimestre', 'color' => '#f59e0b'],
            ['id' => 4, 'name' => '4º Bimestre', 'color' => '#8b5cf6']
        ];
        
        foreach($bimestres as $bim):
            // Filter plannings for this bimester
            $bimPlannings = array_filter($plannings, function($p) use ($bim) {
                return isset($p['bimester']) && $p['bimester'] == $bim['id'];
            });
        ?>
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 4px solid <?= $bim['color'] ?>;">
                <h4 style="margin: 0 0 15px 0; color: <?= $bim['color'] ?>; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-calendar-check"></i>
                    <?= $bim['name'] ?>
                </h4>
                
                <?php if(empty($bimPlannings)): ?>
                    <p style="color: #9ca3af; font-size: 0.9rem; font-style: italic;">Nenhum planejamento associado a este bimestre.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach($bimPlannings as $p): ?>
                            <li style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                                    <?php if($showSchool): ?>
                                        <br><small style="color: #6b7280;"><?= htmlspecialchars($p['school_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= url('school/planning/view?id=' . $p['id']) ?>" class="btn-icon" title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <!-- Dropdown to associate unassigned plannings -->
                <?php 
                $unassignedPlannings = array_filter($plannings, function($p) {
                    return empty($p['bimester']) || $p['bimester'] == 0;
                });
                if(!empty($unassignedPlannings)): 
                ?>
                    <form action="<?= url('school/planning/associate-bimester') ?>" method="POST" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f3f4f6;">
                        <input type="hidden" name="bimester" value="<?= $bim['id'] ?>">
                        <select name="planning_id" required style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 10px;">
                            <option value="">Associar planejamento...</option>
                            <?php foreach($unassignedPlannings as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?>
                                    <?php if($showSchool): ?> - <?= htmlspecialchars($p['school_name']) ?><?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 8px; font-size: 0.9rem;">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
