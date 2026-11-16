<?php
/**
 * @package wpmautic\tests
 */

use Mautic\WP_Mautic\Frontend\Form_Embed;

/**
 * Test shared Mautic form embed rendering
 */
class FormEmbedTest extends WPMauticTestCase
{
    public function setUp()
    {
        parent::setUp();

        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com',
        ));
    }

    public function test_empty_form_id_is_blank_on_frontend()
    {
        $embed = new Form_Embed();
        $this->assertSame('', $embed->render(0, false));
    }

    public function test_empty_form_id_shows_placeholder_in_preview()
    {
        $embed = new Form_Embed();
        $html  = $embed->render(0, true);

        $this->assertContains('wpmautic-form-block__setup', $html);
        $this->assertContains('Mautic Form', $html);
    }

    public function test_frontend_outputs_generate_js()
    {
        $embed = new Form_Embed();
        $html  = $embed->render(1, false);

        $this->assertContains('http://example.com/form/generate.js?id=1', $html);
        $this->assertContains('<script', $html);
    }

    public function test_preview_outputs_iframe()
    {
        $embed = new Form_Embed();
        $html  = $embed->render(1, true);

        $this->assertContains('<iframe', $html);
        $this->assertContains('http://example.com/form/1', $html);
        $this->assertContains('wpmautic-form-preview', $html);
    }

    public function test_missing_base_url_is_blank_on_frontend()
    {
        update_option('wpmautic_options', array());

        $embed = new Form_Embed();
        $this->assertSame('', $embed->render(1, false));
    }

    public function test_missing_base_url_shows_notice_in_preview()
    {
        update_option('wpmautic_options', array());

        $embed = new Form_Embed();
        $html  = $embed->render(1, true);

        $this->assertContains('wpmautic-form-block__notice', $html);
        $this->assertContains('WP Mautic settings', $html);
    }
}
