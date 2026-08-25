<?php
$pageTitle = "Suivi des Locations & Comptoir";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

// Calcul des KPI statistiques
$totalCount = count($locations);
$pendingCount = count(array_filter($locations, fn($l) => $l['statut'] === 'En attente'));
$activeCount = count(array_filter($locations, fn($l) => in_array($l['statut'], ['Validée', 'En cours'])));
$completedCount = count(array_filter($locations, fn($l) => $l['statut'] === 'Terminée'));
$totalRevenue = array_reduce($locations, fn($carry, $l) => $carry + (float)($l['montant_total'] ?? 0) + (float)($l['frais_supplementaires'] ?? 0), 0.0);
?>

<div class="container py-5 animate-rise">
    <!-- En-tête Back-office -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="bo-header-badge">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Back-office</span>
            </div>
            <h1 class="h2 fw-bold text-dark mb-1" style="font-family: var(--font-display);">Suivi des locations</h1>
            <p class="text-muted mb-0">Supervision en temps réel des réservations, validation comptoir et retours d'équipements.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" id="btnExportCsv" class="btn btn-outline-secondary d-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3 shadow-sm">
                <i class="bi bi-download"></i>
                <span>Exporter</span>
            </button>
            <a href="<?= BASE_URL ?>/index.php?action=location_comptoir" class="btn btn-primary d-flex align-items-center gap-2 fw-bold px-3 py-2 rounded-3 shadow-sm">
                <i class="bi bi-plus-circle"></i>
                <span>Créer une location</span>
            </a>
        </div>
    </div>

    <!-- 4 Cartes KPI Statistiques -->
    <div class="row g-3 mb-4">
        <!-- KPI 1 : Total -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bo-kpi-card">
                <div class="bo-kpi-top">
                    <div class="bo-kpi-icon" style="background: rgba(0, 145, 255, 0.12); color: #0091ff;">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size:0.72rem;">Global</span>
                </div>
                <div class="bo-kpi-val"><?= $totalCount ?></div>
                <div class="bo-kpi-label">Locations totales</div>
                <div class="bo-kpi-hint">Toutes réservations confondues</div>
            </div>
        </div>

        <!-- KPI 2 : En attente -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bo-kpi-card">
                <div class="bo-kpi-top">
                    <div class="bo-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill" style="font-size:0.72rem;">Prioritaire</span>
                </div>
                <div class="bo-kpi-val text-warning"><?= $pendingCount ?></div>
                <div class="bo-kpi-label">En attente de validation</div>
                <div class="bo-kpi-hint">Demandes à traiter rapidement</div>
            </div>
        </div>

        <!-- KPI 3 : En cours -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bo-kpi-card">
                <div class="bo-kpi-top">
                    <div class="bo-kpi-icon" style="background: rgba(14, 165, 233, 0.12); color: #0ea5e9;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill" style="font-size:0.72rem;">En rotation</span>
                </div>
                <div class="bo-kpi-val text-info"><?= $activeCount ?></div>
                <div class="bo-kpi-label">En cours</div>
                <div class="bo-kpi-hint">Matériel actuellement sorti</div>
            </div>
        </div>

        <!-- KPI 4 : Chiffre d'affaires -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="bo-kpi-card">
                <div class="bo-kpi-top">
                    <div class="bo-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size:0.72rem;">Total</span>
                </div>
                <div class="bo-kpi-val text-success" style="font-size: 1.6rem;"><?= number_format($totalRevenue, 2, ',', ' ') ?> <small style="font-size: 0.85rem;">DT</small></div>
                <div class="bo-kpi-label">Chiffre d'affaires</div>
                <div class="bo-kpi-hint">Revenus générés & frais</div>
            </div>
        </div>
    </div>

    <!-- Carte principale du Tableau & Filtres -->
    <div class="table-rental-card">
        <!-- Barre de filtres & recherche interactive -->
        <div class="p-3 p-md-4 border-bottom bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <!-- Boutons de filtrage rapide par statut -->
            <div class="d-flex align-items-center gap-2 flex-wrap" id="statusFilterGroup">
                <button type="button" class="btn btn-sm filter-status-btn active btn-dark rounded-pill px-3 py-2 fw-semibold" data-status="all">
                    Toutes <span class="badge bg-white text-dark ms-1 rounded-pill"><?= $totalCount ?></span>
                </button>
                <button type="button" class="btn btn-sm filter-status-btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" data-status="En attente">
                    En attente <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= $pendingCount ?></span>
                </button>
                <button type="button" class="btn btn-sm filter-status-btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" data-status="Validée">
                    Validées <span class="badge bg-info text-white ms-1 rounded-pill"><?= $activeCount ?></span>
                </button>
                <button type="button" class="btn btn-sm filter-status-btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" data-status="Terminée">
                    Terminées <span class="badge bg-success text-white ms-1 rounded-pill"><?= $completedCount ?></span>
                </button>
            </div>

            <!-- Champ de recherche en direct -->
            <div class="rentals-search-wrapper" style="min-width: 280px; max-width: 380px;">
                <i class="bi bi-search rentals-search-icon"></i>
                <input type="text" id="rentalSearchInput" class="form-control rentals-search-input" placeholder="Rechercher client, équipement, ID..." autocomplete="off">
            </div>
        </div>

        <!-- Tableau enrichi des locations -->
        <div class="table-responsive">
            <table class="table rentals-table align-middle" id="locationsTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Client (Locataire)</th>
                        <th>Équipement & Quantité</th>
                        <th>Période & Durée</th>
                        <th>Montant & Frais</th>
                        <th>Statut</th>
                        <th class="text-end" style="min-width: 220px;">Actions & Documents</th>
                    </tr>
                </thead>
                <tbody id="rentalTableBody">
                    <?php if (empty($locations)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Aucune location enregistrée pour le moment.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($locations as $loc): 
                            $nomClient = trim($loc['client_nom'] . ' ' . $loc['client_prenom']);
                            $initials = strtoupper(mb_substr($loc['client_prenom'] ?? '', 0, 1) . mb_substr($loc['client_nom'] ?? '', 0, 1)) ?: 'CL';
                            $status = $loc['statut'];
                            $totalAmount = (float)$loc['montant_total'] + (float)$loc['frais_supplementaires'];
                        ?>
                            <tr class="rental-row" 
                                data-status="<?= htmlspecialchars($status) ?>"
                                data-search="<?= htmlspecialchars(strtolower($loc['id_location'] . ' ' . $nomClient . ' ' . $loc['client_email'] . ' ' . ($loc['client_telephone'] ?? '') . ' ' . $loc['nom_equipement'] . ' ' . $loc['nom_categorie'])) ?>"
                                data-amount="<?= $totalAmount ?>">
                                
                                <!-- ID -->
                                <td>
                                    <span class="fw-bold text-secondary">#<?= $loc['id_location'] ?></span>
                                </td>

                                <!-- Client enrichi -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="client-avatar">
                                            <?= htmlspecialchars($initials) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($nomClient) ?></div>
                                            <div class="text-muted small d-flex align-items-center gap-1">
                                                <i class="bi bi-envelope" style="font-size: 0.75rem;"></i>
                                                <span><?= htmlspecialchars($loc['client_email']) ?></span>
                                            </div>
                                            <?php if (!empty($loc['client_telephone'])): ?>
                                                <div class="text-muted small d-flex align-items-center gap-1">
                                                    <i class="bi bi-telephone" style="font-size: 0.75rem;"></i>
                                                    <span><?= htmlspecialchars($loc['client_telephone']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Équipement & Catégorie -->
                                <td>
                                    <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($loc['nom_equipement']) ?></div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-light text-dark border fw-normal" style="font-size:0.75rem;">
                                            <i class="bi bi-tag me-1 text-primary"></i><?= htmlspecialchars($loc['nom_categorie']) ?>
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold" style="font-size:0.75rem;">
                                            Qté : <?= $loc['quantite'] ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Période & Durée -->
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-dark small fw-semibold mb-1">
                                        <i class="bi bi-calendar-event text-primary"></i>
                                        <span><?= date('d/m/Y', strtotime($loc['date_debut'])) ?></span>
                                        <span class="text-muted">➔</span>
                                        <i class="bi bi-calendar-check text-success"></i>
                                        <span><?= date('d/m/Y', strtotime($loc['date_fin'])) ?></span>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary border fw-normal" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock me-1"></i><?= $loc['duree_jours'] ?> jour(s)
                                    </span>
                                </td>

                                <!-- Montant & Frais -->
                                <td>
                                    <div class="fw-bold text-primary fs-6">
                                        <?= number_format($totalAmount, 2, ',', ' ') ?> DT
                                    </div>
                                    <?php if ($loc['frais_supplementaires'] > 0): ?>
                                        <div class="text-danger small fw-semibold mt-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>+<?= number_format($loc['frais_supplementaires'], 2, ',', ' ') ?> DT
                                        </div>
                                        <?php if (!empty($loc['motif_frais'])): ?>
                                            <div class="text-muted" style="font-size: 0.72rem; max-width: 140px;" title="<?= htmlspecialchars($loc['motif_frais']) ?>">
                                                <?= htmlspecialchars($loc['motif_frais']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Statut avec pastille (.status-pill) -->
                                <td>
                                    <?php if ($status === 'En attente'): ?>
                                        <span class="status-pill status-pill--en-attente">
                                            <span class="dot"></span>
                                            <span>En attente</span>
                                        </span>
                                    <?php elseif (in_array($status, ['Validée', 'En cours'])): ?>
                                        <span class="status-pill status-pill--validee">
                                            <span class="dot"></span>
                                            <span><?= htmlspecialchars($status) ?></span>
                                        </span>
                                    <?php elseif ($status === 'Terminée'): ?>
                                        <span class="status-pill status-pill--terminee">
                                            <span class="dot"></span>
                                            <span>Terminée</span>
                                        </span>
                                    <?php elseif ($status === 'Annulée'): ?>
                                        <span class="status-pill status-pill--annulee">
                                            <span class="dot"></span>
                                            <span>Annulée</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($status) ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions & Documents PDF -->
                                <td class="text-end">
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <!-- Actions contextuelles selon statut -->
                                        <?php if ($status === 'En attente'): ?>
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Validée" 
                                                   class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1 shadow-sm px-2 py-1" 
                                                   title="Valider la location">
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>Valider</span>
                                                </a>
                                                <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Annulée" 
                                                   class="btn btn-sm btn-outline-danger d-inline-flex align-items-center px-2 py-1" 
                                                   title="Refuser ou annuler"
                                                   onclick="return confirm('Confirmer l\'annulation de cette réservation ?');">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            </div>
                                        <?php elseif (in_array($status, ['Validée', 'En cours'])): ?>
                                            <a href="<?= BASE_URL ?>/index.php?action=location_retour&id=<?= $loc['id_location'] ?>" 
                                               class="btn btn-sm btn-warning text-dark fw-bold d-inline-flex align-items-center gap-1 shadow-sm px-3 py-1" 
                                               title="Diagnostiquer le retour matériel">
                                                <i class="bi bi-box-arrow-in-left"></i>
                                                <span>Diagnostic retour</span>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Trio PDF imposé : Contrat, Facture, Reçu -->
                                        <div class="btn-group btn-group-sm pdf-btn-group">
                                            <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-primary d-inline-flex align-items-center gap-1" 
                                               title="Imprimer Contrat PDF">
                                                <i class="bi bi-file-earmark-text"></i> Contrat
                                            </a>
                                            <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-success d-inline-flex align-items-center gap-1" 
                                               title="Imprimer Facture PDF">
                                                <i class="bi bi-receipt"></i> Facture
                                            </a>
                                            <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-info d-inline-flex align-items-center gap-1" 
                                               title="Imprimer Reçu PDF">
                                                <i class="bi bi-check2-square"></i> Reçu
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Message État vide lors de recherche infructueuse -->
        <div id="tableEmptyState" class="text-center py-5 text-muted d-none">
            <i class="bi bi-search fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <h6 class="fw-bold text-dark">Aucun résultat trouvé</h6>
            <p class="small text-muted mb-0">Essayez de modifier votre recherche ou de sélectionner un autre filtre de statut.</p>
        </div>

        <!-- Pied du tableau : Décompte & Total dynamique -->
        <div class="p-3 p-md-4 bg-light border-top d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <div class="text-muted small">
                Affichage de <span id="displayedCount" class="fw-bold text-dark"><?= $totalCount ?></span> location(s)
            </div>
            <div class="small fw-semibold text-dark">
                Total des montants filtrés : <span id="filteredAmountDisplay" class="fw-bold text-primary fs-6"><?= number_format($totalRevenue, 2, ',', ' ') ?> DT</span>
            </div>
        </div>
    </div>
</div>

<!-- Script interactif de filtrage, recherche, décompte et export CSV -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-status-btn');
    const searchInput = document.getElementById('rentalSearchInput');
    const rows = document.querySelectorAll('.rental-row');
    const emptyState = document.getElementById('tableEmptyState');
    const displayedCountEl = document.getElementById('displayedCount');
    const filteredAmountEl = document.getElementById('filteredAmountDisplay');
    const exportBtn = document.getElementById('btnExportCsv');

    let currentStatusFilter = 'all';
    let currentSearchTerm = '';

    function formatDT(amount) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount) + ' DT';
    }

    function applyFilters() {
        let visibleCount = 0;
        let visibleAmountSum = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search') || '';
            const rowAmount = parseFloat(row.getAttribute('data-amount')) || 0;

            let matchesStatus = false;
            if (currentStatusFilter === 'all') {
                matchesStatus = true;
            } else if (currentStatusFilter === 'Validée') {
                matchesStatus = (rowStatus === 'Validée' || rowStatus === 'En cours');
            } else {
                matchesStatus = (rowStatus === currentStatusFilter);
            }

            const matchesSearch = !currentSearchTerm || rowSearch.includes(currentSearchTerm);

            if (matchesStatus && matchesSearch) {
                row.style.display = '';
                visibleCount++;
                visibleAmountSum += rowAmount;
            } else {
                row.style.display = 'none';
            }
        });

        // Afficher ou masquer l'état vide
        if (visibleCount === 0 && rows.length > 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }

        // Mettre à jour le pied de tableau
        if (displayedCountEl) displayedCountEl.textContent = visibleCount;
        if (filteredAmountEl) filteredAmountEl.textContent = formatDT(visibleAmountSum);
    }

    // Gestion du clic sur les boutons de filtre
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => {
                b.classList.remove('active', 'btn-dark');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.add('active', 'btn-dark');
            btn.classList.remove('btn-outline-secondary');

            currentStatusFilter = btn.getAttribute('data-status');
            applyFilters();
        });
    });

    // Recherche en temps réel
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // Export CSV des lignes actuellement visibles
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            let csv = [];
            csv.push(['ID', 'Client', 'Équipement', 'Statut', 'Montant (DT)'].join(';'));

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const id = row.querySelector('td:nth-child(1)')?.innerText.trim();
                    const client = row.querySelector('td:nth-child(2) .fw-bold')?.innerText.trim();
                    const equipement = row.querySelector('td:nth-child(3) .fw-bold')?.innerText.trim();
                    const status = row.getAttribute('data-status') || '';
                    const amount = row.getAttribute('data-amount') || '0';

                    csv.push([`"${id}"`, `"${client}"`, `"${equipement}"`, `"${status}"`, `"${amount}"`].join(';'));
                }
            });

            const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv.join('\n'));
            const downloadAnchor = document.createElement('a');
            downloadAnchor.setAttribute('href', csvContent);
            downloadAnchor.setAttribute('download', `suivi_locations_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            document.body.removeChild(downloadAnchor);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
