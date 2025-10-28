<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

#Laravel Order Management System

A simple and clean Order Management System built with Laravel, designed to handle users, products, categories, and orders — with full admin and superadmin control and real-time notifications.

🚀 Overview

This project provides a backend API for managing product orders.
It allows users to browse and order products, while admins and superadmins can manage the platform’s data and users.

📦 Main Models & Relationships

🏷️ Category

Each category (e.g., Cosmetics) contains multiple products.

Relationship:
Category hasMany Products

🧴 Product

Belongs to one category.

Can appear in many orders.

Relationships:

Product belongsTo Category

Product belongsToMany Orders (via pivot table with quantity & price)

📦 Order

Represents a purchase made by a specific user.

Stores order details like date, total price, and status.

Relationships:

Order belongsTo User

Order belongsToMany Products

👤 User

Represents a client who can place multiple orders.

Relationship:
User hasMany Orders

🛠️ Roles & Permissions
👨‍💼 Admin

Can create, update, and delete categories and products.

Can view and manage all users’ orders.

Can update order statuses, triggering notifications to users.

🦸 Superadmin

Has all admin privileges, plus:

Can view and delete any user or admin.

Can add new admins.

Has full control over the system.
==========================================
👥 User

Can browse products and place orders.

Can view their own orders and receive updates when status changes.

🔔 Notifications

Users receive email + database notifications when:

A new order is created.

The status of an existing order is updated.

These notifications can also be fetched via API.

🧩 Tech Stack

Backend: Laravel 12

Database: MySQL

Authentication: Laravel Sanctum (Token-based)

Notifications: Mail & Database

API Testing: Postman

📁 Example Data

Category: Cosmetics
Products: Body Lotion, Body Splash, Skin Care
User: Mohamed
Order: Body Lotion → status updated → user notified

🔑 Admin Roles Example
Role	Capabilities
Admin	Manage products, categories, orders
Superadmin	Manage admins, users, and system settings

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
