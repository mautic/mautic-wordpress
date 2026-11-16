<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test Elementor widget registration hooks
 */
class ElementorLoaderTest extends WPMauticTestCase
{
    public function test_elementor_hooks_are_registered()
    {
        $this->assertNotFalse(
            has_action('elementor/widgets/register'),
            'Elementor widget register hook should be attached'
        );
        $this->assertNotFalse(
            has_action('elementor/elements/categories_registered'),
            'Elementor category hook should be attached'
        );
        $this->assertNotFalse(
            has_action('elementor/preview/enqueue_styles'),
            'Elementor preview style hook should be attached'
        );
        $this->assertNotFalse(
            has_action('elementor/editor/after_enqueue_styles'),
            'Elementor editor style hook should be attached'
        );
    }
}
