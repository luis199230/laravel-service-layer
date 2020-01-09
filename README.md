# Service Layer
![Packagist](https://img.shields.io/packagist/dm/luis199230/laravel-service-layer?style=plastic)
![GitHub stars](https://img.shields.io/github/stars/luis199230/laravel-service-layer?style=plastic)

This package allow generate service layer for abstraction the classes of the business logic and decoupling calls to models. 
My advice is use this layer of abstraction as intermediate between controllers and models.

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

## For Lumen and Laravel version without discovery package

### In file bootstrap/app.php (Lumen)

```sh
    $app->register(Madeweb\ServiceLayer\ServiceLayerProvider::class);
```

### In file config/app.php (Laravel without discovery package)
```sh
 'providers' => [
        /*
         * Custom Service Providers...
         */
        Madeweb\ServiceLayer\ServiceLayerProvider::class,
  ];
```
