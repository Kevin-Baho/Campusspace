#!/usr/bin/env bash
# Menerapkan overlay CampusSpace ke proyek Laravel. Jalankan dari ROOT proyek Laravel:
#   bash /path/ke/campusspace/install.sh
set -euo pipefail
OVERLAY="$(cd "$(dirname "$0")" && pwd)"
[ -f artisan ] || { echo "Jalankan dari root proyek Laravel (artisan tidak ditemukan)."; exit 1; }

echo "1) Paket Composer"
composer show laravel/breeze >/dev/null 2>&1 || { composer require laravel/breeze --dev && php artisan breeze:install react --no-interaction; }
composer require simplesoftwareio/simple-qrcode

echo "2) Paket npm"
npm install lucide-react html5-qrcode

echo "3) Salin overlay (cadangan file yang ditimpa disimpan di .campusspace-backup/)"
mkdir -p .campusspace-backup
( cd "$OVERLAY" && find app database resources routes tests Dockerfile Caddyfile vercel.json .dockerignore -type f ) | while read -r f; do
  if [ -f "$f" ]; then mkdir -p ".campusspace-backup/$(dirname "$f")"; cp "$f" ".campusspace-backup/$f"; fi
  mkdir -p "$(dirname "$f")"; cp "$OVERLAY/$f" "$f"
done
cp -n "$OVERLAY/config/services.campusspace.php" config/services.campusspace.php || true
echo
echo "SELESAI. Langkah manual berikutnya ada di README.md (bagian 2: services.php, .env, dark mode, migrate)."
