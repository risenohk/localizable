## Laravel Localizable
[![Latest Stable Version](https://poser.pugx.org/riseno/localizable/v/stable)](https://packagist.org/packages/riseno/localizable) [![Total Downloads](https://poser.pugx.org/riseno/localizable/downloads)](https://packagist.org/packages/riseno/localizable) [![License](https://poser.pugx.org/riseno/localizable/license)](https://packagist.org/packages/riseno/localizable)

A small plugin for managing multi-language in database table.

### Installation

Require this package with composer using the following command:

```bash
composer require riseno/localizable
```

After updating composer, add the service provider to the `providers` array in `config/app.php`

```php
Riseno\Localizable\LocalizableServiceProvider::class,
```

Run artisan command to generate the database migration file

```bash
php artisan riseno:localizable:generate user
```

After generated migration file, open the migration file. Add any column that should be localized.

```php
Schema::create('user_localizations', function(Blueprint $table)
{
	$table->increments('id');
	$table->unsignedInteger('user_id');
	$table->string('locale');
	$table->boolean('default')->default(false);
	$table->string('name')->nullable();
	$table->timestamps();
	$table->foreign('user_id')->references('id')->on('users');
});
```

Once all the columns is added to migration file, run migrate command

```bash
php artisan migrate
```

Go to the model that you want to implement localizable function, and add this trait to the class

```php
use Riseno\Localizable\LocalizableTrait;

class User extends Authenticatable
{
    use LocalizableTrait;
```

And also two required properties

```php
protected $localizeModel  = UserLocalizations::class;
protected $localizeFields = ['name'];
```

One more thing, add a localizations method

```php
public function localizations()
{
    return $this->hasMany(UserLocalizations::class, 'user_id', 'id');
}
```

### Usage

The trait has provided a generic method for accessing the localized data,

```php
$user->localize('en_US');
// or access value directly
$user->localize('en_US')->name;
```

If you want to save / update the localized record

```php
$user->saveLocalize('en_US', ['name' => 'Riseno']);
```

### License

The Laravel Localizable is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)