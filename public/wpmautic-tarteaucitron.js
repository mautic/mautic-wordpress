// change language
// @todo Only do it if the page language is German
// tarteaucitronForceLanguage = 'de';
// var currentLanguage = document.documentElement.lang;
// tarteaucitronCustomText = {
// 	'alertBigPrivacy': '<?php echo esc_html( __( 'Diese Webseite verwendet Cookies um Inhalte und Anzeigen zu personalisieren und zu analysieren. Du bestimmst, welche Dienste benutzt werden.', 'fma' ) ); ?>'
// };

tarteaucitron.init({
  privacyUrl: wpmauticParams.privacyUrl,

  hashtag: "#gdpr-consent" /* Open the panel with this hashtag */,
  cookieName: "tarteaucitron" /* Cookie name */,

  orientation:
    wpmauticParams.orientation ||
    "bottom" /* Banner position (top - middle - bottom) */,
  showAlertSmall:
    wpmauticParams.showAlertSmall ||
    false /* Show the small banner on bottom right */,
  cookieslist: wpmauticParams.cookieslist || false /* Show the cookie list */,

  adblocker:
    wpmauticParams.adblocker ||
    false /* Show a Warning if an adblocker is detected */,
  AcceptAllCta:
    wpmauticParams.acceptAllCta ||
    true /* Show the accept all button when highPrivacy on */,
  highPrivacy: wpmauticParams.highPrivacy || true /* Disable auto consent */,
  handleBrowserDNTRequest:
    wpmauticParams.handleBrowserDNTRequest ||
    false /* If Do Not Track == 1, disallow all */,

  removeCredit: wpmauticParams.removeCredit || true /* Remove credit link */,
  moreInfoLink: wpmauticParams.moreInfoLink || true /* Show more info link */,
  useExternalCss:
    wpmauticParams.useExternalCss ||
    false /* If false, the tarteaucitron.css file will be loaded */,
  // cookieDomain: wpmauticParams.orientation || false, /* Shared cookie for subdomain website */
  readmoreLink:
    wpmauticParams.readmoreLink ||
    wpmauticParams.privacyUrl /* Readmore link after tracker */
});

// Config google tag manager if set.
if (wpmauticParams.googletagmanagerId) {
  tarteaucitron.user.googletagmanagerId = wpmauticParams.googletagmanagerId;
  (tarteaucitron.job = tarteaucitron.job || []).push("googletagmanager");
}

// config mautic
tarteaucitron.user.mauticurl = wpmauticParams.baseUrl;
(tarteaucitron.job = tarteaucitron.job || []).push("wpmautic");

// service wpmautic
tarteaucitron.services.wpmautic = {
  key: "wpmautic",
  type: "analytic",
  name: "Mautic WP",
  uri: "https://www.mautic.org/",
  needConsent: true,
  cookies: ["mtc_id", "mtc_sid"],
  js: function() {
    "use strict";
    if (tarteaucitron.user.mauticurl === undefined) {
      return;
    }

    window["MauticTrackingObject"] = "mt";
    window["mt"] =
      window["mt"] ||
      function() {
        (window["mt"].q = window["mt"].q || []).push(arguments);
      };

    tarteaucitron.addScript(tarteaucitron.user.mauticurl, "", function() {
      wpmautic_send();
    });
  },
  fallback: function() {
    "use strict";
    // when use deny cookie
  }
};
