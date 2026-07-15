# Reklama obyektlari reestri

Production: https://reklama.fargonainvestkompaniya.uz

## Serverda yangilash

```bash
cd /var/www/tutash-hudud
./deploy.sh
```

Laravel Blade asosidagi arizalar reestri. Loyiha kadastr arizalarini yaratish, tahrirlash, ko'rish, fayl/rasm yuklash, xaritada poligon belgilash va audit tarixini yuritish uchun ishlatiladi.

## Local tekshiruv

```bash
composer install
npm install
php artisan migrate --seed
php artisan storage:link
php artisan test
npm run build
```

Windows PowerShell `npm.ps1`ni bloklasa:

```bash
npm.cmd install
npm.cmd run build
```

## Production checklist

Serverdagi `.env` qiymatlarini sozlang:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.uz
LOG_LEVEL=warning
SESSION_LIFETIME=480
FILESYSTEM_DISK=local
```

Deploy tartibi:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan optimize
```

Muhim: `.env` to'g'ri production qiymatlariga sozlanmaguncha `php artisan optimize` ishlatmang. Aks holda noto'g'ri cached config session/CSRF yoki URL muammolariga olib kelishi mumkin.

## Tekshirilgan oqimlar

- Login va login throttle.
- Ariza yaratish.
- Ariza tahrirlash.
- Ariza o'chirish va media fayllarni diskdan tozalash.
- 4 ta noyob rasm validatsiyasi.
- Akt fayli optional.
- Hokimiyat kadastri optional.
- Umumiy maydon server tomonda `uzunlik * kenglik` bo'yicha qayta hisoblanadi.
- Session keep-alive va session tugaganda reload banner.
- Tuman foydalanuvchisi faqat o'z hududi bilan ishlashi.

## Browser checklist

- Telegram Android WebView.
- Telegram iOS WebView.
- Chrome Android va desktop.
- Safari iOS/macOS.
- Firefox Android va desktop.
- Microsoft Edge.

Tekshiriladigan sahifalar: `/login`, `/requests`, `/requests/create`, `/requests/{id}`, `/addresses`, `/users/online`.
