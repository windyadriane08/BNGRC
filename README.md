# BNGRC - Système de Gestion des Dons et Besoins des Sinistrés

**Bureau National de Gestion des Risques et des Catastrophes**

## 📋 Description

Application web pour gérer les dons reçus, les besoins des villes sinistrées, et la répartition automatique des dons selon l'ordre chronologique (FIFO).

## 🎯 Fonctionnalités

- **Gestion des Villes** : Enregistrement des villes sinistrées avec leur région
- **Saisie des Besoins** : Définition des besoins par ville (nature, matériaux, argent) avec quantité et prix unitaire
- **Saisie des Dons** : Enregistrement des dons reçus par type de ressource
- **Dispatch Automatique** : Répartition automatique des dons aux villes selon l'ordre de saisie (FIFO)
- **Tableau de Bord** : Visualisation complète des statistiques, besoins, dons et attributions

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8.2
- **Framework** : Flight PHP (Micro-framework)
- **Base de données** : MySQL 8.0 (XAMPP)
- **Frontend** : HTML5, CSS3

## 🚀 Installation et Lancement

### Prérequis

- PHP 8.2 ou supérieur
- XAMPP (pour MySQL)
- Composer

### Étape 1 : Démarrer XAMPP MySQL

Lancez XAMPP et démarrez le service MySQL.

### Étape 2 : Installer les dépendances

```bash
composer install
```

### Étape 3 : Initialiser la base de données

```bash
php init_db.php
```

### Étape 4 : Lancer le serveur web

```bash
php -S localhost:8000 -t public
```

### Étape 5 : Accéder à l'application

Ouvrez votre navigateur : **http://localhost:8000**

## 📱 Pages de l'Application

| Page | Route | Description |
|------|-------|-------------|
| **Dashboard** | `/` | Tableau de bord principal |
| **Villes** | `/villes` | Gestion des villes |
| **Besoins** | `/besoins` | Liste des besoins |
| **Dons** | `/dons` | Liste des dons |
| **Dispatch** | `/dispatch` | Répartition automatique |

## 🔄 Fonctionnement du Dispatch

Le dispatch fonctionne selon le principe FIFO :
1. Les dons sont traités dans l'ordre de saisie
2. Attribution par type de ressource correspondant
3. Traçabilité complète des attributions

## 👥 Auteurs

**ETU004038 & ETU003901** - BNGRC 2026®
