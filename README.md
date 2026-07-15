# Reklama obyektlari reestri

Farg‘ona viloyatidagi tashqi reklama obyektlarini ro‘yxatga olish uchun Laravel 10 ilovasi.

Production: https://reklama.fargonainvestkompaniya.uz

## Imkoniyatlar

- Yuridik va jismoniy egalar, STIR/JShShIR hamda telefon validatsiyasi.
- Tuman, mahalla, ko‘cha turi, ko‘cha va uy raqami bo‘yicha manzil.
- 4 guruhga ajratilgan 15 turdagi reklama konstruksiyasi.
- Uzunlik × kenglik asosida avtomatik maydon hisoblash.
- Bitta xarita lokatsiyasi va bitta majburiy rasm.
- Ogohlantirish xati va ijaraga olish hujjati.
- Rollar, tuman bo‘yicha cheklovlar va audit tarixi.
- Monitoring va Excel eksport.

## Lokal ishga tushirish

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Serverda yangilash

```bash
cd /var/www/tutash-hudud
sudo ./deploy.sh
```
