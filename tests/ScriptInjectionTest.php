<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test Mautic Script injection
 */
class ScriptInjectionTest extends WPMauticTestCase
{
    private function renderRawPage($header = true, $footer = true)
    {
        $this->setOutputCallback(array($this, 'returnVoid'));
        if (true === $header) {
            wp_head();
        }
        if (true === $footer) {
            wp_footer();
        }
        return $this->getActualOutput();
    }

    public function setUp()
    {
        parent::setUp();

        remove_action('wp_head', 'wpmautic_inject_script');
        remove_action('wp_footer', 'wpmautic_inject_script');
        remove_action('wp_footer', 'wpmautic_inject_noscript');
    }

    public function test_script_is_not_injected_when_base_url_is_empty()
    {
        $output = $this->renderRawPage();

        $this->assertNotContains(sprintf("(window,document,'script','/mtc.js','mt')"), $output);
        $this->assertNotContains("['MauticTrackingObject']", $output);
        $this->assertNotContains("mt('send', 'pageview')", $output);
        $this->assertNotContains("/mtracking.gif?d=", $output);
    }

    public function test_script_is_injected_with_valid_base_url()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array('base_url' => $base_url));
        do_action('plugins_loaded');

        $output = $this->renderRawPage();

        $this->assertContains(sprintf("(window,document,'script','{$base_url}/mtc.js','mt')"), $output);
        $this->assertContains("['MauticTrackingObject']", $output);
        $this->assertContains("mt('send', 'pageview')", $output);
        $this->assertContains($base_url."/mtracking.gif?d=", $output);
    }

    public function test_script_is_not_injected_in_footer_by_default()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array('base_url' => $base_url));
        do_action('plugins_loaded');

        $output = $this->renderRawPage(false, true);

        $this->assertNotContains(sprintf("(window,document,'script','{$base_url}/mtc.js','mt')"), $output);
        $this->assertNotContains("['MauticTrackingObject']", $output);
        $this->assertNotContains("mt('send', 'pageview')", $output);

        //Fallback activated and <noscript> is always injected in footer !
        $this->assertContains(sprintf("%s/mtracking.gif?d=", $base_url), $output);
    }

    public function test_script_is_injected_in_footer_when_requested()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array('base_url' => $base_url, 'script_location' => 'footer'));
        do_action('plugins_loaded');

        $output = $this->renderRawPage(false, true);

        $this->assertContains(sprintf("(window,document,'script','{$base_url}/mtc.js','mt')"), $output);
        $this->assertContains("['MauticTrackingObject']", $output);
        $this->assertContains("mt('send', 'pageview')", $output);
        $this->assertContains(sprintf("%s/mtracking.gif?d=", $base_url), $output);
    }

    public function test_script_is_injected_with_custom_attributes_when_defined()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array('base_url' => $base_url, 'script_location' => 'footer'));

        add_filter('wpmautic_tracking_attributes', array($this, 'wpmautic_tracking_attributes_filter'));

        do_action('plugins_loaded');

        $output = $this->renderRawPage(false, true);

        $this->assertContains(sprintf("(window,document,'script','{$base_url}/mtc.js','mt')"), $output);
        $this->assertContains("['MauticTrackingObject']", $output);
        $this->assertContains("mt('send', 'pageview', {\"preferred_locale\":\"xx\"})", $output);
    }

    public function test_tracking_image_is_not_injected_when_disabled()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array(
            'base_url' => $base_url,
            'fallback_activated' => false
        ));
        do_action('plugins_loaded');

        $output = $this->renderRawPage();

        $this->assertNotContains("/mtracking.gif?d=", $output);
    }

    public function test_tracking_image_payload_is_valid()
    {
        $base_url = 'http://example.com';
        $this->go_to( $base_url );
        update_option('wpmautic_options', array('base_url' => $base_url));
        do_action('plugins_loaded');

        $output = $this->renderRawPage();

        $payload = rawurlencode( base64_encode( serialize( wpmautic_get_url_query() ) ) );
        $this->assertContains(sprintf("%s/mtracking.gif?d=%s", $base_url, $payload), $output);
    }

    public function test_tracking_image_payload_is_valid_with_custom_data()
    {
        $base_url = 'http://example.com';
        $this->go_to( $base_url );
        update_option('wpmautic_options', array('base_url' => $base_url));

        add_filter('wpmautic_tracking_attributes', array($this, 'wpmautic_tracking_attributes_filter'));

        do_action('plugins_loaded');

        $output = $this->renderRawPage();

        $payload = rawurlencode( base64_encode( serialize( wpmautic_get_url_query() ) ) );
        $this->assertContains(sprintf("%s/mtracking.gif?d=%s", $base_url, $payload), $output);
    }

    //---------------

    public function wpmautic_tracking_attributes_filter($attrs)
    {
        $attrs['preferred_locale'] = 'xx';
        return $attrs;
    }
}
