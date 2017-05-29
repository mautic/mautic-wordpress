<?php
/**
 * @package wpmautic\tests
 */

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
        wpmautic_option('azerty123456');
    }

    public function test_base_url_when_empty()
    {
        update_option('wpmautic_options', array(
            'base_url' => ''
        ));
        $this->assertEmpty(wpmautic_option('base_url'));
    }

    public function test_base_url_with_value()
    {
        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com'
        ));
        $this->assertEquals('http://example.com', wpmautic_option('base_url'));
    }

    public function test_script_location_when_empty()
    {
        update_option('wpmautic_options', array());
        $this->assertEquals('header', wpmautic_option('script_location'));
    }

    public function test_script_location_with_value()
    {
        update_option('wpmautic_options', array(
            'script_location' => 'footer'
        ));
        $this->assertEquals('footer', wpmautic_option('script_location'));
    }
}
