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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">Professores da Escola</h3>
            <button type="button" onclick="window.print()" class="btn btn-secondary" style="padding: 0.5rem 1rem; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;" title="Imprimir lista de professores">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
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

<style>
    /* Professional Print Styles - Corporate Layout */
    @media print {
        @page { 
            margin: 1.5cm; 
            size: A4 portrait; 
        }
        
        body { 
            background: #fff !important; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important;
        }
        
        /* Hide non-printable elements */
        .navbar, 
        .btn, 
        .upload-section,
        .btn-icon,
        button {
            display: none !important;
        }
        
        .main-container { 
            padding: 0 !important; 
            width: 100% !important; 
            max-width: 100% !important; 
            margin: 0 !important; 
        }
        
        .content-row {
            display: block !important;
        }
        
        .list-section { 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important; 
            margin: 0 !important; 
            page-break-inside: avoid;
        }
        
        /* Professional Header */
        .list-section::before {
            content: "SISTEMA DE GESTÃO PEDAGÓGICA - LISTA DE PROFESSORES";
            display: block;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }
        
        .list-section {
            position: relative;
        }
        
        .list-section::after {
            content: "Escola: " attr(data-school-name) "\A Diretor(a): " attr(data-director-name) "\A Emitido em: " attr(data-print-date);
            display: block;
            text-align: left;
            font-size: 11px;
            color: #444;
            margin-top: 8px;
            margin-bottom: 15px;
            white-space: pre-line;
            line-height: 1.6;
            font-weight: 500;
        }
        
        h3 {
            font-size: 14px !important;
            text-align: center;
            margin: 10px 0 15px 0 !important;
            color: #444 !important;
            font-weight: 600;
        }
        
        /* Professional Table Styling */
        .data-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px;
            font-size: 10px !important;
            display: table !important;
        }
        
        .data-table thead {
            background-color: #2c3e50 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            display: table-header-group !important;
        }
        
        .data-table tbody {
            display: table-row-group !important;
        }
        
        .data-table tr {
            display: table-row !important;
            page-break-inside: avoid;
        }
        
        .data-table th {
            padding: 8px 6px !important;
            border: 1px solid #34495e !important;
            font-weight: 600 !important;
            font-size: 10px !important;
            text-align: left !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            display: table-cell !important;
        }
        
        .data-table td {
            padding: 6px !important;
            border: 1px solid #ddd !important;
            font-size: 9px !important;
            color: #000 !important;
            vertical-align: middle !important;
            display: table-cell !important;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Column widths */
        .data-table th:nth-child(1),
        .data-table td:nth-child(1) {
            width: 35%;
        }
        
        .data-table th:nth-child(2),
        .data-table td:nth-child(2) {
            width: 30%;
        }
        
        .data-table th:nth-child(3),
        .data-table td:nth-child(3) {
            width: 35%;
        }
        
        /* Hide actions column */
        .data-table th:last-child,
        .data-table td:last-child {
            display: none !important;
        }
        
        /* Badges */
        .badge {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            color: #000 !important;
            font-weight: normal !important;
        }
        
        /* Ensure proper page breaks */
        h3 {
            page-break-after: avoid;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>

<script>
    // Auto-insert current date/time, school name, and director name for print header
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const dateStr = now.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const section = document.querySelector('.list-section');
        if (section) {
            section.setAttribute('data-print-date', dateStr);
            
            // Get school name from PHP
            <?php if (isset($schools) && !empty($schools)): ?>
                section.setAttribute('data-school-name', '<?= htmlspecialchars($schools[0]['name'] ?? 'N/A') ?>');
            <?php endif; ?>
            
            // Get director name from user session
            <?php 
            $user = auth();
            if (isset($user)): 
            ?>
                section.setAttribute('data-director-name', '<?= htmlspecialchars($user['name'] ?? 'N/A') ?>');
            <?php endif; ?>
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
