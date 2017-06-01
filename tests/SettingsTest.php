<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test mautic settings page
 */
class SettingsTest extends WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        require_once(VPMAUTIC_PLUGIN_DIR.'/options.php');

        $this->setOutputCallback(create_function('', ''));
    }

    public function test_nonce_fields_are_present()
    {
        wpmautic_options_page();
        $output = $this->getActualOutput();
        $this->assertContains("name='option_page' value='wpmautic'", $output);
    }

    public function test_with_admin_init()
    {
        wpmautic_admin_init();
        wpmautic_options_page();
        $output = $this->getActualOutput();
        $this->assertContains("name='option_page' value='wpmautic'", $output);
        $this->test_base_url_setting();
        $this->test_script_location_setting();
    }

    public function test_base_url_setting()
    {
        wpmautic_base_url();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_base_url", $output);
        $this->assertContains("wpmautic_options[base_url]", $output);
        $this->assertContains("value=\"\"", $output);
    }

    public function test_base_url_setting_with_value()
    {
        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com'
        ));

        wpmautic_base_url();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_base_url", $output);
        $this->assertContains("wpmautic_options[base_url]", $output);
        $this->assertContains("value=\"http://example.com\"", $output);
    }

    public function test_script_location_setting()
    {
        wpmautic_script_location();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_script_location", $output);
        $this->assertContains("wpmautic_options[script_location]", $output);
        $this->assertRegExp('/value="header"\s+checked/', $output);
    }

    public function test_script_location_setting_with_footer_checked()
    {
        update_option('wpmautic_options', array(
            'script_location' => 'footer'
        ));

        wpmautic_script_location();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_script_location", $output);
        $this->assertContains("wpmautic_options[script_location]", $output);
        $this->assertRegExp('/value="footer"\s+checked/', $output);
    }

    public function test_fallback_activated_setting_with_default()
    {
        wpmautic_fallback_activated();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_fallback_activated", $output);
        $this->assertContains("wpmautic_options[fallback_activated]", $output);
        $this->assertContains("checked", $output);
    }

    public function test_fallback_activated_setting_with_unchecked()
    {
        update_option('wpmautic_options', array(
            'fallback_activated' => false
        ));

        wpmautic_fallback_activated();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_fallback_activated", $output);
        $this->assertContains("wpmautic_options[fallback_activated]", $output);
        $this->assertNotContains("checked", $output);
    }

    public function test_fallback_activated_setting_with_checked()
    {
        update_option('wpmautic_options', array(
            'fallback_activated' => true
        ));

        wpmautic_fallback_activated();
        $output = $this->getActualOutput();
        $this->assertContains("wpmautic_fallback_activated", $output);
        $this->assertContains("wpmautic_options[fallback_activated]", $output);
        $this->assertContains("checked", $output);
    }
}
