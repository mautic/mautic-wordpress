=== WP Mautic ===
Contributors: mautic,hideokamoto
Donate link: http://mautic.org/
Tags: marketing, automation
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will allow you to add Mautic (Free Open Source Marketing Automation) tracking to your site.

== Description ==

[Mautic](http://mautic.org) WordPress Plugin inserts Mautic tracking image and forms to the WP website. Your Mautic instance will be able to track information about your visitors that way.

### Key features
- You don't have to edit source code of your template to insert tracking code.
- Plugin adds additional information to tracking image URL so you get better results than using just plain HTML code of tracking image.
- You can use Mautic form embed with shortcode descirbed below.

### Mautic Tracking Image

Tracking JS works right after you finish step 5 of Installation above. That means it will insert the Mautic JS from your Mautic instance into your document head. You can check HTML source code (CTRL + U) of your WP website to make sure the plugin works. You should be able to find something like this:

```
<script>
    (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
    })(window,document,'script','http://yourmauticsite.com/mtc.js','mt');

    mt('send', 'pageview');
</script>
```

### Mautic Forms

To load a Mautic Form to your WP post, insert this shortcode to the place you want the form to appear:

`[mautic type="form" id="1"]`

Replace "1" with the form ID you want to load. To get the ID of the form, go to your Mautic, open the form detail and look at the URL. The ID is right there. For example in this URL: http://yourmautic.com/s/forms/view/3 the ID = 3.

### Mautic Focus

To load a Mautic Focus to your post, insert this shortcode to the place you want the form to appear:

```
[mautic type="focus" id="1"]
```

Replace "1" with the focus ID you want to load. To get the ID of the focus, go to your Mautic, open the focus detail and look at the URL. The ID is right there. For example in this URL: http://yourmautic.com/s/focus/3 the ID = 3.

### Mautic Dynamic Content

To load dynamic content into your WP content, insert this shortcode where you'd like it to appear:

```
[mautic type="content" slot="slot_name"]Default content to display in case of error or unknown contact.[/mautic]
```

Replace the "slot_name" with the slot name you'd like to load. This corresponds to the slot name you defined when building your campaign and adding the "Request Dynamic Content" contact decision.

== Installation ==

### Via WP administration

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
