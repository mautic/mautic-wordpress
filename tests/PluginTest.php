<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test generic plugin behaviour
 */
class PluginTest extends WP_UnitTestCase
{
    public function test_plugin_is_loaded()
    {
        $active = get_option( 'active_plugins', array() );
        $this->assertContains('mautic-wordpress/wpmautic.php', $active);

        $this->assertTrue(function_exists('wpmautic_option'));
        $this->assertTrue(function_exists('wpmautic_inject_script'));
    }

    public function test_action_loaded()
    {
        $this->assertTrue(false !== has_action('admin_menu', 'wpmautic_settings'));
        $this->assertTrue(false !== has_action('plugins_loaded', 'wpmautic_injector'));
    }

    public function test_filter_loaded()
    {
        $this->assertTrue(false !== has_filter('plugin_action_links', 'wpmautic_plugin_actions'));
    }

    public function test_settings_link_is_present()
    {
        $links = apply_filters('plugin_action_links', array(), 'mautic-wordpress/wpmautic.php');

        $this->assertCount(1, $links);
        $this->assertContains('options-general.php?page=wpmautic', $links[0]);
    }

    public function test_option_page_is_loaded()
    {
        $current_user = get_current_user_id();
        wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

        $this->assertEmpty(menu_page_url('wpmautic', false));
        wpmautic_settings();
        $this->assertEquals(
            admin_url( 'options-general.php?page=wpmautic' ),
            menu_page_url('wpmautic', false)
        );

        wp_set_current_user( $current_user );
    }
}
