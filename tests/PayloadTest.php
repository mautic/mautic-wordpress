test_url_query_payload_without_referer<?php
/**
 * @package wpmautic\tests
 */

/**
 * Ensure that payload data are valid in the current context
 */
class PayloadTest extends WP_UnitTestCase
{
    public function test_url_query_payload_deprecated_key()
    {
        $payload = wpmautic_get_url_query();

        $this->assertArrayNotHasKey('url', $payload);
        $this->assertArrayNotHasKey('title', $payload);
        $this->assertArrayHasKey('page_url', $payload);
        $this->assertArrayHasKey('page_title', $payload);
    }

    public function test_url_query_payload_without_referer()
    {
        $base_url = 'http://example.org';
        $this->go_to( $base_url );

        $payload = wpmautic_get_url_query();

        $title = sprintf( "%s &#8211; %s", WP_TESTS_TITLE, get_option( 'blogdescription' ) );

        $this->assertEquals($payload['page_url'], $base_url);
        $this->assertEquals($payload['page_title'], $title);
        $this->assertEquals($payload['language'], get_locale());
        if ( version_compare( get_bloginfo('version'), '4.5', '<' ) ) {
            $this->assertEquals($payload['referrer'], null);
        } else {
            $this->assertEquals($payload['referrer'], $base_url);
        }
    }

    public function test_url_query_payload_with_wp_referer()
    {
        if ( version_compare( get_bloginfo('version'), '4.5', '<' ) ) {
            $this->markTestSkipped( 'wp_get_raw_referer function was introduced in WP 4.5' );
            return;
        }

        $base_url = 'http://example.org';
        $_REQUEST['_wp_http_referer'] = 'http://toto.org';
        $this->go_to( $base_url );

        $payload = wpmautic_get_url_query();

        $title = sprintf( "%s &#8211; %s", WP_TESTS_TITLE, get_option( 'blogdescription' ) );

        $this->assertEquals($payload['page_url'], $base_url);
        $this->assertEquals($payload['page_title'], $title);
        $this->assertEquals($payload['language'], get_locale());
        $this->assertEquals($payload['referrer'], 'http://toto.org');

        unset($_REQUEST['_wp_http_referer']);
    }

    public function test_url_query_payload_with_server_referer()
    {
        if ( version_compare( get_bloginfo('version'), '4.5', '<' ) ) {
            $this->markTestSkipped( 'wp_get_raw_referer function was introduced in WP 4.5' );
            return;
        }

        $base_url = 'http://example.org';
        $_SERVER['HTTP_REFERER'] = 'http://toto.org';
        $this->go_to( $base_url );

        $payload = wpmautic_get_url_query();

        $title = sprintf( "%s &#8211; %s", WP_TESTS_TITLE, get_option( 'blogdescription' ) );

        $this->assertEquals($payload['page_url'], $base_url);
        $this->assertEquals($payload['page_title'], $title);
        $this->assertEquals($payload['language'], get_locale());
        $this->assertEquals($payload['referrer'], 'http://toto.org');

        unset($_SERVER['HTTP_REFERER']);
    }
}
