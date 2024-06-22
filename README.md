<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

# Backend Server for Food Delivery

sebuah RESRFULL API untuk aplikasi Food Delivery, untuk target frontend menggunakan Flutter. Project Idea from Bootcamp FIC Bacth 18 dan di modif sendiri oleh daffa alif (myself XD), for better rest api. Untuk dokumentasi menggunakan OPENAPI

## Improve Feature BE

1. build with OPENAPI, terdapat dokumen untuk tiap route api. lokasi ada di `docs/`. [lihat caranya](#dokumentasi-route-api)
2. build with Test Case schema
3. build with log viewer, semua aktivitas terekam di dalam log
4. relation database schema
5. clear syntax

## Setup Project

untuk yang mau ngeclone project

```
git clone https://github.com/Lemonade-honey/FIC18-BE-Food-Delivery.git
```

Optional, hapus git history

```
rm .git
```

setelah itu buat environtmen project dan setup database di env

```
copy .env.example .env
```

setelah semua tersetting di env, migrasikan database.

```
php artisan migrate
```

jalankan server

```
php artisan serve
```

jalankan test untuk mengecek project

```
php artisan test
```

## Dokumentasi Route API

untuk melihat dokumentasi Route API, pastikan sudah memiliki extension **OPENAPI (Swagger)** dapat di [download disini](https://marketplace.visualstudio.com/items?itemName=42Crunch.vscode-openapi). Lokasi dokumentasi ada di folder `docs`

untuk melihat route secara tampilan dapat [dilihat disini](https://marketplace.visualstudio.com/items?itemName=42Crunch.vscode-openapi#preview-openapi-documentation)
