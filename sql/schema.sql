-- Création de la table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'secretaire', 'medecin', 'admin') DEFAULT 'patient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Profil des patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    age INT,
    sexe ENUM('H', 'F'),
    telephone VARCHAR(20),
    profession VARCHAR(100),
    adresse TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Catégorie d’examens
CREATE TABLE examens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Sous-types d’examen
CREATE TABLE examens_sous_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    examen_id INT NOT NULL,
    nom VARCHAR(100),
    disponible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (examen_id) REFERENCES examens(id)
);

-- Plages horaires disponibles
CREATE TABLE plages_horaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    quota_max INT DEFAULT 0,
    quota_restant INT DEFAULT 0
);

-- Rendez-vous réservés
CREATE TABLE rendezvous (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    sous_type_id INT NOT NULL,
    plage_id INT NOT NULL,
    statut ENUM('en_attente', 'valide', 'rejete', 'termine', 'annule') DEFAULT 'en_attente',
    commentaire TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (sous_type_id) REFERENCES examens_sous_types(id),
    FOREIGN KEY (plage_id) REFERENCES plages_horaires(id)
);

-- Bulletin médical
CREATE TABLE bulletins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rendezvous_id INT NOT NULL,
    image_path VARCHAR(255),
    verifie BOOLEAN DEFAULT FALSE,
    remarque TEXT,
    FOREIGN KEY (rendezvous_id) REFERENCES rendezvous(id)
);

-- Paiement associé à un rendez-vous
CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rendezvous_id INT NOT NULL,
    montant DECIMAL(10,2),
    methode ENUM('momo', 'om', 'virement') DEFAULT 'momo',
    statut ENUM('en_attente', 'effectue', 'echoue') DEFAULT 'en_attente',
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rendezvous_id) REFERENCES rendezvous(id)
);

-- Notifications envoyées
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    type ENUM('rappel', 'imprevu'),
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
