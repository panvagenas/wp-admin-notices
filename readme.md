# WordPress Admin notices script

## What is it?
This is a simple script to display WordPress notices to admin page

## How to use it?
### Basic use
First load the file to your script

```
#!php
    require_once path/to/file/WP_Admin_Notices.php
```

Whenever you want to enqueue a notice create a notice
```
#!php
    $notice = new WP_Updated_Notice('Your message here');
```

And add enqueue it
```
#!php
    WP_Admin_Notices::getInstance()->addNotice($notice);
```

Next time a page is loaded in admin panel the notice will be displayed

There are three classes of notices you can use:
1. WP_Error_Notice
1. WP_Updated_Notice
1. WP_UpdateNag_Notice
These classes affects the layout of the notice to be displayed. Read more [here](http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices).

### Arguments for notices
You can pass these arguments in when constructing notices of the above classes

1. `$content` : String. The content of your notice

1. `$times` : int. Optional. Number of times that this notice should be displayed. Default is 1

1. `$screen` : Array. Optional. An array containing ids of the screens this notice should be displayed on. Default empty array displays it anywhere.

1. `$users` : Array. Optional. An array containing user ids of the users to whom this notice should be displayed. Default empty array displays it to any user.