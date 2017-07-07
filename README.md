# Environment Detection

A simple library to detect the environment your application is currently executing in.

## Create a new Detector

```php
use LiftKit\EnvironmentDetection\Detector;

$detector = new Detector;
```

## Match HTTP_HOST

Match the value is `$_SERVER['HTTP_HOST']`. This value is passed to php from the web server as the domain the request was sent to.

```php
$detector->ifHttpHost('something.localhost', 'local')
	->ifHttpHost('test.something.com', 'test')
	->ifHttpHost('www.something.com', 'production')
	->ifHttpHost('*.something.com', 'subdomain')
	->ifHttpHost('*', 'default'); // if no other pattern matches
	
$environment = $detector->resolve();
```

`$environment` will equal one of `'development'`, `'test'`, `'production'`, '`subdomain`', or '`default`', depending on the host the application was accessed at. Note that `$_SERVER` will not be populated if the application was accessed via CLI. None of these will pass, and `$detector->resolve()` will return `null`.

## Match the hostname of the current machine

This tests against the output of the command `uname -n` (or the equivalent `php_uname('n')`).

```php
$detector->clear() // clear previous rules
	->ifHostName('*.local', 'local') // default pattern for macOS
	->ifHostName('*', 'default'); // will match all others

$environment = $detector->resolve();
```

## Match an environment variable

This test against a variable in `$_ENV`.

```php
$detector->clear() // clear previous rules
	->ifEnv('environment', 'dev', 'local') // tests $_ENV['development'] == 'dev'
	->ifEnv('environment', '*', 'default'); // will match all values of $_ENV['environment'], if $_ENV['environment'] is defined

$environment = $detector->resolve();
```

## Match an arbitrary value

```php
$detector->clear() // clear previous rules
	->ifMatch(php_uname('s'), 'Darwin', 'mac') // if macOS
	->ifMatch(php_uname('s'), 'Linux', 'linux'); // if Linux

$environment = $detector->resolve();
```

Note `php_uname('s')` reports the name of the operating system kernel.

## Match a boolean expression and defaults

```php
$detector->clear() // clear previous rules
	->ifBool(defined('TEST_ENVIRONMENT'), 'test')
	->defaultTo('production'); // default value

$environment = $detector->resolve();
```

## You can also mix and match conditions

```php
$detector->clear() // clear previous rules
	->ifMatch(php_sapi_name(), 'cli', 'cli') // in CLI
	->defaultTo('web'); // otherwise assume web request

$environment = $detector->resolve();
```

## No matches

`$detector->resolve()` will return null if there is no default and no condition matches.

```php
$detector->clear(); // clear previous rules

$environment = $detector->resolve(); // $environment === null
```
