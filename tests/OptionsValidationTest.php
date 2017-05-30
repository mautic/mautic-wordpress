<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test Mautic options validation
 */
class OptionsValidationTest extends WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        require_once(VPMAUTIC_PLUGIN_DIR.'/options.php');
    }

    public function test_validation_when_empty()
    {
        $options = wpmautic_options_validate(array());
        $this->assertArrayHasKey('base_url', $options);
        $this->assertArrayHasKey('script_location', $options);
        $this->assertArrayHasKey('fallback_activated', $options);
        $this->assertEmpty($options['base_url']);
        $this->assertEquals('header', $options['script_location']);
        $this->assertFalse($options['fallback_activated']);
    }

    public function test_validation_with_invalid_script_location()
    {
        $options = wpmautic_options_validate(array(
            'script_location' => 'toto'
        ));
        $this->assertEquals('header', $options['script_location']);
    }

    public function test_validation_with_whitespaces_in_values()
    {
        $options = wpmautic_options_validate(array(
            'base_url' => '    http://example.com    ',
            'script_location' => '    footer    '
        ));
        $this->assertEquals('http://example.com', $options['base_url']);
        $this->assertEquals('footer', $options['script_location']);
    }

    public function test_validation_with_invalid_url()
    {
        $options = wpmautic_options_validate(array(
            'base_url' => 'url'
        ));
        $this->assertEquals('http://url', $options['base_url']);
    }

    public function test_validation_with_invalid_fallback_activated()
    {
        $options = wpmautic_options_validate(array(
            'fallback_activated' => 'toto'
        ));
        $this->assertFalse($options['fallback_activated']);
    }

    public function test_validation_with_valid_fallback_activated()
    {
        $options = wpmautic_options_validate(array(
            'fallback_activated' => '1'
        ));
        $this->assertTrue($options['fallback_activated']);
    }
}
