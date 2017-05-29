Mautic WordPress plugin [![Build Status](https://travis-ci.org/mautic/mautic-wordpress.svg?branch=master)](https://travis-ci.org/mautic/mautic-wordpress)
=======================

[Mautic](http://mautic.org) [Wordpress Plugin](https://wordpress.org/plugins/wp-mautic/) inserts Mautic tracking image and forms to the WP website. Your Mautic instance will be able to track information about your visitors that way.

## Installation

### Via WP administration

Mautic - WordPress plugin [is listed](https://wordpress.org/plugins/wp-mautic/) in the in the official WordPress plugin repository. That makes it very easy to install it directly form WP administration.

1. Go to *Plugins* / *Add New*.
2. Search for **WP Mautic** in the search box.
3. The "WP Mautic" plugin should appear. Click on Install.
4. Go to *Settings* / WP Mautic and fill in the Base URL of your Mautic instance.

### Via ZIP package

If the installation via official WP plugin repository doesn't work for you, follow these steps:

1. [Download ZIP package](https://github.com/mautic/mautic-wordpress/archive/master.zip).
2. At your WP administration go to *Plugins* / *Add New* / *Upload plugin*.
3. Select the ZIP package you've downloaded in step 1.
4. Go to *Plugins* and enable WP Mautic plugin.
5. Go to *Settings* / WPMautic and fill in the Base URL of your Mautic instance.

## Usage

### Mautic Tracking Image

Tracking image works right after you finish step 5 of Installation above. That means it will insert 1 px gif image loaded from your Mautic instance. You can check HTML source code (CTRL + U) of your WP website to make sure the plugin works. You should be able to find something like this:

```html
<script>
    (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
    })(window,document,'script','http://yourmauticsite.com/mtc.js','mt');

    mt('send', 'pageview');
</script>
```

Plugin adds more information (current url, referal url, page title, user language) to the image URL query encoded in base 64 (not humanly readable). This way your Mautic instance receives more valuable data.

### Mautic Forms

To load a Mautic Form to your WP post, insert this shortcode to the place you want the form to appear:

```
[mautic type="form" id="1"]
```

Replace "1" with the form ID you want to load. To get the ID of the form, go to your Mautic, open the form detail and look at the URL. The ID is right there. For example in this URL: http://yourmautic.com/s/forms/view/3 the ID = 3.

### Mautic Focus

To load a Mautic Focus to your post, insert this shortcode to the place you want the form to appear:

```
[mautic type="focus" id="1"]
```

Replace "1" with the focus ID you want to load. To get the ID of the focus, go to your Mautic, open the focus detail and look at the URL. The ID is right there. For example in this URL: http://yourmautic.com/s/focus/3.js the ID = 3.

### Mautic Dynamic Content

To load dynamic content into your WP content, insert this shortcode where you'd like it to appear:

```
[mautic type="content" slot="slot_name"]Default content to display in case of error or unknown contact.[/mautic]
```

Replace the "slot_name" with the slot name you'd like to load. This corresponds to the slot name you defined when building your campaign and adding the "Request Dynamic Content" contact decision.

### Mautic Gated Videos

Mautic supports gated videos with Youtube, Vimeo, and MP4 as sources.

To load gated videos into your WP content, insert this shortcode where you'd like it to appear:

```
[mautic type="video" gate-time="#" form-id="#" src="URL"]
```

Replace the # signs with the appropriate number. For gate-time, enter the time
 (in seconds) where you want to pause the video and show the mautic form. For
 form-id, enter the id of the mautic form that you'd like to display as the
 gate. Replace URL with the browser URL to view the video. In the case of
 Youtube or Vimeo, you can simply use the URL as it appears in your address
 bar when viewing the video normally on the providing website. For MP4 videos,
 enter the full http URL to the MP4 file on the server.

### Mautic Tags

 You can add or remove multiple lead tags on specific pages using commas. To remove an tag you have to use minus "-" signal before tag name:

 ```
 [mautic type="tags" values="mytag,anothertag,-removetag"]
 ```
