# Kohana HAPI

**H**yperText Transfer Protocol **A**pplication **P**rogramming **I**nterface

The purpose of this module is to provide an easy framework for API-based development projects.

**In development. Do not use.** It is expected that the major (backwards incompatible) versions will be increased rapidly.

## What it is

* Framework for adding API support to your Kohana project
* Unfinished

## What it is not

* RESTful - Well, a bit, but missing [level three of the API maturity model](http://www.crummy
.com/writing/speaking/2008-QCon/act3.html)
* General purpose
* Scalable and optimized - mean for one server, one client approach where client is under vendor control
* Tested

# Installation

* Clone and enable the module. Use a git submodule, download the zip or add a Composer dependency
* Add a route to the API controller:

```php
<?php
Route::set(
	'api',
	'api(/<controller>(/<id>(/<action>)))'
)
	->defaults(
	array(
		'directory'  => 'API/V1/',
		'controller' => 'About',
		'action'     => 'index'
	)
);
```

* Create `APPPATH/classes/Controller/API/V1/Main.php` - extends `Controller_HAPI`
* Create `APPPATH/classes/Controller/API/V1/About.php` - extends `Controller_API_V1_Main`
* In the `action_index`, write:

```php
<?php
$this->hapi(['about' => 'Hello, world']);
```

* Make a HTTP GET query to `BASE_URL/api/v1/about

# TODO

* Remove some of the hardcoded values to support not only JSON
* Document code, wiki, gh-pages
* Improve architecture
* Improve API semantics: provide meaningful error and status messages in response body
* Work towards achieving REST