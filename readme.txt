================================================================================
  WEB SIEU RE — Ban theme website theo chu de (Laravel 12)
================================================================================

  Website ban giao dien / theme website theo tung nganh:
  ban hang, nha hang, spa, bat dong san, giao duc, landing page...

--------------------------------------------------------------------------------
1. YEU CAU
--------------------------------------------------------------------------------

  - PHP >= 8.2, Composer
  - XAMPP / Laragon (Apache + MySQL hoac SQLite)
  - Thu muc: C:\xampp\htdocs\websieure

--------------------------------------------------------------------------------
2. CAI DAT LAN DAU
--------------------------------------------------------------------------------

  cd C:\xampp\htdocs\websieure

  composer install          (neu chua co vendor)
  copy .env.example .env    (neu chua co .env)
  php artisan key:generate

  Tao database MySQL (XAMPP — bat MySQL truoc):
    CREATE DATABASE websieure CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  (hoac chay: C:\xampp\mysql\bin\mysql -u root -e "CREATE DATABASE IF NOT EXISTS websieure ...")

  Cau hinh .env (mac dinh da dung MySQL):
    DB_CONNECTION=mysql
    DB_DATABASE=websieure
    DB_USERNAME=root
    DB_PASSWORD=

  php artisan migrate --force
  php artisan db:seed --force

  Tao symlink anh (neu sau nay upload thumbnail):
    php artisan storage:link

--------------------------------------------------------------------------------
3. TRUY CAP
--------------------------------------------------------------------------------

  Laragon/XAMPP virtual host: http://websieure.test
  Hoac: http://localhost/websieure/public

  Trang chu       /
  Kho theme       /themes
  Chu de          /chu-de
  Admin           /admin/login

  Tai khoan admin (sau khi seed):
    Email:    admin@websieure.test
    Mat khau: password

  Doi trong .env:
    ADMIN_EMAIL=...
    ADMIN_PASSWORD=...

--------------------------------------------------------------------------------
4. CHUC NANG
--------------------------------------------------------------------------------

  Khach hang:
    - Duyet theme theo chu de
    - Tim kiem, loc gia
    - Xem chi tiet, demo (URL tuy chinh)
    - Dat mua (form lien he — admin xu ly thu cong)

  Admin (/admin):
    - Quan ly chu de (categories)
    - Quan ly theme (gia, sale, noi bat)
    - Quan ly don hang (cho TT / da TT / da giao)

--------------------------------------------------------------------------------
5. LENH HUU ICH
--------------------------------------------------------------------------------

  php artisan serve
  php artisan migrate:fresh --seed
  php artisan config:clear

================================================================================
