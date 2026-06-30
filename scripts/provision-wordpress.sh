#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${1:?domain required}"
ADMIN_EMAIL="${2:?admin email required}"
SITES_PATH="${3:-/var/www}"
SERVER_IP="${4:-}"
SITE_FOLDER="${5:-${SITE_FOLDER:-}}"

DB_NAME="wp_$(echo "$DOMAIN" | tr '.-' '__' | cut -c1-48)"
DB_USER="${DB_NAME}_u"
DB_PASS="$(openssl rand -base64 24 | tr -dc 'a-zA-Z0-9' | head -c 24)"
WP_ADMIN_USER="${WP_ADMIN_USER:-admin}"
WP_ADMIN_PASS="$(openssl rand -base64 18 | tr -dc 'a-zA-Z0-9' | head -c 16)"
SITE_DIR="${SITES_PATH}/${SITE_FOLDER:-$DOMAIN}"
NGINX_SITE="${DOMAIN}"

export DEBIAN_FRONTEND=noninteractive

if ! command -v wp >/dev/null 2>&1; then
  curl -sS https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp
  chmod +x /usr/local/bin/wp
fi

mkdir -p "$SITE_DIR"
chown -R www-data:www-data "$SITE_DIR"

mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

if [ ! -f "${SITE_DIR}/wp-config.php" ]; then
  sudo -u www-data wp core download --path="$SITE_DIR" --locale=vi --quiet
  sudo -u www-data wp config create \
    --path="$SITE_DIR" \
    --dbname="$DB_NAME" \
    --dbuser="$DB_USER" \
    --dbpass="$DB_PASS" \
    --dbhost=localhost \
    --skip-check
  sudo -u www-data wp core install \
    --path="$SITE_DIR" \
    --url="https://${DOMAIN}" \
    --title="${DOMAIN}" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email
fi

PHP_SOCK=$(ls /run/php/php*-fpm.sock 2>/dev/null | head -1)

cat > "/etc/nginx/sites-available/${NGINX_SITE}" <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root ${SITE_DIR};
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    client_max_body_size 64M;
}
NGINX

ln -sf "/etc/nginx/sites-available/${NGINX_SITE}" "/etc/nginx/sites-enabled/${NGINX_SITE}"
nginx -t
systemctl reload nginx

if command -v certbot >/dev/null 2>&1; then
  certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos \
    --email "$ADMIN_EMAIL" --redirect 2>/dev/null || true
fi

chown -R www-data:www-data "$SITE_DIR"

echo "PROVISION_OK"
echo "SITE_URL=https://${DOMAIN}"
echo "WP_ADMIN_USER=${WP_ADMIN_USER}"
echo "WP_ADMIN_PASS=${WP_ADMIN_PASS}"
echo "DB_NAME=${DB_NAME}"
