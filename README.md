
# Programmation Avancée : Application Météo

---

Ce projet consiste à créer une application web météo basée sur des API permettant de récupérer et de traiter des données météorologiques, recenscant kes événements métérotologiques extrêmes tels que les tempêtes, les vents violents, variations de températures, les précipitations etc.

L'application fournit des alertes sur les conditions définies par l'utilisateur dans ses prférences. L'utilisateur peut y mentionner les villes/pays qui l'intéressent, ainsi que les seuils de température, la vitesse du vent etc.

---

## Commandes pour le lancement de l'application

0. Pré-requis : avoir installer PHP et ses variables d'environnements.

1. Cloner le projet sur votre machine

```bash
git clone https://github.com/Adjanouhoun/appmeteo.git
```

2. Ouvrir le terminal et accéder au dossier du projet

```bash
cd appmeteo
```

3. Créer la base de données

```bash
php bin/console doctrine:database:create
```

4. Créer la migration

```bash
php bin/console make:migration
```

5. Mettre la base de données à jour

```bash
php bin/console doctrine:migrations:migrate
```

6. Lancer le serveur

```bash
php bin/console server:start
```

Super ! Vous pouvez à présent profiter de l'application météo.

---

### Etudiants - M1 MIAGE groupe 1

- ADJANOUHOUN Amadou
- BA Ibrahima
- LIBEAUD Alexis


#### Remerciements

Nous tenons à remercier notre professeur Gilles Pierre Poirot de nous avoir encadrer et conseiller durant le module de Programmation Avancée.

README.md
Affichage de README.md en cours...
