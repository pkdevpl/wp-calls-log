# WP Calls Log

## About

Wordpress plugin that allows you to show incoming phone calls from your Android device in the Wordpress admin panel. It generates XML files you can import into [Tasker App](https://play.google.com/store/apps/details?id=net.dinglisch.android.taskerm&hl=pl&gl=US) on your phone.

Tasker let's you detect events, like incoming phone calls or received text messages and perform a task. In this case it sends HTTP requests to your Wordpress site REST API, providing caller id and device api key.

*Note: Tasker is not free (though it's ridiculously cheap compared to custom apps with the same functionality)*

## Installation

Download plugin `.zip` file (3 588 kB) from this repository and install plugin in your wordpress admin.  

Go to `Urządzenia` page and click `Dodaj urządzenie` to add new device (phone).

![All devices](http://wp.pkdev.pl/wp-content/uploads/2021/05/all_devices_sm.jpg)

Provide your unique name for the device and click `Opublikuj`.

![Add new device](http://wp.pkdev.pl/wp-content/uploads/2021/05/add_device_sm.jpg)

In the devices list screen, click `Pobierz profil Tasker` to download XML Tasker project for selected device. Save this file in device memory (phone).

![Download XML file](http://wp.pkdev.pl/wp-content/uploads/2021/05/all_devices_2_sm.jpg)

On the device open Tasker app and look for `Import XML` option (this may vary depending on device). 

When prompted select XML file and then enable `Phone ringing` profile.

![Import Tasker Project](http://wp.pkdev.pl/wp-content/uploads/2021/05/tasker_sm.jpg)

Incoming phone calls on device will be now logged into your Wordpress admin in `Połączenia` tab.

You can disable `Phone ringing` profile to stop tracking your phone calls.

## Use cases

You can use this plugin to make your Wordpress website react to incoming phone call and immidiately show customer data on the screen, even before you answer. This eliminates the need to manually search for customers during phone calls. It is also usefull to track stats of customer contact.

## iOS

Unfortunately iOS does not expose caller ID on incoming phone calls and makes it impossible for this plugin to detect who is calling.
