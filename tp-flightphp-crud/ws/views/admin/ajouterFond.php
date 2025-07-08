<form id="ajouterFondForm" action="/user/ajouterFond" method="post">
    <div class="form-group">
        <label for="dateAjout">
            <i class="fas fa-calendar-alt"></i>
            Date d'ajout du fond
        </label>
        <div class="input-wrapper">
            <i class="fas fa-calendar-alt"></i>
            <input
                type="date"
                name="dateAjout"
                id="dateAjout"
                class="form-control"
                required
                max="">
        </div>
        <div class="error-message">Veuillez sélectionner une date valide</div>
    </div>

    <div class="form-group">
        <label for="montant">
            <i class="fas fa-euro-sign"></i>
            Montant du fond
        </label>
        <div class="input-wrapper">
            <i class="fas fa-euro-sign"></i>
            <input
                type="number"
                name="montant"
                id="montant"
                class="form-control"
                placeholder="0.00"
                step="0.01"
                min="0.01"
                required>
        </div>
        <div class="error-message">Le montant doit être supérieur à 0</div>
    </div>

    <button type="submit" class="btn" id="submitBtn">
        <i class="fas fa-plus-circle"></i>
        <span class="btn-text">Ajouter les fonds</span>
        <i class="fas fa-spinner loading-spinner"></i>
    </button>
</form>
<script>
    const apiBase = "http://localhost<?= BASE_URL ?>";

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            ajouterFond();
        });
    });

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
        ajax('POST', '/user/ajouterFond', data, function(response) {
            if (response.success) {
                alert('Fonds ajoutés avec succès');
                // Réinitialiser le formulaire
                document.getElementById('dateAjout').value = '';
                document.getElementById('montant').value = '';
            } else {
                alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
            }
        });
    }
</script>
<style>
    :root {
        --primary-color: #273267;
        --primary-light: #3a4785;
        --accent-color: #5a67c4;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-gray: #f8f9fa;
        --medium-gray: #6c757d;
        --dark-gray: #343a40;
        --white: #ffffff;
        --shadow: 0 4px 20px rgba(39, 50, 103, 0.1);
        --shadow-hover: 0 8px 30px rgba(39, 50, 103, 0.15);
        --border-radius: 12px;
        --transition: all 0.3s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, var(--light-gray) 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .container {
        max-width: 500px;
        width: 100%;
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transform: translateY(0);
        transition: var(--transition);
    }

    .container:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .form-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        padding: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .form-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="20" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="30" cy="80" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="70" cy="70" r="2.5" fill="rgba(255,255,255,0.1)"/></svg>');
        opacity: 0.3;
    }

    .form-header h1 {
        font-size: 2rem;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .form-header p {
        opacity: 0.9;
        font-size: 1.1rem;
        position: relative;
        z-index: 1;
    }

    .form-header i {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.8;
    }

    .form-content {
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--dark-gray);
        font-size: 0.95rem;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper i {
        position: absolute;
        left: 15px;
        color: var(--medium-gray);
        z-index: 1;
        font-size: 1.1rem;
    }

    .form-control {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e9ecef;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
        background: var(--white);
        color: var(--dark-gray);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(90, 103, 196, 0.1);
        transform: translateY(-1px);
    }

    .form-control:valid {
        border-color: var(--success-color);
    }

    .btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--white);
        border: none;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39, 50, 103, 0.3);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn i {
        margin-right: 8px;
    }

    .loading-spinner {
        display: none;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: var(--border-radius);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert i {
        font-size: 1.2rem;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-footer {
        background: var(--light-gray);
        padding: 20px 40px;
        text-align: center;
        border-top: 1px solid #e9ecef;
    }

    .form-footer p {
        color: var(--medium-gray);
        font-size: 0.9rem;
    }

    .form-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .form-footer a:hover {
        color: var(--accent-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            margin: 10px;
        }

        .form-content {
            padding: 30px 20px;
        }

        .form-header {
            padding: 25px 20px;
        }

        .form-header h1 {
            font-size: 1.8rem;
        }

        .form-footer {
            padding: 20px;
        }
    }

    /* Animation d'entrée */
    .container {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Validation visuelle */
    .form-group.has-error .form-control {
        border-color: var(--danger-color);
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .form-group.has-success .form-control {
        border-color: var(--success-color);
    }

    .error-message {
        color: var(--danger-color);
        font-size: 0.85rem;
        margin-top: 5px;
        display: none;
    }

    .form-group.has-error .error-message {
        display: block;
    }
</style>