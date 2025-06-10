<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accueil - EASY IMAGERIE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.45);
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-shadow: 0 2px 8px #000;
    }
    .carousel-container {
        position: relative;
        height: 60vh;
        min-height: 350px;
        max-height: 500px;
        overflow: hidden;
    }
    .carousel-inner img {
        filter: brightness(0.6);
        object-fit: cover;
        height: 60vh;
        min-height: 350px;
        max-height: 500px;
    }
    .btn-glow {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 0 10px #0d6efd, 0 0 20px #0d6efd;
        animation: glow 1.5s infinite alternate;
        border: none;
    }
    @keyframes glow {
        from { box-shadow: 0 0 10px #0d6efd, 0 0 20px #0d6efd; }
        to { box-shadow: 0 0 30px #0d6efd, 0 0 60px #0d6efd; }
    }
</style>
</head>
<body class="bg-light">

<header class="bg-white shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
            <img src="assets/logo.svg" alt="Logo" height="50" class="me-2">
            <span class="fs-4 fw-bold text-primary">Easy ImAgInG</span>
        </div>
        <nav>
            <a href="profil.php" class="btn btn-outline-primary me-2"><i class="fas fa-user"></i> Mon Profile </a>
            <a href="login.php" class="btn btn-outline-primary me-2"><i class="fas fa-sign-in-alt"></i> Connexion</a>
            <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Inscription</a>
        </nav>
    </div>
</header>

<main>
    <!-- Carrousel avec overlay de bienvenue -->
    <div class="carousel-container mb-5">
        <div id="carouselAccueil" class="carousel slide h-100" data-bs-ride="carousel">
            <div class="carousel-inner h-100">
                <div class="carousel-item active h-100 position-relative">
                    <img src="assets/scanner.jpg" class="d-block w-100 h-100" alt="Examens modernes">
                    <div class="hero-overlay text-center">
                        <h1 class="display-4 fw-bold mb-3">Bienvenue sur la plateforme<br>de prise de rendez-vous en imagerie médicale</h1>
                        <p class="lead mb-4">Réservez vos examens en ligne, accédez à vos résultats et bénéficiez de l’expertise de notre équipe.</p>
                        <a href="register.php" class="btn btn-glow btn-lg"><i class="fas fa-calendar-plus"></i> Prendre un rendez-vous</a>
                    </div>
                </div>
                <div class="carousel-item h-100">
                    <img src="assets/radio.jpg" class="d-block w-100 h-100" alt="Equipe médicale">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h4>Expert & Resultat  </h4>
                        <p>Vous etes la priorité et faite valoir notre metier.</p>
                    </div>
                </div>
                <div class="carousel-item h-100">
                    <img src="assets/scanner.jpg" class="d-block w-100 h-100" alt="Equipe médicale">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h4>Appareils recents & Resultat Certifier  </h4>
                        <p>Resultat Rapide et Fiable.</p>
                    </div>
                </div>
                <div class="carousel-item h-100">
                    <img src="assets/teamwork.gif" class="d-block w-100 h-100" alt="Sécurité">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h5>Sécurité & Confidentialité</h5>
                        <p>Vos données et résultats sont protégés et accessibles en toute sécurité.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselAccueil" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselAccueil" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
            </button>
        </div>
    </div>

    <!-- Section expertise -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <h1 class="display-5 fw-bold text-primary">Votre santé, notre expertise</h1>
                <p class="lead">Notre centre d’imagerie médicale met à votre disposition une équipe pluridisciplinaire, des équipements de dernière génération et un accueil chaleureux pour garantir la qualité de vos examens et la rapidité de vos résultats.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check-circle text-success me-2"></i> Prise de rendez-vous en ligne simple et rapide</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i> Résultats sécurisés et accessibles 24/7</li>
                    <li><i class="fas fa-check-circle text-success me-2"></i> Conseils personnalisés et suivi médical</li>
                </ul>
                <a href="register.php" class="btn btn-primary btn-lg mt-3"><i class="fas fa-calendar-plus"></i> Prendre rendez-vous</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/stetocope.jpg" alt="Imagerie" class="img-fluid rounded shadow">
            </div>
        </div>
    </section>

    <!-- Témoignages -->
    <section class="bg-white py-5">
            <div class="container">
                <h2 class="text-center mb-4">Ils nous font confiance</h2>
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Accueil chaleureux, prise en charge rapide et résultats disponibles le jour même. Je recommande vivement !”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Deffo Daryl.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Des professionnels à l’écoute et un service très sécurisé. J’ai pu accéder à mes résultats en ligne facilement.”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Ali Baba.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Plateforme intuitive et équipe très professionnelle. Merci pour votre accompagnement !”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Onan Otto.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Références et chiffres clés -->
    <section class="container py-5">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <i class="fas fa-users fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">+10 000</h3>
                    <p>Patients accompagnés</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-x-ray fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">15</h3>
                    <p>Types d’examens proposés</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-award fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">98%</h3>
                    <p>Satisfaction patients</p>
                </div>
            </div>
        </section>
</main>

<footer class="bg-primary text-white text-center py-3 mt-5">
    &copy; 2025 Easy Imagerie. Tous droits réservés @william.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



 <!-- Témoignages -->
 <!-- <section class="bg-white py-5">
            <div class="container">
                <h2 class="text-center mb-4">Ils nous font confiance</h2>
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Accueil chaleureux, prise en charge rapide et résultats disponibles le jour même. Je recommande vivement !”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Marie D.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Des professionnels à l’écoute et un service très sécurisé. J’ai pu accéder à mes résultats en ligne facilement.”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Ali B.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">“Plateforme intuitive et équipe très professionnelle. Merci pour votre accompagnement !”</p>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                    <span>Sophie L.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->



 <!-- Références et chiffres clés -->
 <!-- <section class="container py-5">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <i class="fas fa-users fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">+10 000</h3>
                    <p>Patients accompagnés</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-x-ray fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">15</h3>
                    <p>Types d’examens proposés</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-award fa-3x text-primary mb-2"></i>
                    <h3 class="fw-bold">98%</h3>
                    <p>Satisfaction patients</p>
                </div>
            </div>
        </section> -->