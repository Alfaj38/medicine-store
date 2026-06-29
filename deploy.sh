#!/bin/bash

# Exit on any error
set -e

# --- CONFIGURATION ---
USER="meetvvxd"
HOST="68.65.123.215"
PORT="21098" # UPDATED: Standard Namecheap SSH Port
REMOTE_PATH="/home/$USER/public_html/apps/medicine_store"
# ---------------------

echo "🚀 Starting Deployment to Namecheap..."

# 1. Build Assets Locally
echo "📦 Building assets..."
npm run build

# 2. Optimization
echo "⚡ Preparing for deployment..."
rm -f public/hot

# 4. Create Tarball (Handles long paths perfectly)
echo "🤐 Creating deployment package (tar.gz)..."
tar -czf deploy.tar.gz \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='tests' \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='.idea' \
    --exclude='.phpunit.result.cache' \
    --exclude='.editorconfig' \
    --exclude='.styleci.yml' \
    --exclude='scratch*' \
    --exclude='check_*.php' \
    --exclude='deploy.tar.gz' \
    --exclude='./deploy.tar.gz' \
    --exclude='.env' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*.php' \
    --exclude='public/hot' \
    --exclude='public/.hot' \
    --exclude='.hot' \
    .

# 5. Upload & Extract
echo "📤 Uploading to $HOST on port $PORT..."
scp -P $PORT deploy.tar.gz $USER@$HOST:$REMOTE_PATH/

echo "📂 Performing fresh cleanup and extraction on server..."
ssh -p $PORT $USER@$HOST "
    # 1. Clean up old core files (Preserving .env and the fresh tarball)
    find $REMOTE_PATH -maxdepth 1 -mindepth 1 ! -name '.env' ! -name 'deploy.tar.gz' -exec rm -rf {} + &&
    cd $REMOTE_PATH &&
    
    # 2. Extract new files
    tar -xzf deploy.tar.gz &&
    rm deploy.tar.gz &&
    rm -f bootstrap/cache/*.php &&
    
    # 3. Install dependencies on server
    composer install --no-dev --optimize-autoloader &&
    
    # 4. Clean up root assets to ensure no stale files remain
    rm -rf ../../medicine_store/build ../../medicine_store/css ../../medicine_store/js &&
    
    # 5. Sync fresh public files
    rm -rf ../../medicine_store/storage &&
    rsync -av --exclude='index.php' public/ ../../medicine_store/ &&
    php artisan migrate --force &&
    php artisan db:seed --class=PermissionSeeder --force &&
    php artisan cache:clear &&
    php artisan optimize:clear &&
    php artisan optimize &&
    php artisan event:cache &&
    php artisan view:cache"

# 6. Cleanup local file
echo "🧹 Cleaning up..."
rm -f deploy.tar.gz

echo "✅ Deployment Successful!"