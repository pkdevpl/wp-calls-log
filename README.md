# WP Calls Log

## About

Wordpress plugin that registers incoming phone calls from Android phone device. It uses [Tasker app for android](https://play.google.com/store/apps/details?id=net.dinglisch.android.taskerm&hl=pl&gl=US) and WP REST API.

## Installation

Download plugin .zip file and install plugin in your wordpress admin.  

Go to `Urządzenia` page and click `Dodaj urządzenie` to add new device.

Provide your unique name for the device and click `Opublikuj`.

In the devices list screen, click `Pobierz profil Tasker` to download XML Tasker project for selected device. Save this file in device memory.

On the device open Tasker app and look for `import XML` option. When prompted select XML file from memory and enable `Phone ringing` profile.

Incoming phone calls on device will be now logged into your Wordpress admin in `Połączenia` tab.

## Use cases

You can use this plugin to make your Wordpress website recognise incoming phone calls and show saved customer/users data. It can be used to show recent customer orders or payment info without the need to manually search for customer in the database. It is also usefull to track stats on incoming phone calls.

## iOS

Unfortunately iOS does not expose caller ID on incoming phone calls and makes it impossible for this plugin to detect, who is calling.
