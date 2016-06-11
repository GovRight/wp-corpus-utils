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
3. You may remove `bin` and `tests` folders, they are used only during development.
4. You may use the [afragen/github-updater plugin](https://github.com/afragen/github-updater) to get recent updates.

## Plugin settings

Plugin has no settings in the admin. You can change Corpus API url used by plugin by adding the following constant
to your local `wp-config.php`:

```php
define('CORPUS_API_URL', 'http://localhost:3000/api');
```

## PHP API reference

### Performing API calls

Get the Corpus API object:

```php
// Get Corpus API object
$api = corpus_get_api();
// Or like this
$api = CorpusAPI::getInstance();

// Now, any Corpus model name can be called as a property or a method
// on the $api object to perform API calls.
// Singular/plural/lowercase/uppercase model name will work.

// These all are the same model
$model = $api->law;
$model = $api->Law;
$model = $api->laws;
$model = $api->Laws;

// Camel case, these are equal
$model = $api->mediaIncident;
$model = $api->MediaIncident;
$model = $api->mediaincident;
```

Make API calls:

```php
// Find in a set with filter
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

// Or use generic `get()` method to make calls
// It is used under the hood of the other methods described above 
$comparison = $api->law->get('compare', [
   'slug' => 'morocco-penal-revision'
]);
$law = $api->law->get('findOne', [
    'filter' => [
        'where' => [
            'slug' => 'morocco-penal-revision',
            'revistionIndex' => 0
        ]
    ]
]);
```

### Helper functions

---

##### `corpus_get_api_url()`
Returns Corpus API url. Default value is `http://corpus.govright.org/api`.

---

##### `corpus_get_api()`
Returns Corpus API object to perform API calls.

---

##### `corpus_get_locale($instance, $languageCode = null)`
Extracts locale data from a model instance.
If `$languageCode` is specified - returns corresponding translations if available
or a first available otherwise.
If `$languageCode` is not specified - checks if the WPML plugin is activated and tries to extract
currently set language, return a first available locale otherwise.

---

##### `corpus_atts_string($atts, $include_locale = true)`
Converts `$atts` array into a string. Includes `data-locale` prop if `$include_locale` is `true`
and WPML is activated.

---

## JavaScript API reference

Plugin adds a global `GovRight` object which has the following properties/methods:

---

##### `GovRight.corpusApiUrl`
String property that stores Corpus API url. Default value is `http://corpus.govright.org/api`. Example:

```javascript
// Load all laws
$.get(GovRight.corpusApiUrl + '/laws', function(laws) {
    console.log(laws);
});
```

---

##### `GovRight.getLocale(instance, languageCode)`
Extracts locale data from a model instance.
If `languageCode` is specified - returns corresponding translations if available
or a first available otherwise.
If `languageCode` is not specified - checks if the WPML plugin is activated and tries to extract
currently set language, return a first available locale otherwise.

---

##### `GovRight.getLocaleProp(instance, prop, languageCode)`
Extracts a property from the locale object on a given model instance.
If `languageCode` is specified - returns corresponding translation if available
or a first available otherwise.
If `languageCode` is not specified - checks if the WPML plugin is activated and tries to extract
currently set language, return a first available locale otherwise.

---

##### `GovRight.api(model)`
Returns a Corpus model object, which in turn has `get()` method to perform api calls.

**`GovRight.api(model)`**<br>
_Params:_<br>
`model` (String) - a Corpus model name, e.g. `law`, etc.<br>
_Returns:_<br>
CorpusModel - a Corpus model object

**`CorpusModel.get(path, params)`**<br>
_Params:_<br>
`path` (String) - model id or method, e.g. `count`, `findOne`, `versions/search`, etc.<br>
`params` (Object) - query string like filter or remote method params, etc.<br>
_Returns:_<br>
Promise that resolves with a single instance or array of instances depending on called method. Returns a single instance if id was passed as `path`.

Get Corpus model object:

```javascript
var model;

// Singular/plural/lowercase/uppercase model name will work.
// These all are the same model
model = GovRight.api('law');
model = GovRight.api('laws');
model = GovRight.api('Law');
model = GovRight.api('Laws');

// Camel case, these are equal
model = GovRight.api('mediaIncident');
model = GovRight.api('MediaIncident');
model = GovRight.api('mediaincident');
```

Make API calls:

```javascript
var result;
var handleResponse = function(data) {
    console.log(data);
    result = data;
};

// Find in a set with filter
GovRight.api('law').get({
    filter: {
        where: {
            slug: 'morocco-penal-revision'
        }
    }
}).then(handleResponse);

// Find by id
GovRight.api('law').get('567028d5219fffbb2d363f38').then(handleResponse);

// Find by id with a filter
GovRight.api('law').get('567028d5219fffbb2d363f38', {
    filter: {
        'include': [ 'user', 'discussions' ],
        'fields': [ 'id', 'slug', 'locales' ]
    }
}).then(handleResponse);

// Any available model method can called as a method on
// the model object like this
GovRight.api('law').get('findOne', {
    filter: {
        where: {
            slug: 'morocco-penal-revision'
        }
    }
}).then(handleResponse);

GovRight.api('law').get('count', {
    where: {
        slug: 'morocco-penal-revision'
    }
}).then(handleResponse);

// Call custom remote methods
GovRight.api('law').get('package', {
    slug: 'morocco-penal-revision',
    rev: 0
}).then(handleResponse);
```
---

## Development

### PHP

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
# In the plugin root, just run
phpunit
```

### JavaScript

```bash
# Install deps
npm install

# Run tests
npm test
```
