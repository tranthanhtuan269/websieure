#!/usr/bin/env bash
set -euo pipefail

WP="sudo -u www-data wp --path=/var/www/banhang01 --allow-root"

echo "=== Kích hoạt theme ==="
$WP theme activate klever-child

echo "=== Cấu hình site ==="
$WP option update blogname "KLEVER FRUIT"
$WP option update blogdescription "Trái cây nhập khẩu cao cấp"
$WP rewrite structure '/%postname%/' --hard
$WP rewrite flush

# Tạo trang chủ nếu chưa có
PAGE_ID=$($WP post list --post_type=page --name=trang-chu --field=ID 2>/dev/null || true)
if [ -z "$PAGE_ID" ]; then
  PAGE_ID=$($WP post create --post_type=page --post_title="Trang chủ" --post_name=trang-chu --post_status=publish --porcelain)
fi
$WP option update show_on_front page
$WP option update page_on_front "$PAGE_ID"

echo "=== Tạo sản phẩm ==="

create_product() {
  local name="$1"
  local slug="$2"
  local regular="$3"
  local sale="$4"
  local origin="$5"
  local tag1="$6"
  local tag2="$7"
  local img_url="$8"

  local existing
  existing=$($WP post list --post_type=product --name="$slug" --field=ID 2>/dev/null || true)
  if [ -n "$existing" ]; then
    echo "Đã có: $name (ID $existing)"
    echo "$existing"
    return
  fi

  local pid
  pid=$($WP wc product create \
    --name="$name" \
    --slug="$slug" \
    --regular_price="$regular" \
    --sale_price="$sale" \
    --status=publish \
    --porcelain)

  $WP post meta update "$pid" _klever_origin "$origin"
  $WP post meta update "$pid" _klever_tag1 "$tag1"
  $WP post meta update "$pid" _klever_tag2 "$tag2"

  local att_id
  att_id=$($WP media import "$img_url" --porcelain 2>/dev/null || true)
  if [ -n "$att_id" ]; then
    $WP post meta update "$pid" _thumbnail_id "$att_id"
  fi

  echo "Tạo: $name (ID $pid)"
  echo "$pid"
}

create_product \
  "Việt Quất Mỹ" \
  "viet-quat-my" \
  "199000" \
  "159000" \
  "MỸ" \
  "Xuất xứ: Mỹ" \
  "Mọng nước, ngọt nhẹ" \
  "https://images.unsplash.com/photo-1498557850523-fd3d118b962e?w=600"

create_product \
  "Dưa Lê Vàng Hàn Quốc" \
  "dua-le-vang-han-quoc" \
  "890000" \
  "356000" \
  "HÀN QUỐC" \
  "Xuất xứ: Hàn Quốc" \
  "Giòn ngọt, thơm mát" \
  "https://images.unsplash.com/photo-1595475207225-428b62bda831?w=600"

create_product \
  "Cherry Mỹ" \
  "cherry-my" \
  "450000" \
  "360000" \
  "MỸ" \
  "Xuất xứ: Mỹ" \
  "Sắc đỏ mùa hè" \
  "https://images.unsplash.com/photo-1528821122594-5ca9533f8560?w=600"

create_product \
  "Nho Xanh Úc" \
  "nho-xanh-uc" \
  "320000" \
  "256000" \
  "ÚC" \
  "Xuất xứ: Úc" \
  "Crispy & sweet" \
  "https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=600"

create_product \
  "Táo Envy New Zealand" \
  "tao-envy-nz" \
  "280000" \
  "224000" \
  "NEW ZEALAND" \
  "Xuất xứ: NZ" \
  "Giòn, ngọt thanh" \
  "https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600"

create_product \
  "Kiwi Vàng New Zealand" \
  "kiwi-vang-nz" \
  "180000" \
  "144000" \
  "NEW ZEALAND" \
  "Xuất xứ: NZ" \
  "Vitamin C cao" \
  "https://images.unsplash.com/photo-1585059895524-3d48122745f0?w=600"

echo "=== Hoàn tất ==="
$WP theme list --status=active
$WP wc product list --format=table --fields=id,name,regular_price,sale_price
