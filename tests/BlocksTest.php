<?php
/**
 * @package wpmautic\tests
 */

use Mautic\WP_Mautic\Frontend\Blocks;

/**
 * Test Gutenberg form block rendering
 */
class BlocksTest extends WPMauticTestCase
{
    /**
     * @var Blocks
     */
    private $blocks;

    /**
     * @var mixed
     */
    private $previous_rest_route;

    public function setUp()
    {
        parent::setUp();

        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com',
        ));

        $this->blocks = new Blocks();
        $this->previous_rest_route = isset($GLOBALS['wp']->query_vars['rest_route'])
            ? $GLOBALS['wp']->query_vars['rest_route']
            : null;
    }

    public function tearDown()
    {
        if (null === $this->previous_rest_route) {
            unset($GLOBALS['wp']->query_vars['rest_route']);
        } else {
            $GLOBALS['wp']->query_vars['rest_route'] = $this->previous_rest_route;
        }

        parent::tearDown();
    }

    public function test_editor_script_uses_available_block_editor_handle()
    {
        $this->blocks->wpmautic_register_form_block();
        $script = wp_scripts()->query('wpmautic-form-block');

        $this->assertNotFalse($script);

        global $wp_version;
        if (version_compare($wp_version, '5.3', '>=')) {
            $this->assertContains('wp-block-editor', $script->deps);
            $this->assertContains('wp-server-side-render', $script->deps);
        } else {
            $this->assertContains('wp-editor', $script->deps);
            $this->assertNotContains('wp-block-editor', $script->deps);
        }
    }

    public function test_frontend_render_uses_generate_js()
    {
        $html = $this->blocks->wpmautic_render_form_block(array('formId' => '1'));

        $this->assertContains('http://example.com/form/generate.js?id=1', $html);
        $this->assertNotContains('<iframe', $html);
    }

    public function test_public_rest_post_render_uses_generate_js()
    {
        $this->set_rest_route('/wp/v2/pages/56');

        $html = $this->blocks->wpmautic_render_form_block(array('formId' => '1'));

        $this->assertContains('http://example.com/form/generate.js?id=1', $html);
        $this->assertNotContains('<iframe', $html);
    }

    public function test_public_rest_post_edit_context_uses_generate_js()
    {
        $this->set_rest_route('/wp/v2/posts/12');
        $_GET['context'] = 'edit';

        $html = $this->blocks->wpmautic_render_form_block(array('formId' => '1'));

        unset($_GET['context']);

        $this->assertContains('http://example.com/form/generate.js?id=1', $html);
        $this->assertNotContains('<iframe', $html);
    }

    public function test_block_renderer_request_uses_iframe_preview()
    {
        $this->set_rest_route('/wp/v2/block-renderer/wp-mautic/form');

        $html = $this->blocks->wpmautic_render_form_block(array('formId' => '1'));

        $this->assertContains('<iframe', $html);
        $this->assertContains('http://example.com/form/1', $html);
        $this->assertContains('wpmautic-form-preview', $html);
        $this->assertNotContains('generate.js', $html);
    }

    /**
     * @param string $route REST route query var.
     * @return void
     */
    private function set_rest_route($route)
    {
        if (!isset($GLOBALS['wp']->query_vars) || !is_array($GLOBALS['wp']->query_vars)) {
            $GLOBALS['wp']->query_vars = array();
        }

        $GLOBALS['wp']->query_vars['rest_route'] = $route;
    }
}
