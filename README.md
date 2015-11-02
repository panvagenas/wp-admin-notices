# WordPress Admin notices script

[![Build Status](https://img.shields.io/travis/panvagenas/wp-admin-notices/master.svg?style=flat-square)](http://travis-ci.org/panvagenas/wp-admin-notices)
[![Codacy](https://api.codacy.com/project/badge/1cdfb91afc80407e99fffe8d092ab628)](https://www.codacy.com/app/pan-vagenas/wp-admin-notices)
[![Latest Stable Version](https://img.shields.io/packagist/v/panvagenas/wp-admin-notices.svg?style=flat-square)](https://packagist.org/packages/panvagenas/wp-admin-notices)
[![License](https://img.shields.io/packagist/l/panvagenas/wp-admin-notices.svg?style=flat-square)](https://packagist.org/packages/panvagenas/wp-admin-notices)
[![Total Downloads](https://img.shields.io/packagist/dt/panvagenas/wp-admin-notices.svg?style=flat-square)](https://packagist.org/packages/panvagenas/wp-admin-notices)


## What is it?
This is a simple script to display WordPress notices to admin page

## How to use it?

### Load with composer

The recommended method to load this script in your plugin or theme, is to use composer.
To do so add the following line to your `composer.json` file:

```json
{
    "require": {
        "panvagenas/wp-admin-notices": "*"
    }
}
```
or run 

`composer require panvagenas/wp-admin-notices`

### Basic usage

Whenever you want to enqueue a notice create a notice
```php
<?php
// Create a notice
$notice = new WP_Updated_Notice('Your message here');

// And add enqueue it
WP_Admin_Notices::getInstance()->addNotice($notice);
```

Next time a page is loaded in admin panel the notice will be displayed

### Notice types

There are three classes of notices you can use:

1. `WP_Error_Notice`
2. `WP_Updated_Notice`
3. `WP_UpdateNag_Notice`

These classes affects the layout of the notice to be displayed. Read more [here](http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices).

### Arguments for `\Notices\WP_Notice` class

You can pass these arguments in when constructing notices of the above classes

1. `$content`: String. The content of your notice
2. `$title`: Title of the notice, optional default is empty string.
3. `$times`: int. Optional. Number of times that this notice should be displayed. Default is 1
4. `$screen`: Array. Optional. An array containing ids of the screens this notice should be displayed on. Default empty array displays it anywhere.
5. `$users`: Array. Optional. An array containing user ids of the users to whom this notice should be displayed. Default empty array displays it to any user.

### Using formatters

`\Notices\WP_Notice` instances make use of formatters to get the actual output of the notice.
Default formatter is `\Notices\Formatters\WordPress` so you can use this script as is. 

If you want to create your own formatter you should create a class that extends `\Notices\WP_Notice\Formatter`.
This class must implement the `formatOutput( WP_Notice $notice )` method that accepts an instance of `\Notices\WP_Notice`
as its only parameter and returns the output to be displayed.

## Licence

Copyright (C) 2015 Panagiotis Vagenas <pan.vagenas@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/

