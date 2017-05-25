<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test Mautic Script injection
 */
class ScriptInjectionTest extends WP_UnitTestCase
{
    private function renderRawPage()
    {
        $this->setOutputCallback(create_function('', ''));
        wp_head();
        wp_footer();
        return $this->getActualOutput();
    }

    public function test_script_is_not_injected_when_base_url_is_empty()
    {
        $output = $this->renderRawPage();
        $this->assertNotContains(sprintf("(window,document,'script','/mtc.js','mt')"), $output);
        $this->assertNotContains("['MauticTrackingObject']", $output);
        $this->assertNotContains("mt('send', 'pageview')", $output);
    }

    public function test_script_is_injected_with_valid_base_url()
    {
        $base_url = 'http://example.com';
        update_option('wpmautic_options', array('base_url' => $base_url));

        $output = $this->renderRawPage();

        $this->assertContains(sprintf("(window,document,'script','{$base_url}/mtc.js','mt')"), $output);
        $this->assertContains("['MauticTrackingObject']", $output);
        $this->assertContains("mt('send', 'pageview')", $output);
    }
}
