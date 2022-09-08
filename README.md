# NEON &mdash; Attributable
Handles advanced attibutes related to any kind of models. The reason of this solution is the Neon CMS' best practice: If customer needs an option, like "show this e-mail on the company's page", we can add that e-mail, like an attribute via the admin UI and just handle the variable on template, and then, no programers needed to show anything on the given page.

## Requirements
* `"neon/model-uuid": "^1.0"`

## Install
Easily install the composer package:
```
composer require neon/attributable
```
Then you should install database migrations by:
```
php artisan vendor:publish --provider=\"Neon\\Attributables\\NeonAttributableServiceProvider\"
```

## Usage
Just use the Trait like othes:
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Neon\Attributables\Models\Traits\Attributable;
use Neon\Models\Traits\Uuid;

class AwesomeModel extends Model
{
    use Attributable;
    use Uuid;

    ...

}
```