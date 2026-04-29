# Hostinger (laravel + public_html) GitHub Auto Deploy

Bu akış sizin mevcut yapınız için hazırlandı:

- Laravel kaynak kodu: `/home/<user>/laravel`
- Web kökü: `/home/<user>/public_html`

`main` branch'e her push'ta GitHub Actions:

1. Vite build alır (`npm run build`)
2. Projeyi `laravel` klasörüne sync eder
3. `public/` içeriğini `public_html` klasörüne sync eder
4. Hostinger'a özel `index.php` dosyasını yükler
5. Sunucuda `composer install` + `artisan optimize` çalıştırır

## 1) GitHub Secrets

Repository -> Settings -> Secrets and variables -> Actions -> New repository secret:

- `HOSTINGER_SSH_HOST` -> SSH host (IP ya da host adı)
- `HOSTINGER_SSH_PORT` -> SSH port (Hostinger SSH Access ekranındaki port)
- `HOSTINGER_SSH_USER` -> SSH user (örn: `u976366413`)
- `HOSTINGER_SSH_KEY` -> Private key (GitHub Actions için)
- `HOSTINGER_APP_PATH` -> Laravel path (örn: `/home/u976366413/laravel`)
- `HOSTINGER_PUBLIC_PATH` -> Public path (örn: `/home/u976366413/public_html`)
- `HOSTINGER_ENV_FILE_B64` (opsiyonel) -> `.env` dosyasının base64 hali (set edilirse her deploy'da `.env` bununla güncellenir)

Opsiyonel değişken:

- `RUN_MIGRATIONS=true` (Repository -> Settings -> Secrets and variables -> Variables)

## 2) Sunucuda bir kere yapılacaklar

1. `laravel` klasörünün var olduğundan emin olun.
2. `.env` dosyası sunucuda `laravel/.env` altında mevcut olmalı.
3. Veritabanı ayarları `.env` içinde production değerlerinde olmalı.
4. `public_html/storage` klasörünü deploy silmemesi için bu akışta `storage/` hariç tutulur.

## 3) Çalıştırma

1. `main` branch'e push yapın.
2. GitHub -> Actions -> `Deploy to Hostinger` job'unu takip edin.
3. İlk deploy sonrası siteyi test edin.

## .env hatası için hızlı çözüm

Eğer log'da `ERROR: .env not found` görürseniz:

1. Ya sunucuda manuel `laravel/.env` oluşturun
2. Ya da localde şu komutla base64 üretip `HOSTINGER_ENV_FILE_B64` secret'ına koyun:

```bash
base64 -w 0 .env
```

macOS için:

```bash
base64 .env | tr -d '\n'
```

## Notlar

- Hostinger panelindeki Git Deploy özelliğini bu akışla aynı anda kullanmayın; iki farklı sistem aynı dosyaları yönetmesin.
- `public/hot` dosyası build sırasında silinir; production'da kalmamalı.
- Deploy logunda `.env` içinden `DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME` önizlemesi yazdırılır; hangi dosyanın okunduğunu doğrulamak için.
- Deploy sırasında `cache:clear` çağrısı atlanır; `CACHE_STORE=database` kullanılırken eski DB bilgileriyle hata döngüsüne girmesin diye.
