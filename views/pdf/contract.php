<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de Location #<?= $location['id_location'] ?> - EquipLoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #1e293b; }
        .document-container { max-width: 850px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .header-logo { border-bottom: 3px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .signature-box { height: 100px; border: 2px dashed #cbd5e1; border-radius: 8px; margin-top: 15px; }
        @media print {
            .no-print { display: none !important; }
            .document-container { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container no-print text-end pt-3">
    <button onclick="window.print()" class="btn btn-primary fw-bold">
        <i class="bi bi-printer"></i> Imprimer / Enregistrer en PDF
    </button>
</div>

<div class="document-container">
    <div class="header-logo d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-primary mb-0">EquipLoc SARL</h2>
            <small class="text-muted">Plateforme Officielle de Location d'Équipements</small>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0">CONTRAT DE LOCATION</h4>
            <span class="badge bg-dark">N° CONTRAT-<?= sprintf('%05d', $location['id_location']) ?></span>
            <small class="d-block text-muted">Date : <?= date('d/m/Y') ?></small>
        </div>
    </div>

    <!-- Informations des parties -->
    <div class="row mb-4">
        <div class="col-6">
            <div class="p-3 bg-light rounded-3 border">
                <h6 class="fw-bold text-primary mb-2">BAILLEUR (Entreprise) :</h6>
                <strong>EquipLoc Tunisie</strong><br>
                Avenue Habib Bourguiba, Tunis<br>
                Tél : +216 71 000 000<br>
                Email : contact@equiploc.tn
            </div>
        </div>
        <div class="col-6">
            <div class="p-3 bg-light rounded-3 border">
                <h6 class="fw-bold text-primary mb-2">LOCATAIRE (Client) :</h6>
                <strong><?= htmlspecialchars($location['client_nom'] . ' ' . $location['client_prenom']) ?></strong><br>
                Email : <?= htmlspecialchars($location['client_email']) ?><br>
                Téléphone : <?= htmlspecialchars($location['client_telephone'] ?? 'Non renseigné') ?><br>
                Client ID : #<?= $location['id_client'] ?>
            </div>
        </div>
    </div>

    <!-- Objet du contrat -->
    <h5 class="fw-bold border-bottom pb-2 mb-3">1. Équipement Loué</h5>
    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th>Quantité</th>
                <th>Prix / Jour</th>
                <th>Durée</th>
                <th>Montant HT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong><?= htmlspecialchars($location['nom_equipement']) ?></strong></td>
                <td><?= htmlspecialchars($location['nom_categorie']) ?></td>
                <td><?= $location['quantite'] ?></td>
                <td><?= number_format($location['prix_par_jour'], 2) ?> DT</td>
                <td><?= $location['duree_jours'] ?> jour(s)</td>
                <td class="fw-bold"><?= number_format($location['montant_total'], 2) ?> DT</td>
            </tr>
        </tbody>
    </table>

    <h5 class="fw-bold border-bottom pb-2 mb-3">2. Période de Location</h5>
    <p>Le matériel est mis à disposition du <strong><?= date('d/m/Y', strtotime($location['date_debut'])) ?></strong> au <strong><?= date('d/m/Y', strtotime($location['date_fin'])) ?></strong> inclus.</p>

    <h5 class="fw-bold border-bottom pb-2 mb-3">3. Conditions et Engagements</h5>
    <ul class="small text-muted mb-4">
        <li>Le locataire s'engage à restituer le matériel dans le même état d'origine.</li>
        <li>Tout dommage, casse ou restitution hors délai entraînera des frais supplémentaires (min 150 DT).</li>
        <li>L'utilisation du matériel est sous la seule responsabilité du locataire.</li>
    </ul>

    <!-- Signatures -->
    <div class="row pt-4 mt-4 border-top">
        <div class="col-6 text-center">
            <strong>Signature du Locataire (Client)</strong>
            <p class="small text-muted">Lu et approuvé</p>
            <div class="signature-box"></div>
        </div>
        <div class="col-6 text-center">
            <strong>Signature de l'Agent EquipLoc</strong>
            <p class="small text-muted">Pour l'entreprise EquipLoc</p>
            <div class="signature-box"></div>
        </div>
    </div>
</div>

</body>
</html>
