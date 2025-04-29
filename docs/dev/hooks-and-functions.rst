Developer Hooks & Functions
===========================

Useful Functions
----------------

- ``wpmautic_option( $option, $default )`` — Retrieve plugin settings safely.
- ``wpmautic_base_script()`` — Get full URL of ``mtc.js`` script.
- ``wpmautic_get_tracking_attributes()`` — Get custom tracking data array.

Filters
-------

Extend the plugin using:

::

  apply_filters('wpmautic_tracking_attributes', $attrs)

Actions
-------

- ``wp_head`` or ``wp_footer`` — Automatically injects Mautic script depending on settings.
