# Storage Setup (Shared Hosting)

Paylaşımlı hostingde symlink (public/storage) kısıtlı olduğu için depolama doğrudan `public_html/storage` klasörüne yönlendirildi.

## Yapılandırma
- `config/filesystems.php` → `public` diski docroot’a alındı:
  - `root => env('FILESYSTEM_PUBLIC_ROOT', public_path('storage'))`
  - `url  => env('FILESYSTEM_PUBLIC_URL', env('APP_URL').'/storage')`
- `.env` örnekleri:
  ```
  FILESYSTEM_DISK=public
  FILESYSTEM_PUBLIC_ROOT=/home/takvadergisi/public_html/storage
  FILESYSTEM_PUBLIC_URL=https://takvadergisi.org/storage
  APP_URL=https://takvadergisi.org
  ```
- Config cache: `php artisan config:clear` (cron ile bir kerelik de olabilir).

## Klasörler ve izinler
- Depolama kökü: `public_html/storage` (gerçek klasör, symlink değil).
- İzinler: klasörler 755, dosyalar 644.
- Eski dosyalar: `laravel/storage/app/public` ve `laravel/public/storage` içindekiler, klasör yapısı bozulmadan `public_html/storage` altına taşındı.

## Test
- Örnek dosya: `public_html/storage/ping.txt` → `https://takvadergisi.org/storage/ping.txt` 200 dönmeli.
- Yeni upload’lar doğrudan `public_html/storage` altına yazılır ve `/storage/...` ile erişilir.

## Not
- Symlink’e gerek yok; host symlink izni açılmadığı sürece bu yapı en sorunsuz yaklaşım. İş bittiyse geçici `/storage-check` route’unu kaldırın.
