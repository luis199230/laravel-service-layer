# Service Layer
![Packagist](https://img.shields.io/packagist/dm/luis199230/service-layer?style=plastic)
![GitHub stars](https://img.shields.io/github/stars/luis199230/service-layer?style=plastic)

This package allow generate service layer for abstract classes of business logic and decoupling calls to models. 
The advice is use this layer of abstraction how to intermediate between controllers and models.

## Installation 

```sh
composer require madeweb/service-layer
```

Generate service base class and custom service class

```sh
php artisan make:service ServiceName  
```

Generate service base class and custom service class with custom model 

```sh
php artisan make:service ServiceName --model=App\\Models\\ModelName
```
