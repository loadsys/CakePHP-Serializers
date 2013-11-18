# CakeSerializers

An object oriented solution to CakePHP model data serialization to JSON.

## Installation

Load the plugin and be sure that bootstrap is set to true:

``` php
// Config/boostrap.php
CakePlugin::load('CakeSerializers', array('bootstrap' => true));
```

## Usage

In controller

```
class AppController extends Controller {
  public $viewClass = 'CakeSerializers.CakeSerializer';
}
```

