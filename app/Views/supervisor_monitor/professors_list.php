<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- CSS Específico inspirado em Relatórios SEMED -->
<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .dashboard-header h2 {
        font-size: 1.75rem;
        color: #333;
        margin: 0;
        font-weight: 700;
    }

    .list-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
    }

    .filter-container {
        display: flex;
        gap: 20px;
        align-items: flex-end;
        flex-wrap: wrap; /* Para mobile */
    }

    .filter-group {
        flex: 1; /* Ocupar espaço disponível */
        min-width: 200px;
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 0.75rem;
        color: #8898aa;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .filter-input, .filter-select {
        width: 100%;
        padding: 10px 15px;
        font-size: 0.95rem;
        color: #495057;
        background-color: #f8f9fa;
        background-clip: padding-box;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        height: 45px; /* Altura confortável */
    }

    .filter-input:focus, .filter-select:focus {
        background-color: #fff;
        border-color: #11998e;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
    }

    .btn-filter {
        background-color: #11998e;
        color: #fff;
        border: none;
        padding: 0 25px;
        height: 45px;
        border-radius: 6px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-filter:hover {
        background-color: #0e847a;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* Estilo WhatsApp SEMED */
    .whatsapp-btn {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    
    .whatsapp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    .prof-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        height: 100%;
    }
    .prof-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.05);
    }
</style>

<div class="main-container">
    
    <!-- Cabeçalho Estilo Relatórios -->
    <div class="dashboard-header">
        <div>
            <h2>Professores Monitores</h2>
            <p class="text-muted mb-0">Gestão da rede municipal</p>
        </div>
        <div>
            <a href="<?= url('supervisor-monitor/dashboard') ?>" class="btn btn-secondary px-4" style="border-radius: 6px; font-weight: 500;">
                <i class="fas fa-arrow-left me-2"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <!-- Seção de Filtros (Frontend Relatórios) -->
    <div class="list-section">
        <form method="GET" action="<?= url('supervisor-monitor/professors') ?>" class="filter-container">
            
            <!-- Busca por Nome -->
            <div class="filter-group" style="flex: 2;">
                <label for="search" class="filter-label">Buscar Professor</label>
                <input type="text" name="search" id="search" class="filter-input" 
                       placeholder="Digite o nome..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            
            <!-- Filtro de Escola -->
            <div class="filter-group" style="flex: 2;">
                <label for="school_id" class="filter-label">Unidade Escolar</label>
                <select name="school_id" id="school_id" class="filter-select cursor-pointer">
                    <option value="">Todas as Escolas</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>" <?= (isset($_GET['school_id']) && $_GET['school_id'] == $school['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Botão Filtrar -->
            <div style="padding-bottom: 2px;"> <!-- Ajuste fino de alinhamento -->
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Professores -->
    <div class="row">
        <?php if (empty($professors)): ?>
            <div class="col-12">
                <div class="list-section text-center p-5">
                    <div style="opacity: 0.5; font-size: 3rem; margin-bottom: 20px;"><i class="fas fa-ghost"></i></div>
                    <h4 class="text-muted">Nenhum professor encontrado.</h4>
                    <p class="text-secondary">Tente ajustar seus filtros de busca.</p>
                    <a href="<?= url('supervisor-monitor/professors') ?>" class="btn btn-link text-decoration-none fw-bold">Limpar Filtros</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($professors as $prof): ?>
                <div class="col-md-6 mb-4">
                    <div class="card prof-card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                                <!-- Foto do Professor -->
                                <?php 
                                    $photoUrl = !empty($prof['profile_photo']) 
                                        ? url('uploads/avatars/' . $prof['profile_photo']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($prof['name']) . '&background=random&size=150';
                                ?>
                                <img src="<?= $photoUrl ?>" alt="Foto" 
                                     class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;"
                                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode($prof['name']) ?>&background=random&size=150';">
                                
                                <div class="flex-grow-1 text-center text-md-start">
                                    
                                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-2">
                                        <h3 class="card-title mb-0 text-dark fw-bold" style="font-size: 1.4rem;">
                                            <?= htmlspecialchars($prof['name']) ?>
                                        </h3>
                                        
                                        <!-- Botão WhatsApp Estilo SEMED -->
                                        <?php if (!empty($prof['whatsapp'])): ?>
                                            <?php
                                                $phone = preg_replace('/[^0-9]/', '', $prof['whatsapp']);
                                                $msg = urlencode("Olá professor(a) {$prof['name']}, sou da Supervisão de Monitores do SGP.");
                                            ?>
                                            <a href="https://wa.me/55<?= $phone ?>?text=<?= $msg ?>" target="_blank" 
                                               class="whatsapp-btn" title="Falar com Professor">
                                                <i class="fab fa-whatsapp fa-lg"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3 text-secondary">
                                        <i class="fas fa-school me-2 text-primary"></i> 
                                        <?= htmlspecialchars($prof['school_name'] ?? 'Sem escola vinculada') ?>
                                    </div>

                                    <hr style="border-top: 1px dashed #e9ecef;">

                                    <!-- Seção Coordenador(a) -->
                                    <div class="bg-light p-3 rounded-3" style="font-size: 0.9rem;">
                                        <strong class="text-secondary d-block mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            Coordenação da Escola
                                        </strong>
                                        
                                        <?php 
                                            $schoolId = $prof['school_id'] ?? 0;
                                            $coords = $coordinatorsMap[$schoolId] ?? [];
                                        ?>
                                        
                                        <?php if (empty($coords)): ?>
                                            <span class="text-muted small fst-italic">Nenhum coordenador vinculado.</span>
                                        <?php else: ?>
                                            <?php foreach ($coords as $coord): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="text-dark"><?= htmlspecialchars($coord['name']) ?></span>
                                                    <?php if(!empty($coord['whatsapp'])): ?>
                                                        <?php
                                                            $cPhone = preg_replace('/[^0-9]/', '', $coord['whatsapp']);
                                                            $cMsg = urlencode("Olá coordenador(a) {$coord['name']}, assunto referente à Monitoria.");
                                                        ?>
                                                        <a href="https://wa.me/55<?= $cPhone ?>?text=<?= $cMsg ?>" target="_blank" 
                                                           class="text-success" style="opacity: 0.8; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8" title="WhatsApp Coordenação">
                                                            <i class="fab fa-whatsapp fa-lg"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <!-- Botão Voltar Rodapé -->
    <div class="d-flex justify-content-center mt-4 mb-5">
        <a href="<?= url('supervisor-monitor/dashboard') ?>" class="btn btn-secondary px-4 py-2" style="border-radius: 6px;">
            <i class="fas fa-arrow-left me-2"></i> Voltar ao Painel
        </a>
    </div>
</div>
