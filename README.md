### v0.1 - Use at your own risk

# CakePHP-Serializers

An object oriented solution to CakePHP model data serialization to JSON.

## Installation


### Git ###

``` bash
git clone git@github.com:loadsys/CakePHP-Serializers.git Plugin/Serializers
```

### Composer ###

* Add to your `composer.json` file

``` php
"require": {
  "loadsys/cakephp_serializers": "dev-master"
}
```

Load the plugin and be sure that bootstrap is set to true:

``` php
// Config/boostrap.php
CakePlugin::load('Serializers', array('bootstrap' => true));
```

## Usage

In controller

```
class AppController extends Controller {
  public $viewClass = 'Serializers.CakeSerializer';
}
```

