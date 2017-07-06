<?php
/**
 * @package wpmautic\tests
 */

/**
 * Ensure that custom attributes are handled properly in the current context
 */
class CustomAttributesTest extends WP_UnitTestCase
{
    public function wpmautic_tracking_attributes_filter_1()
    {
        throw new RuntimeException('Message from filter');
    }

    public function wpmautic_tracking_attributes_filter_2($attrs)
    {
        $attrs['custom'] = 'toto';
        return $attrs;
    }


    public function test_custom_attributes_are_empty_by_default()
    {
        $payload = wpmautic_get_tracking_attributes();

        $this->assertTrue(is_array($payload));
        $this->assertEmpty($payload);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Message from filter
     */
    public function test_custom_attributes_are_injected_by_filter()
    {
        add_filter('wpmautic_tracking_attributes', array($this, 'wpmautic_tracking_attributes_filter_1'));
        wpmautic_get_tracking_attributes();
    }

    public function test_custom_attributes_from_filter_are_returned()
    {
        add_filter('wpmautic_tracking_attributes', array($this, 'wpmautic_tracking_attributes_filter_2'));
        $payload = wpmautic_get_tracking_attributes();

        $this->assertArrayHasKey('custom', $payload);
        $this->assertEquals('toto', $payload['custom']);
    }
}
