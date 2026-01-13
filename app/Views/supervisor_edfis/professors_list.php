<?php 
// Atualizado em: 13/05/2026 - Correcao Layout
require __DIR__ . '/../layouts/header.php'; ?>

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

    <!-- Filtros de Busca (Layout Horizontal) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="<?= url('supervisor-edfis/professors') ?>">
                <div class="row g-3 align-items-end">
                    <!-- Campo de Busca -->
                    <div class="col-md-5">
                        <label for="search" class="form-label fw-bold text-secondary">Buscar Professor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" id="search" class="form-control border-start-0" 
                                   placeholder="Nome do professor..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Filtro de Escola -->
                    <div class="col-md-5">
                        <label for="school_id" class="form-label fw-bold text-secondary">Filtrar por Escola</label>
                        <select name="school_id" id="school_id" class="form-select cursor-pointer">
                            <option value="">Todas as Escolas</option>
                            <?php foreach ($schools as $school): ?>
                                <option value="<?= $school['id'] ?>" <?= (isset($_GET['school_id']) && $_GET['school_id'] == $school['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($school['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Botão Filtrar -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
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
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                                <!-- Foto do Professor -->
                                <?php 
                                    $photoUrl = !empty($prof['school_cover']) // Ops, school_cover não, profile_photo (mantendo lógica anterior corrigida)
                                        ? url('public/uploads/profiles/' . $prof['profile_photo']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($prof['name']) . '&background=random&size=150';
                                    
                                    if(empty($prof['profile_photo'])) {
                                         $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($prof['name']) . '&background=7F9CF5&color=fff&size=150';
                                    }
                                ?>
                                <img src="<?= $photoUrl ?>" alt="Foto" 
                                     class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                                
                                <div class="flex-grow-1 text-center text-md-start">
                                    <h3 class="card-title mb-1 text-dark fw-bold">
                                        <?= htmlspecialchars($prof['name']) ?>
                                    </h3>
                                    
                                    <div class="mb-3 text-muted" style="font-size: 1.1rem;">
                                        <i class="fas fa-school me-1 text-primary"></i> 
                                        <?= htmlspecialchars($prof['school_name'] ?? 'Sem escola vinculada') ?>
                                    </div>

                                    <!-- Botão WhatsApp Professor (Sempre Visível) -->
                                    <div class="mb-3">
                                        <?php if (!empty($prof['whatsapp'])): ?>
                                            <?php
                                                $phone = preg_replace('/[^0-9]/', '', $prof['whatsapp']);
                                                $msg = urlencode("Olá professor(a) {$prof['name']}, sou da Supervisão de Ed. Física do SGP.");
                                            ?>
                                            <a href="https://wa.me/55<?= $phone ?>?text=<?= $msg ?>" target="_blank" 
                                               class="btn btn-success btn-lg shadow-sm text-uppercase fw-bold w-100" 
                                               style="border-radius: 50px; padding: 10px 25px; font-size: 0.9rem;">
                                                <i class="fab fa-whatsapp fa-lg me-2"></i> WhatsApp Professor
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-lg shadow-sm text-uppercase fw-bold w-100" disabled
                                                    style="border-radius: 50px; padding: 10px 25px; font-size: 0.9rem; opacity: 0.6;">
                                                <i class="fab fa-whatsapp fa-lg me-2"></i> Sem WhatsApp
                                            </button>
                                        <?php endif; ?>
                                    </div>

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
