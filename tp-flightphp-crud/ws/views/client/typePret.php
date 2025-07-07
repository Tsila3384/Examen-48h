<div class="container">
    <h2>Types de prêt disponibles</h2>
    
    <?php if (!empty($types)): ?>
    
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Taux d'intérêt</th>
                    <th>Durée maximale</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?= htmlspecialchars($type['nom']) ?></td>
                        <td><?= htmlspecialchars($type['taux_interet']) ?>%</td>
                        <td><?= htmlspecialchars($type['duree_max']) ?> mois</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aucun type de prêt disponible pour votre profil.</div>
    <?php endif; ?>
</div>