# GovRight Corpus Utilities

Developer's suite for handling common small tasks like locale data extraction or GovRight Corpus API calls
in Wordpress themes and plugins.

## Requirements

Tested on:
* Wordpress >= 4.5
* PHP >= 5.5
* (dev) PHPUnit 4.8.24

## Installation

1. Download plugin archive from GitHub and put it to `wp-content/plugins`.
2. Activate the plugin on the Wordpress plugins page.
3. You may remove `bin` and `tests` folders, they are used only for plugin development.
4. You may use the [afragen/github-updater plugin](https://github.com/afragen/github-updater) to get recent updates.

## Plugin settings

Plugin has no settings in the admin. You can change Corpus API url used by plugin by adding the following constant
to your local `wp-config.php`:

```php
define('CORPUS_API_URL', 'http://localhost:3000/api');
```

## PHP API reference

### Performing API calls

```php
// Get Corpus API object
$api = corpus_get_api();
// Or like this, result is exactly the same
$api = CorpusAPI::getInstance();

// Now, you can call any corpus model name as a property or a method
// on the $api object to perform API calls.
// Singular/plural/lowercase/uppercase model name will work.

// These all are the same model
$model = $api->law;
$model = $api->Law;
$model = $api->laws;
$model = $api->Laws;

// Find in a set
$laws = $api->laws([
    'where' => [ 'defaultLocale' => 'en' ],
    'limit' => 3,
    'include' => [ 'nodes' ]
]);

// Find by id
$law = $api->law('567028d5219fffbb2d363f38');

// Find by id with a filter
$law = $api->law('567028d5219fffbb2d363f38', [
   'include' => [ 'user', 'discussions' ],
   'fields' => [ 'id', 'slug', 'locales' ]
]);

// Any available model method can called as a method on
// the model object like this
$law = $api->Laws->findOne([
    'where' => [
        'slug' => 'morocco-penal-revision'
    ]
]);
$count = $api->laws->count([
    'where' => [ 'slug' => 'morocco-penal-revision' ]
]);

// Call custom remote methods
$package = $api->law->package([
   'slug' => 'morocco-constitution-2011',
   'rev' => 0
]);

// Or use generic model `get()` method to make calls
$comparison = api->law->get([
   'method' => 'compare',
   'query' => [
       'slug' => 'morocco-penal-revision'
   ]
]);
```

### Helper functions

##### `corpus_get_api_url()`
Gets Corpus API url.

##### `corpus_get_api()`
Gets Corpus API object to perform API calls.

##### `corpus_get_locale($item, $languageCode = null)`
Extracts locale data from a model instance. If `$languageCode` specified - returns corresponding translations if available
or the first available otherwise. Has a support of the WPML plugin and tries to extract currently set language if `$languageCode`
is not specified.

##### `corpus_atts_string($atts, $include_locale = true)`
Converts `$atts` array into a string. Includes `data-locale` prop if `$include_locale` is `true`
and WPML is activated.

## JavaScript API reference

Plugin adds a global `GovRight` object which has the following properties/methods:

* `corpusApiUrl` - string, property that stores Corpus API url like `http://corpus.govright.org/api`

## Development

### Testing

#### Configure testing environment
```bash
# Go to plugin directory
cd wp-content/plugins/wp-corpus-utils

# Run wp tests installer and follow instructions
./bin/install-wp-tests.sh
```

Also, make sure you have PHPUnit installed.

#### Run tests
```bash
# In the plugin root, just run this
phpunit
```