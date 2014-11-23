#SamsonPHP events module 
We have build unique and very simple approach to make our modules and classes very loosely coupled by creating event-driven logic. 
All core module code is build using this approach which gives unlimited abilities in extending its functionality and adding supeior features to it. 
This approach is build with programming pattern Observer in mind, but has a little bit different approach.

Events gives us opportunity to remove interclass connections, so it would be perfectly suited for writing unit tests. 
From other hand, before moving to event based system, we had a lot of different handler stack, every of them fulfilled only only specific goal 
and had to have field and function for working with it, with event approach we dont need all this stacks, functions and other stuff anymore.

[![Latest Stable Version](https://poser.pugx.org/samsonos/php_event/v/stable.svg)](https://packagist.org/packages/samsonos/php_event) 
[![Build Status](https://travis-ci.org/samsonos/php_event.png)](https://travis-ci.org/samsonos/php_event) 
[![Coverage Status](https://coveralls.io/repos/samsonos/php_event/badge.png)](https://coveralls.io/r/samsonos/php_event) 
[![Code Climate](https://codeclimate.com/github/samsonos/php_event/badges/gpa.svg)](https://codeclimate.com/github/samsonos/php_event) 
[![Total Downloads](https://poser.pugx.org/samsonos/php_event/downloads.svg)](https://packagist.org/packages/samsonos/php_event)


## Basics
We have created one simple static class to provide all this awesome features in SamsonPHP, called ```\samson\core\Event```. This class has two main simple static functions:
* ```fire($id, $params, $signal = false)```
* ```subscribe($id, $params)```

## Event identifier
This is the main parameter which connects event subscribers and executors, identifier must match followings standard rules:
* must be in lowercase - as all identifiers is resolved only in lowercase
* must consist of two parts: ```[module_name].[event_name]```
* event name, if it is long, must be separated with underscore('_')
All firing module, class events must specified in its documentation ```Events``` section

## Event - Subscribe
This method is used to register listener for a specific event identifier, there is no actual validation or checking if this identifier will ever be fired somewhere, we actually do not know, but we hope, and our hopes
would be fulfilled, as we can read module documentation and subscribe only for existing events. This is done
so because we cannot guarantee what events will actually fire because code can be loaded in different order(thanks to autoloading) and sucbcribe() can be called much earlier then event declaration possibly be, so we have decided and putted this responsibility on developers shoulders and only they must think of what event they subscribe on.

Method declaration:
```subscribe($key, $handler, $params = array())``` 
* ```$key``` - Unique event identifier, read module documentation to find out which events and when will be fired.
* ```$handler``` - Callback, can be simple string ```callme``` if this is just a function or ```array($obj, '[method_name]')``` or ```array('[classs_name]', '[method_name]')''' for static methods.
* ```$params``` - Collection of additional data that is needed when the event handler will be excuted, this array will be passed ass callback arguments.

## Event - Fire
This method is used to tell all other listeners(modules, classes) who has subscribed to current event identifier that its being happened right here and right now. When this method is triggered its meant that this is exact place and time when all subscribers must handle current event.

Method declaration:
```fire($key,$params = array(), $signal = false)``` 
* ```$key``` - Unique event identifier, who fires this event must specify it in the documentation.
* ```$params``` - Collection of additional data that will be passed to callback, this collection differs from the subscribe parameters collection as it is being send from event firing side.
* ```signal``` - This is special parameter that is characterizes event as signal, when the signal is fired, only the first subscriber callback will be called and the result of it execution will be returned. This is done when this event must be handled only once, for example routing system, you cannot use multiple routing systems in one application(Why is that??? need to change to something more generic), the idea was that we must return something from subscribed callback.

We recommend to think twice before firing event somewhere in your code, as this must be realy needed and usefull event to avoid system overhead.

> When the actual event is trigered, system merges event fired params with subscribe parameters, event fired parameters is first to be passed to callback function. This is done in this way because there can be any amount of subscribers with different count of parameters, but the event firing parameters will never change.

## Changing data in subscribed event handlers
When you want to pass variable to event handler to change it just use array with references syntax:
```php
Event::fire('core.routing', array(&$url, $count))
```
In the example described above we have passed a $url variable by reference to all possible subscribers to ```core.routing``` event, and if one of them will change it, it will be changed every where.