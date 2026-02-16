#!/bin/bash

# Script d'initialisation de la base de données BNGRC

echo "🏥 BNGRC - Initialisation de la Base de Données"
echo "================================================"

if [ -z "$DB_HOST" ]; then
    DB_HOST="localhost"
    DB_USER="root"
    DB_PASS=""
    DB_NAME="bngrc"
else
    # Utiliser les variables d'environnement Docker
    DB_USER="${DB_USER:-root}"
    DB_PASS="${DB_PASS:-root}"
    DB_NAME="${DB_NAME:-bngrc}"
fi

echo "✓ Paramètres de connexion:"
echo "  - Host: $DB_HOST"
echo "  - User: $DB_USER"
echo "  - Database: $DB_NAME"

# Attendre que MySQL soit disponible
echo ""
echo "⏳ Attente de la disponibilité de MySQL..."

for i in {1..30}; do
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" &> /dev/null; then
        echo "✓ MySQL est disponible!"
        break
    fi
    echo "  Tentative $i/30..."
    sleep 1
done

# Importer le schéma
echo ""
echo "📊 Importation du schéma de base de données..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" < database/schema.sql

if [ $? -eq 0 ]; then
    echo "✓ Base de données initialisée avec succès!"
else
    echo "✗ Erreur lors de l'importation du schéma"
    exit 1
fi

echo ""
echo "🎉 Initialisation terminée!"
echo "L'application est prête à être utilisée."
echo "URL: http://localhost:8000"
