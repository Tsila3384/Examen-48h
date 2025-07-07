
    <form action="<?= BASE_URL ?>/user/ajouterFond" method="post">
        <label for="dateAjout">Date d'ajout du fond: </label>
        <input type="date" name="dateAjout" id="dateAjout" placeholder="Date d'ajout" required>
        <label for="montant">Montant du fond: </label>
        <input type="number" name="montant" id="montant" placeholder="Montant à ajouter" required>
        <input type="submit" value="Ajouter">
    </form>
<script>
    const apiBase = "http://localhost<?= BASE_URL ?>";
    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
        };
        xhr.send(data);
    }
    function ajouterFond() {
        const dateAjout = document.getElementById('dateAjout').value;
        const montant = document.getElementById('montant').value;
        
        const data = `dateAjout=${encodeURIComponent(dateAjout)}&montant=${encodeURIComponent(montant)}`;
        ajax('POST', flight.base_url+'/user/ajouterFond', data, function(response) {
            if (response.success) {
                alert(response.message || 'Fonds ajoutés avec succès');
                // Réinitialiser le formulaire
                document.getElementById('dateAjout').value = '';
                document.getElementById('montant').value = '';
            } else {
                alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            ajouterFond();
        });
    });
    }
</script>