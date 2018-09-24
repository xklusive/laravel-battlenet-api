# Laravel 5 Battle.net API


[![Latest Version on Packagist](https://img.shields.io/packagist/v/xklusive/laravel-battlenet-api.svg?style=flat-square)](https://packagist.org/packages/xklusive/laravel-battlenet-api)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/xklusive/laravel-battlenet-api.svg?branch=master)](https://travis-ci.org/xklusive/laravel-battlenet-api)
[![Code Quality](https://scrutinizer-ci.com/g/xklusive/laravel-battlenet-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xklusive/laravel-battlenet-api/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/02f8f016-b462-4c0c-85b7-afc51b1a3b6a/mini.png)](https://insight.sensiolabs.com/projects/02f8f016-b462-4c0c-85b7-afc51b1a3b6a)
[![StyleCI](https://styleci.io/repos/79335460/shield)](https://styleci.io/repos/79335460)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dd19ac3e70f44957b76f9d18c4bf3eaa)](https://www.codacy.com/app/atraides/laravel-battlenet-api?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=xklusive/laravel-battlenet-api&amp;utm_campaign=Badge_Grade)
[![Total Downloads](https://img.shields.io/packagist/dt/xklusive/laravel-battlenet-api.svg?style=flat-square)](https://packagist.org/packages/xklusive/laravel-battlenet-api)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/xklusive/laravel-battlenet-api.svg)](http://isitmaintained.com/project/xklusive/laravel-battlenet-api "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/xklusive/laravel-battlenet-api.svg)](http://isitmaintained.com/project/xklusive/laravel-battlenet-api "Percentage of issues still open")

This package allows to call the Battle.net API.

Once installed you can do stuff like this:

```php
use Xklusive\BattlenetApi\Services\WowService;

public function index(WowService $wow)
{
	$achievement = $wow->getAchievement(2144);

	dd($achievement);

	//Ouput: 
	//Collection {#236 ▼
  	//	#items: array:10 [▼
	//	    "id" => 2144
	//	    "title" => "Voyages au bout du monde"
	//	    "points" => 50
	//	    "description" => "Accomplir les hauts faits des évènements mondiaux listés ci-dessous."
	//	    "reward" => "Récompense : proto-drake pourpre"
	//	    "rewardItems" => array:1 [▶]
	//	    "icon" => "achievement_bg_masterofallbgs"
	//	    "criteria" => array:8 [▶]
	//	    "accountWide" => true
	//	    "factionId" => 2
	//	]
}

```

## Battle.net API key
Before you be able to make requests to the Battle.net API, you need to provide your API key.
If you don't have an API key, refer to https://dev.battle.net/docs to get your API key.
Without a Battle.net API key, the package will not be functionnal.

## Install
 
You can install the pacakge via composer:
```bash
$ composer require xklusive/laravel-battlenet-api
```
 
Then this provider must be installed :
```php
// config/app.php
'providers' => [
	...
    Xklusive\BattlenetApi\BattlenetApiServiceProvider::class,
];
```
 
The last required step is to publish configuration's file in your application with :
```bash
$ php artisan vendor:publish --provider="Xklusive\BattlenetApi\BattlenetApiServiceProvider" --tag="config"
```

Once the config file is published setup the required variables in the .env file :
```php
// .env

BATTLENET_KEY=[YOUR_API_KEY]
BATTLENET_REGION=eu

```
 
Congratulations, you have successfully installed Laravel Battle.net API !
