# PHP Collection

[![Latest Stable Version](https://poser.pugx.org/fobia/php-object-collection/v/stable.svg)](https://packagist.org/packages/fobia/php-object-collection) [![Total Downloads](https://poser.pugx.org/fobia/php-object-collection/downloads.svg)](https://packagist.org/packages/fobia/php-object-collection) [![Latest Unstable Version](https://travis-ci.org/fobiaphp/php-object-collection.svg?branch=master)](https://packagist.org/packages/fobia/php-object-collection) [![License](https://poser.pugx.org/fobia/php-object-collection/license.svg)](https://packagist.org/packages/fobia/php-object-collection)

Колекция объектов. Позволяет работать сразу над всеми объектами, фильтравать, устанавливать и извлекать их свойства.


## Installation

PHP Object Collection can be installed with [Composer](http://getcomposer.org)
by adding it as a dependency to your project's composer.json file.

```json
{
    "require": {
        "fobia/collection": "*"
    }
}
```

Please refer to [Composer's documentation](https://github.com/composer/composer/blob/master/doc/00-intro.md#introduction)
for more detailed installation and usage instructions.


## Usage

#### eq 

Получить элемент по индексу

```php
$oc->eq();  // Первый элемент
$oc->eq(0); // эквивалентно
$oc->eq(1); // Второй элемент
```

#### find

Найти все элементы, параметр которых удовлетворяют услови. 
Возвращает ноый экземпляр колекции объектов.


Поиск объектов с существующим свойством

```php
$oc->find('Location');
```


Поиск объектов со свойством равным указаному значению

```php
$oc->find('Location', 'localhost/js');
```


Поиск объектов удавлетворяющие возврату функции

```php
$oc->find(function($obj, $key) {});
```


#### filter

Отфильтровать список объектов используя функции обратного вызова. В Функцию передаються объект  и его индекс. Все объекты на которые функция вернула ``false``, исключаються.
Возвращает объект текущей колекции

Отфильтрует так, что остануться те элементы, свойство ``id`` которых соответствуют индексу в колекции. Причем после фильтрации индексы сбрасываються.

```php
$oc->filter(function($object, $key) {
    return ($object->id == $key);   
});
```


#### each

Обходит весь масив, передавая функции объект, его индекс и дополнительные параметры. Если функция возвращает ``false``, обход останавливаеться. 
Возвращает объект текущей колекции

```php
$oc->each(function($object, $key) {});
```