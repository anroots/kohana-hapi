# Kohana HAPI

**H**yperMedia **A**pplication **P**rogramming **I**nterface - A Kohana module to use as a framework for building HTTP APIs.

![Screenshot of a HTTP response](https://raw.github.com/anroots/kohana-hapi/master/guide/Screenshot-1.png)

**In development. Do not use.** It is expected that the major (backwards incompatible) versions will be increased rapidly.

## What it is

* Framework for adding API support to your Kohana project
* Unfinished
* Rather strict than general-purpose

## What it is not

* RESTful - Well, a bit, but missing [level three of the API maturity model](http://www.crummy
.com/writing/speaking/2008-QCon/act3.html)
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

# Bibliography

* [HAL - Hypertext Application Language](http://stateless.co/hal_specification.html)
* [JSON Linking with HAL](http://blog.stateless.co/post/13296666138/json-linking-with-hal)
* [REST+JSON API Design - Best Practices for Developers](http://www.youtube
.com/watch?v=hdSrT4yjS1g&list=PL40E8C61DBE5F4266&index=25)
* [SymfonyLive Paris 2012 - David Zuelke Designing HTTP Interfaces And RESTful Web Services](http://www.youtube
.com/watch?v=XzgCzjMdvRE&list=PL40E8C61DBE5F4266&index=27)
* [How to GET a Cup of Coffee](http://www.infoq.com/articles/webber-rest-workflow)
* [How I Explained REST to My Wife](http://tomayko.com/writings/rest-to-my-wife)