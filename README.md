# PHP SimpleRouter


## Install

Install via [composer](https://getcomposer.org):

```javascript
{
    "require": {
        "anoxgh/simplerouter": "dev-master"
    }
}
```

Run `composer install` then use as normal:


## Usage
```php

use AnoxGH\SimpleRouter\Router;

Router::get('/', function ()
{
    echo "Homepage (GET)";
});

Router::put('/entry/', function ()
{
    echo "Create Entry";
});

Router::post('/entry/(:num)', function ($id)
{
    echo "Update Entry:".$id;
});

Router::delete('/entry/(:num)', function ($id)
{
    echo 'Delete Entry:' . $id;
});

Router::error(function ()
{
    echo "404 Error";
});

Router::dispatch();
```