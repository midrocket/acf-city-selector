# ACF City Selector

Welcome to the City Selector plugin, which is an extension for [Advanced Custom Fields](https://www.advancedcustomfields.com). This is not a stand-alone plugin, you'll need ACF for it.

- [Version](#version)
- [Description](#description)
- [Installation](#installation)
- [Impact](#impact)
- [Usage](#usage)
- [Cities](#cities)
- [Actions](#actions)
- [Filters](#filters)
- [Functions](#functions)
- [Compatibility](#compatibility)
- [Tested on](#tested)
- [Updates](#updates)
- [Support](#support)
- [Website](#website)
- [Disclaimer](#disclaimer)
- [Credit](#credit)
- [Changelog](#changelog)

<a name="version"></a>
### Version

0.29.0 - released 25.11.20

<a name="description"></a>
### Description

This plugin allows you to select a city, based on country and province/state in an ACF Field.

![Screenshot ACF City Selector](https://beee4life.github.com/beee4life.github.io/images/screenshot-acf-city-selector.png)

It creates a new `field type` for you to choose when you're creating an ACF Field Group. If you click '+ add field' in a Field Group, you will find a new option (category: "Choice") to choose called `City Selector`.

* Add the field.
* Select whether to show labels above the input fields (default = yes).
* Save/publish the Field Group.

<a name="installation"></a>
### Installation

1. Download the [latest release](https://github.com/Beee4life/acf-city-selector/archive/master.zip).
1. Copy the `acf-city-selector` folder into your `wp-content/plugins` folder.
1. Activate the `ACF City Selector` plugin via the plugins admin page.
1. Create a new field via ACF and select the `City Selector` type (listed in the Choice section).
1. Select if you want to show labels
1. Select if you want a default country
1. (optional) Import new cities with help of the included Excel sheet.

If you use a composer file to add any plugins/libraries. Add the following to your composer.json:

```
  "repositories": [
    {
      "type":    "package",
      "package": {
        "name":    "Beee4life/acf-city-selector",
        "type":    "wordpress-plugin",
        "version": "0.29.0",
        "dist":    {
          "type": "zip",
          "url":  "https://github.com/Beee4life/acf-city-selector/archive/master.zip"
        }
      }
    },
  ]
```

Then run `composer require "beee4life/acf-city-selector"`

or add this to the `require` section by hand:

```
"beee4life/acf-city-selector": "0.29.0",
```

<a name="impact"></a>
### Impact

The plugin adds a database table named `{$wpdb->prefix}cities` upon plugin activation and imports cities from 3 different countries.

<a name="usage"></a>
### Usage

When the field is used by a single field, 3 values are stored in an array: 

```php
array(3) {
  ["countryCode"]=>
  string(2) "NL"
  ["stateCode"]=>
  string(5) "NL-NH"
  ["cityName"]=>
  string(9) "Amsterdam"
}
```

When the field is used in a repeater field, the values are stored in a multidimensional array:

```php 
array(2) {
  [0]=>
  array(3) {
    ["countryCode"]=>
    string(2) "BE"
    ["stateCode"]=>
    string(5) "BE-BR"
    ["cityName"]=>
    string(10) "Anderlecht"
  }
  [1]=>
  array(3) {
    ["countryCode"]=>
    string(2) "NL"
    ["stateCode"]=>
    string(5) "NL-FL"
    ["cityName"]=>
    string(6) "Almere"
  }
}
```

The reason why the state is prefixed (with the country code) in the database is because there can be states/provinces which use the same abbreviation as in another country. You won't notice this, since this value is formatted on return.

The return value gets overridden, so you get 'more return info' and properly formatted (stateCode). 5 values are returned:
```php
array(5) {
  ["countryCode"]=>
  string(2) "NL"
  ["stateCode"]=>
  string(5) "NH"
  ["cityName"]=>
  string(9) "Amsterdam"
  ["stateName"]=>
  string(13) "Noord-Holland"
  ["countryName"]=>
  string(11) "Netherlands"
}
```

Echo it as follows:

```php
$city_selector = get_field('field_name');
echo 'I live in ' . $city_selector['cityName'];
echo 'which is in ' . city_selector['stateName'] . ' (' . city_selector['stateCode'] . ')'; 
echo ' which lies in the country: ' . $city_selector['country'] . ' (' . $city_selector['countryCode'] . ')';
```

This outputs:

```
"I live in Amsterdam which is in the state Noord-Holland (NH) which lies in the country Netherlands (NL)".
```
        
<a name="cities"></a>
### Cities

The plugin comes with all cities in the Benelux (Belgium, Netherlands, Luxembourg) and Andorra pre-installed.

You can also add more countries yourself, through SQL or CSV import. There's a simple Excel sheet included in the plugin and can be found in the `import` folder. With this sheet, you can easily create an SQL insert statement or a CSV data set.

The explanation on how to do this, can be found on the first tab/sheet of the excel file.

There are a few country packages (csv files) available. These packages can be imported as is. These are available through the [ACFCS website](https://acf-city-selector.com).

<a name="actions"></a>
### Actions

There are a few actions available to add your own custom actions. 

Find all actions [here](https://acf-city-selector.com/documentation/actions/).

<a name="filters"></a>
### Filters

Find all filters [here](https://acf-city-selector.com/documentation/filters/).

<a name="functions"></a>
### Functions

A few custom functions are available for you to easily retrieve data.

Find all functions and their info [here](https://acf-city-selector.com/documentation/functions/).

<a name="compatibility"></a>
### Compatibility

This ACF field type is compatible/tested with ACF 5 (Pro). It's slightly tested with the free version (v4), but we won't be putting any (more) time in it. Just buy the Pro version. It's worth every cent !

<a name="tested"></a>
### Tested with

* [X] Wordpress 5.5.3
* [X] Advanced Custom Fields Pro 5.9.3
* [X] Advanced Custom Fields 4.4.12
* [X] Chrome (latest version)
* [X] Firefox (latest version)
* [ ] Safari (latest version)
* [ ] Edge (latest version)
* [ ] iPhone
* [ ] Android

<a name="updates"></a>
### Updates

Since this plugin hasn't been added to the Wordpress repo yet, we recommend 'watching' the plugin [here](https://github.com/Beee4life/acf-city-selector/watchers).

<a name="support"></a>
### Support

If you need support, please turn to [Github](https://github.com/Beee4life/acf-city-selector/issues).

<a name="website"></a>
### Website

[acf-city-selector.com](https://acf-city-selector.com)

<a name="disclaimer"></a>
### Disclaimer

This plugin is not 100% finished yet. It won't break anything but be on the look out, just in case.

The plugin works in the following situations: 
* in a single field
* in a repeater field
* in a group
* in a flexible content block
* in an accordion field
* as a cloned field
* on taxonomy terms
* on settings pages

The plugin has NOT been tested yet in the following situations: 
* most front-end usage (except single/repeater use)
* with the Gutenberg editor (and don't hold your breath either, I hate it)

Sometimes the loading of states/cities, takes a few seconds... Don't know why yet...
This seems to be very random and unpredictable.

<a name="credit"></a>
### Credit

I got the idea for this plugin through [Fabrizio Sabato](https://github.com/fab01) who used it a bit differently, which can ben seen [here](http://www.deskema.it/en/articles/multi-level-country-state-city-cascading-select-wordpress).

[Jarah de Jong](https://github.com/inquota) helped me out with some JS at the start and [John McDonald](https://github.com/mrjohnmc) did the German translations.

<a name="changelog"></a>
### Changelog

0.29.0
* added a fix for select2 in repeaters/flexible content blocks 
* added China, New Zealand, Aruba and Curaçao country files
* added a fix for incorrect escaping which caused incorrect ordering in names starting with a `'`.
* added new function as fallback for `acfcs_get_country_name()`
* removed flag assets from plugin
* changed URLs to new domain

0.28.0
* added select2 option
* changed hide labels filter as fallback for select2 
* added new country packages on the website

0.28.0-beta1
* added a new option: "state/provence + city" (for when a default country is set)
* added a transient for cities per state 
* added 3 new filters to override field labels 
* added a new filter to override showing of field labels 
* (re-)added a check for database version to prevent unnecessary table updates 

0.27.1
* removed version check for database which prevents creating database on plugin activation

0.27.0
* updated output for free countries
* removed Andorra and Luxembourg as default included countries
* added more available countries and combined country packages
* added fix for non-returning transient on first (state) load

0.26.1
* urgent fix for non-working city dropdown upon state change
* added first 'empty' option for cities upon state change
* improved styling for mobile admin pages
* fix broken sql query in search when no limit is set
* improve styling search form (admin)
* load searched values on search form (admin)

0.26.0
* added Andorra to included countries
* added Australia to available country packages
* added natsorting for cities
* added transient for cities per country
* preload all cities when a default country is set
* preload all cities when a country is selected
* merged populate dropdown functions
* changed dropdowns (for line limits) to free user input  

0.25.0
* forgotten version in readme

0.24.0
* dropdown values are now loaded from transients to speed it up
* added wpdb->prepare in queries
* styled csv upload better
* changed incorrect function call acfcs_after_success_import_nuke to acfcs_after_success_nuke  

0.23.0
* added min. PHP requirement
* removed brackets around file includes
* fixed db version option

0.22.2
* changed var name which prevented storing of some fields
* added constants

0.22.1
* added undefined index when no criteria are used to search (on the admin search page)
* added isset for new values

0.22
* updated German translation

0.21
* fixed error in verification on preview page + added page back
* added natural sorting for cities
* added option to select which fields to use (all/country only/country + state/country + city)
* added an info page with info for debug/support
* added German translation

0.20
* removed a check on length state code which returned false on countries like France, Spain and Australia
* temporarily removed preview page since it incorrectly deleted files

0.19
* fixed the newly added state transient because it was overriding the countries transient
* added an option to delete all transients, if needed

0.18
* forgot to update version + release date in readme

0.17
* changed typo in Dutch translation
* added 'bolding' to current page in admin menu
* added a transient for states per country to speed up state retrieval
* added more hooks 'to hook into'
    * acfcs_after_success_country_remove
    * acfcs_after_success_file_upload
    * acfcs_after_success_file_delete
    * acfcs_after_success_import_nuke

0.16
* made the field work in all field types

0.15
* added the option to add a single field to flexible content blocks
* added the option to delete a country at once through the admin pages
* added a page in the admin which contains which packages are available
* added a filter to set a default delimiter
* added a filter to set a different line length
* 'remember' verified file on dashboard
* 'remember' search values on search page

0.14
* added the option to set a default country (for single fields/in groups/in repeaters)
* changed state length to 3 characters for Australia and some other countries
* added `optgroups` to the state dropdown in the admin search

0.13
* Forgot to change version

0.12
* Hotfix to remove an incorrect SQL statement

0.11
* Fixed select values in admin state search
* Added natural sorting for French 'arrondisements'

0.10
* Made the field available on user pages
* Dropped inclusion for v4.

0.10-beta
* Made the field available in repeaters
* Made the field available in groups

0.9
* Added a search page to manually remove cities from the database

0.8
* Fix incorrect version
* Removed deprecated filter contextual_help

0.7
* Change indentation from spaces to tabs

0.6
* Translate more strings
* Fix import errors for Luxembourg
* DRY import code

0.5
* Fix unescaped characters on import

0.4
* Internationalised all cities/states/countries

0.3
* Added hooks for import/delete actions

0.2
* Added database collation
