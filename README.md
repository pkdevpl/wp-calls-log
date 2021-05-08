# WP Calls Log

This Wordpress plugin registers incoming phone calls from Android phone devices.

It uses REST API to create secure endpoints and then receives POST requests from Android device with incoming call data.

Android device should have [Tasker App](https://play.google.com/store/apps/details?id=net.dinglisch.android.taskerm&hl=pl&gl=US) installed. You can also instead use any other App capable of detecting incoming phone calls and sending POST requests).

You can download ready-to-use Tasker Profile configuration from `tasker` directory. Import it as a profile in your Tasker App and paste API key generated in Wordpress admin into Takser variables.

## Use cases
You can use this plugin to make your Wordpress website recognise incoming phone calls and match them to saved customer/users data. This can then be used to show recent customer orders or payment info without manually searching for customer in the database. It is also usefull to track stats on incoming phone calls.

Unfortunately iOS does not support detection of incoming phone calls, which defeats purpose of this plugin.
