<?php
/**
 * @package wpmautic\tests
 */

use Mautic\WP_Mautic\Utils\Options;

/**
 * Test Mautic options management
 */
class OptionsTest extends WP_UnitTestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function test_invalid_option_name()
    {
        Options::get('azerty123456');
    }

    public function test_base_url_when_empty()
    {
        update_option('wpmautic_options', array(
            'base_url' => ''
        ));
        $this->assertEmpty(Options::get('base_url'));
    }

    public function test_base_url_with_value()
    {
        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com'
        ));
        $this->assertEquals('http://example.com', Options::get('base_url'));
    }

    public function test_script_location_when_empty()
    {
        update_option('wpmautic_options', array());
        $this->assertEquals('header', Options::get('script_location'));
    }

    public function test_script_location_with_value()
    {
        update_option('wpmautic_options', array(
            'script_location' => 'footer'
        ));
        $this->assertEquals('footer', Options::get('script_location'));
    }

    public function test_script_location_with_disabled_value()
    {
        update_option('wpmautic_options', array(
            'script_location' => 'disabled'
        ));
        $this->assertEquals('disabled', Options::get('script_location'));
    }

    public function test_fallback_activated_when_empty()
    {
        update_option('wpmautic_options', array());
        $this->assertTrue(Options::get('fallback_activated'));
    }

    public function test_fallback_activated_with_value()
    {
        update_option('wpmautic_options', array(
            'fallback_activated' => false
        ));
        $this->assertFalse(Options::get('fallback_activated'));
    }
}
