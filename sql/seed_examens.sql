-- Insertion des examens
INSERT INTO examens (nom) VALUES
('Écho'),
('Scanner'),
('IRM'),
('Radio');

-- Insertion des sous-types d’Écho
INSERT INTO examens_sous_types (examen_id, nom) VALUES
(1, 'Abdominale'),
(1, 'Pelvienne & uro-génitale'),
(1, 'Obstétricale'),
(1, 'Mammaire'),
(1, 'Thyroïdienne et cervicale'),
(1, 'Vasculaire (Doppler)'),
(1, 'Endovaginale'),
(1, 'Hystérosonographie'),
(1, 'Transfontanellaire'),
(1, 'Autre…');

-- Insertion des sous-types de Scanner
INSERT INTO examens_sous_types (examen_id, nom) VALUES
(2, 'Cérébrale'),
(2, 'Faciale et cervicale'),
(2, 'Thoracique'),
(2, 'Angioscanner'),
(2, 'Cardiaque'),
(2, 'Coroscanner'),
(2, 'Uroscanner'),
(2, 'Abdominale'),
(2, 'ThoracoAbdominoPelvien_TAP'),
(2, 'Lombaire'),
(2, 'Dorsale'),
(2, 'Rocher'),
(2, 'Membres'),
(2, 'Autre…');

-- Insertion des sous-types de IRM
INSERT INTO examens_sous_types (examen_id, nom) VALUES
(3, 'Rachis cervicale'),
(3, 'Dorsale'),
(3, 'Lombaire'),
(3, 'Rachis entier'),
(3, 'Membres inférieures/supérieures'),
(3, 'Angio-IRM'),
(3, 'Autre…');

-- Insertion des sous-types de Radio
INSERT INTO examens_sous_types (examen_id, nom) VALUES
(4, 'Thorax'),
(4, 'Gril costal'),
(4, 'Rachis (dorsal, lombaire, cervicale)'),
(4, 'Membres inférieures/supérieures');
