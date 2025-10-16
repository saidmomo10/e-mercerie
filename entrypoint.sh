#!/bin/bash
set -e

echo "🚀 Lancement du conteneur Laravel..."

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Vérifie que PostgreSQL est bien la base configurée
echo "📦 Vérification du driver de base de données : $DB_CONNECTION"

if [ "$DB_CONNECTION" != "pgsql" ]; then
  echo "⚠️ Attention : DB_CONNECTION n'est pas 'pgsql'."
  echo "Vérifie ton render.yaml ou tes variables d'environnement."
  exit 1
fi

# Exécute les migrations
echo "🔄 Migration de la base de données..."
php artisan migrate --force || { echo "Erreur lors des migrations"; exit 1; }

# Seeders
echo "🌱 Exécution des seeders..."
php artisan db:seed --force || { echo "Erreur lors des seeders"; exit 1; }

# Démarre Apache
echo "✅ Démarrage du serveur Apache..."
exec apache2-foreground
