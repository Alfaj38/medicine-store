#!/bin/bash
# ==========================================
# SaaSMedi Regular Deployment Script
# ==========================================

echo "🚀 Starting Deployment..."

# 1. Enter Maintenance Mode
php artisan down || true

# 2. Pull the latest code
echo "📥 Pulling latest code..."
git pull origin master --force

# 3. Install/Update Dependencies
echo "📦 Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Clear and Cache Configurations
echo "🧹 Rebuilding caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run Database Migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 6. Bring Application Live
php artisan up

echo "🎉 Deployment Completed Successfully!"
