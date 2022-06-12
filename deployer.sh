set -e

echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true
    # Update codebase
    git add .
    git commit -m 'merge'
    git pull origin main
# Exit maintenance mode
php artisan up

echo "Application deployed!"