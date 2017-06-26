<?php
/**
 * @package wpmautic\tests
 */

/**
 * Ensure that custom attributes are handled properly in the current context
 */
class CustomAttributesTest extends WP_UnitTestCase
{
    public function test_custom_attributes_are_empty_by_default()
    {
        $payload = wpmautic_get_tracking_attributes();

        $this->assertTrue(is_array($payload));
        $this->assertEmpty($payload);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Message from filter
     */
    public function test_custom_attributes_are_injected_by_filter()
    {
        add_filter('wpmautic_tracking_attributes', function() {
            throw new Exception('Message from filter');
        });
        wpmautic_get_tracking_attributes();
    }

    public function test_custom_attributes_from_filter_are_returned()
    {
        add_filter('wpmautic_tracking_attributes', function($attrs) {
            $attrs['custom'] = 'toto';
            return $attrs;
        });
        $payload = wpmautic_get_tracking_attributes();

        $this->assertArrayHasKey('custom', $payload);
        $this->assertEquals('toto', $payload['custom']);
    }
}
