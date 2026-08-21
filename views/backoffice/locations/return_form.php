<?php
$pageTitle = "Enregistrement du Retour Matériel";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h3 class="fw-bold mb-1"><i class="bi bi-box-arrow-in-left me-2 text-warning"></i> Inspection & Retour Matériel</h3>
                    <p class="text-muted small mb-0">Contrôle physique par l'Agent / Responsable Inventaire</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Client :</span>
                                <strong class="text-dark fs-6"><?= htmlspecialchars($location['client_nom'] . ' ' . $location['client_prenom']) ?></strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Équipement restitué :</span>
                                <strong class="text-primary fs-6"><?= htmlspecialchars($location['nom_equipement']) ?> (Qté: <?= $location['quantite'] ?>)</strong>
                            </div>
                        </div>
                    </div>

                    <form action="<?= BASE_URL ?>/index.php?action=location_retour" method="POST">
                        <input type="hidden" name="id_location" value="<?= $location['id_location'] ?>">

                        <!-- Inspection Physique (Questions du sujet) -->
                        <div class="card border-warning border-opacity-50 bg-warning bg-opacity-10 p-3 mb-4 rounded-3">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-search me-1"></i> Grille de vérification au retour :</h6>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" id="chk_fct" checked>
                                <label class="form-check-label" for="chk_fct">L'équipement est-il 100% fonctionnel ?</label>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" id="chk_raye">
                                <label class="form-check-label" for="chk_raye">Présence de rayures ou traces d'usure légères ?</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="chk_casse">
                                <label class="form-check-label text-danger font-weight-bold" for="chk_casse">L'équipement est-il cassé ou hors service ?</label>
                            </div>
                        </div>

                        <!-- Décision de l'état final -->
                        <div class="mb-4">
                            <label for="etat_retour" class="form-label font-weight-bold">Décision de l'État du Matériel <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="etat_retour" name="etat_retour" required>
                                <option value="Disponible">🟢 Disponible (Aucun dommage, prêt pour réutilisation)</option>
                                <option value="En maintenance">🟡 En maintenance (Besoin de révision / nettoyage / réparation mineure)</option>
                                <option value="Endommagé">🔴 Endommagé (Cassé / pièces détruites / inutilisable)</option>
                            </select>
                        </div>

                        <!-- Frais supplémentaires imposés par le sujet (ex: +150 DT) -->
                        <div class="mb-4">
                            <label for="frais_supplementaires" class="form-label font-weight-bold">Frais supplémentaires à facturer (DT)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control" id="frais_supplementaires" name="frais_supplementaires" value="0.00">
                                <span class="input-group-text">DT</span>
                            </div>
                            <small class="text-muted">Exemple : +150 DT si rendu cassé ou en retard.</small>
                        </div>

                        <div class="mb-4">
                            <label for="motif_frais" class="form-label font-weight-bold">Motif des frais / Remarques d'inspection</label>
                            <textarea class="form-control" id="motif_frais" name="motif_frais" rows="3" placeholder="ex: Écran rayé, câble d'alimentation manquant..."></textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=locations_admin" class="btn btn-outline-secondary w-50 fw-bold">Annuler</a>
                            <button type="submit" class="btn btn-warning w-50 fw-bold">
                                <i class="bi bi-box-arrow-in-left me-2"></i> Clôturer & Valider le Retour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
