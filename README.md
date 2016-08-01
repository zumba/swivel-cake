# CakePHP Plugin for Zumba ***Swivel***

[Zumba Swivel](https://github.com/zumba/swivel) is a library that allows PHP applications to
manage features to multiple users via buckets. It consists with 10 buckets, allowing the same
code have up to 10 different behaviors.

This plugin is a bridge between CakePHP and Swivel. It provides a helper, component and
behavior classes to be used in your CakePHP application.

## Installation

You can install Swivel Cake into your project using [composer](http://getcomposer.org).
For existing applications you can add the following to your `composer.json` file:

```
    "require": {
        "zumba/swivel-cake": "1.*"
    }
```

And run `php composer.phar update`

## Loading the Plugin

After installing, you should tell your application to load the plugin:

```php
CakePlugin::load('Swivel', ['bootstrap' => true]);
```

## Configuration

The plugin has default configurations and is ready to use. However, you can customize
some of the configurations.

| Configuration | Default Value | Description |
| ------------- | ------------- | ----------- |
| Cookie.enabled | `true` | If cookie should be set at all |
| Cookie.name   | `Swivel_Bucket` | Cookie name used to store the client bucket number. |
| Cookie.expire | `0` | Expiration, in seconds, of the cookie. Setting 0 means a session cookie. |
| Cookie.path | `/` | Cookie's path. |
| Cookie.domain | `env('HTTP_HOST')` | Domain name used for the cookie. |
| Cookie.secure | `false` | If cookie can be only used in secure transmissions, ie. HTTPS |
| Cookie.httpOnly | `false` | If cookie can be accessed via other sources other than HTTP, ie. JavaScript |
| BucketIndex | `null` | Defines the user's bucket. Leaving null it will auto-generate a number between 1 and 10. |
| LoaderAlias | `SwivelManager` | Name that will be used to store the instance on Cake's `ClassRegistry`. |
| Logger | `null` | Instance to receive the logs. Setting to `null` will make the log be discarded. |
| Metrics | `null` | Metrics instance. |
| ModelAlias | `Swivel.SwivelFeature` | Model name that will provide the swivel mapping configuration. |

To set custom configurations, you need to create a file in `APP/Config/swivel.php` and set the fields you
want to override. Here is the default configuration file:

```php
<?php

$config = [
    'Swivel' => [
        'Cookie' => [
            'enabled' => true,
            'name' => 'Swivel_Bucket',
            'expire' => 0,
            'path' => '/',
            'domain' => env('HTTP_HOST'),
            'secure' => false,
            'httpOnly' => false
        ],
        'BucketIndex' => null,
        'LoaderAlias' => 'SwivelManager',
        'Logger' => null,
        'Metrics' => null,
        'ModelAlias' => 'Swivel.SwivelFeature',
    ]
];
```

Let's say for an example that you want to reserve one bucket for your testing and give the other 9
buckets to your customers, you can do something like this:
```php
<?php

// Saving bucket 1 for internal testing
$bucketIndex = isset($\_COOKIE['Swivel\_Bucket']) ? $\_COOKIE['Swivel\_Bucket'] : mt_rand(2, 10);

$config = [
    'Swivel' => [
        'BucketIndex' => $bucketIndex
    ]
];
```

## Loading Feature List

In order to Swivel to work, you need to specify which features are enabled for each bucket.

### Loading Feature via Database

The default behavior from swivel-cake is to load the features from database. This is done via
the built-in class `Swivel.SwivelFeature`. This class However expects the table `swivel_buckets`
to exist in your `default` database configuration.

This is the minimum table structure:
```sql
CREATE TABLE `swivel_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `buckets` varchar(20) NOT NULL DEFAULT '1,2,3,4,5,6,7,8,9,10',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_UNIQUE` (`slug`)
);
```

Feel free to add more fields if you want. For example, at Zumba we have the field modified
that automatically auto-update when we change the buckets configuration. If you rename one
of the pre-defined fields you will have to extends the plugin model and update accordingly.

Note the buckets are in a string field, separated by comma. You should not add spaces between
the numbers.

### Loading via Custom Source

You can also load the feature list from any other source, ie. from a webservice that you
use to share across all your apps.

In order to do that, create a model and make this model to implement `SwivelModelInterface`
interface. Example, I will call my model `MySwivelFeature`:

```php
<?php
App::uses('AppModel', 'Model');
App::uses('SwivelModelInterface', 'Swivel.Lib');

class SwivelFeature extends AppModel implements SwivelModelInterface {
    public $useTable = false;

    public function getMapData()
    {
        // @todo Load data from some source
        // @todo Format this data in a KEY/VALUE array, where KEY is the feature
        // and VALUE is the buckets in an array format, ie [1, 2, 3]
        // @todo Return the formatted data
        return [
            'FeatureA' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'FeatureB' => [1, 2, 3],
            'FeatureC' => [2, 4, 6, 8, 10],
        ];
    }
}
```

## Using

Either the component, behavior and helper implement the methods `forFeature` and `invoke`,
which are part of swivel. You can check the details of these methods in
[Swivel's documentation](https://github.com/zumba/swivel#zumbaswivelmanager).

To exemplify in CakePHP application:

```php
<?php

class UsersController extends AppController
{
    public $components = ['Swivel.Swivel'];
    public $uses = ['User', 'MyCoolWidget'];

    public function index()
    {
        $this->set('users', $this->User->find(/* ... */));
        $this->Swivel->invoke('MyCoolWidget', function() {
            return $this->set('widget', $this->MyCoolWidget->find(/* ... */));
        });
    }

    public function view($id = null)
    {
        $this->Swivel->forFeature('Redesign')
            ->addBehavior('userView', [$this, 'renderNewView'], [$id])
            ->defaultBehavior([$this, 'renderOldView'], [$id])
            ->execute();
    }

    protected function renderOldView($id)
    {
        // @todo implement
        $this->render('oldView');
    }

    protected function renderNewView($id)
    {
        // @todo implement
        $this->render('newView');
    }
}
```

This is just an example of how you can use the plugin in the controller, but
you can also use it on your models and views.
