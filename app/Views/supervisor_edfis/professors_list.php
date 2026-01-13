<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= url('supervisor-edfis/dashboard') ?>" class="btn btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Voltar ao Painel
            </a>
            <h1><i class="fas fa-users"></i> Professores de Educação Física</h1>
            <p class="text-muted">Gestão e contato dos professores da rede municipal</p>
        </div>
    </div>

    <!-- Filtros de Busca -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-4 bg-white text-center">
            <h4 class="card-title mb-4 text-secondary"><i class="fas fa-search me-2"></i>Filtrar Professores</h4>
            
            <form method="GET" action="<?= url('supervisor-edfis/professors') ?>">
                <!-- Container Rígido Centralizado: 50% da largura da tela (desktop) -->
                <div style="width: 100%; max-width: 600px; margin: 0 auto;">
                    
                    <!-- Campo de Busca -->
                    <div class="mb-4">
                        <div class="text-start mb-2 fw-bold text-dark" style="font-size: 1.1rem;">Buscar por Nome:</div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="search" class="form-control border-start-0 bg-light" 
                                   placeholder="Digite o nome..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   style="height: 50px; font-size: 1.1rem;">
                        </div>
                    </div>
                    
                    <!-- Filtro de Escola -->
                    <div class="mb-4">
                        <div class="text-start mb-2 fw-bold text-dark" style="font-size: 1.1rem;">Filtrar por Escola:</div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-school text-muted"></i></span>
                            <select name="school_id" id="school_id" class="form-select border-start-0 bg-light" style="height: 50px; font-size: 1.1rem; cursor: pointer;">
                                <option value="">Todas as Escolas da Rede</option>
                                <?php foreach ($schools as $school): ?>
                                    <option value="<?= $school['id'] ?>" <?= (isset($_GET['school_id']) && $_GET['school_id'] == $school['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($school['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Botão Filtrar -->
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" 
                            style="border-radius: 8px; height: 50px; font-size: 1.1rem; text-transform: uppercase;">
                        <i class="fas fa-filter me-2"></i> Filtrar Resultados
                    </button>
                    
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Professores -->
    <div class="row">
        <?php if (empty($professors)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4>Nenhum professor encontrado.</h4>
                    <p class="text-muted">Tente ajustar os filtros de busca.</p>
                    <a href="<?= url('supervisor-edfis/professors') ?>" class="btn btn-outline-primary mt-2">Limpar Filtros</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($professors as $prof): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Foto do Professor -->
                                <?php 
                                    $photoUrl = !empty($prof['profile_photo']) 
                                        ? url('public/uploads/profiles/' . $prof['profile_photo']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($prof['name']) . '&background=random';
                                ?>
                                <img src="<?= $photoUrl ?>" alt="Foto" 
                                     class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1 text-primary fw-bold">
                                        <?= htmlspecialchars($prof['name']) ?>
                                    </h5>
                                    
                                    <div class="mb-2 text-muted small">
                                        <i class="fas fa-school me-1"></i> 
                                        <?= htmlspecialchars($prof['school_name'] ?? 'Sem escola vinculada') ?>
                                    </div>

                                    <!-- Contato Professor -->
                                    <?php if (!empty($prof['whatsapp'])): ?>
                                        <div class="mb-3">
                                            <?php
                                                $phone = preg_replace('/[^0-9]/', '', $prof['whatsapp']);
                                                $msg = urlencode("Olá professor(a) {$prof['name']}, sou da Supervisão de Ed. Física do SGP.");
                                            ?>
                                            <a href="https://wa.me/55<?= $phone ?>?text=<?= $msg ?>" target="_blank" 
                                               class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="fab fa-whatsapp"></i> Contato Professor
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <hr style="border-top: 1px dashed #dee2e6;">

                                    <!-- Seção Coordenador(a) -->
                                    <div class="bg-light p-2 rounded" style="font-size: 0.85rem;">
                                        <strong class="text-secondary d-block mb-1">
                                            <i class="fas fa-user-tie me-1"></i> Coordenação da Escola:
                                        </strong>
                                        
                                        <?php 
                                            $schoolId = $prof['school_id'] ?? 0;
                                            $coords = $coordinatorsMap[$schoolId] ?? [];
                                        ?>
                                        
                                        <?php if (empty($coords)): ?>
                                            <span class="text-muted fst-italic">Nenhum coordenador identificado.</span>
                                        <?php else: ?>
                                            <?php foreach ($coords as $coord): ?>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span><?= htmlspecialchars($coord['name']) ?></span>
                                                    <?php if(!empty($coord['whatsapp'])): ?>
                                                        <?php
                                                            $cPhone = preg_replace('/[^0-9]/', '', $coord['whatsapp']);
                                                            $cMsg = urlencode("Olá coordenador(a) {$coord['name']}, assunto referente à Educação Física.");
                                                        ?>
                                                        <a href="https://wa.me/55<?= $cPhone ?>?text=<?= $cMsg ?>" target="_blank" 
                                                           class="text-success text-decoration-none fw-bold" title="WhatsApp Coordenação">
                                                            <i class="fab fa-whatsapp"></i>
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
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-2px);
    }
</style>
