<?php
/**
 * @package wpmautic\tests
 */

/**
 * Test shortcode handling
 */
class ShortCodeTest extends WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        update_option('wpmautic_options', array(
            'base_url' => 'http://example.com'
        ));
    }

    public function test_shortcode_exists()
    {
        $this->assertTrue(shortcode_exists('mautic'));
        $this->assertTrue(shortcode_exists('mauticcontent'));
        $this->assertTrue(shortcode_exists('mauticvideo'));
        $this->assertTrue(shortcode_exists('mauticform'));
        $this->assertTrue(shortcode_exists('mautictags'));
        $this->assertTrue(shortcode_exists('mauticfocus'));
    }

    public function test_form_shortcode_from_empty_atts()
    {
        $result = wpmautic_form_shortcode(array());
        $this->assertFalse($result);
    }

    public function provideShortCode()
    {
        return array(
            array(
                '[mauticform id="1"]',
                '<script type="text/javascript" src="http://example.com/form/generate.js?id=1"></script>'
            ),
            array(
                '[mautic type="form" id="1"]',
                '<script type="text/javascript" src="http://example.com/form/generate.js?id=1"></script>'
            ),
            array(
                '[mautictags values="tag,-tag2,tag3"]',
                '<img src="http://example.com/mtracking.gif?tags=tag,-tag2,tag3" alt="Mautic Tags" />'
            ),
            array(
                '[mautic type="tags" values="tag,tag2,-tag3"]',
                '<img src="http://example.com/mtracking.gif?tags=tag,tag2,-tag3" alt="Mautic Tags" />'
            ),
            array(
                '[mautictags values="tag,-tag2,tag3"]',
                '<img src="http://example.com/mtracking.gif?tags=tag,-tag2,tag3" alt="Mautic Tags" />'
            ),
            array(
                '[mautic type="tags" values="tag,tag2,-tag3"]',
                '<img src="http://example.com/mtracking.gif?tags=tag,tag2,-tag3" alt="Mautic Tags" />'
            ),
            array(
                '[mauticfocus id="1"]',
                '<script type="text/javascript" src="http://example.com/focus/1.js" async="async"></script>'
            ),
            array(
                '[mautic type="focus" id="1"]',
                '<script type="text/javascript" src="http://example.com/focus/1.js" async="async"></script>'
            ),
            array(
                '[mauticcontent slot="name"]content[/mauticcontent]',
                '<div class="mautic-slot" data-slot-name="name">content</div>'
            ),
            array(
                '[mautic type="content" slot="name"]content[/mautic]',
                '<div class="mautic-slot" data-slot-name="name">content</div>'
            ),
            array(
                '[mautic type="content" slot="name"]<div class="my-class">content</div>[/mautic]',
                '<div class="mautic-slot" data-slot-name="name"><div class="my-class">content</div></div>'
            ),
            array(
                '[mautic type="content" slot="name"]<script type="text/javascript">content</script>[/mautic]',
                '<div class="mautic-slot" data-slot-name="name">content</div>'
            ),
            array(
                '[mautic type="video" src="https://www.youtube.com/watch?v=1234" form-id="1" width="148"]',
                '<video height="360" width="148" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://www.youtube.com/watch?v=1234" form-id="1" width="148"]',
                '<video height="360" width="148" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://vimeo.com/218680983" form-id="1" height="272"]',
                '<video height="272" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://vimeo.com/218680983" form-id="1" height="272"]',
                '<video height="272" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://vimeo.com/218680983" form-id="1" gate-time="25"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="25" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://vimeo.com/218680983" form-id="1" gate-time="25"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="25" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://vimeo.com/218680983" form-id="1" video-type="mp4"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://vimeo.com/218680983" form-id="1" video-type="mp4"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/vimeo" src="https://vimeo.com/218680983" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://example.com/mavideo.mov" form-id="1" video-type="mov"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/mov" src="https://example.com/mavideo.mov" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://example.com/mavideo.mov" form-id="1" video-type="mov"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/mov" src="https://example.com/mavideo.mov" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://example.com/mavideo.mp4" form-id="1"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/mp4" src="https://example.com/mavideo.mp4" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://example.com/mavideo.mp4" form-id="1"]',
                '<video height="360" width="640" data-form-id="1" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/mp4" src="https://example.com/mavideo.mp4" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://www.youtube.com/watch?v=1234" form-id="1" width="148" mautic-video="false"]',
                '<video height="360" width="148" data-form-id="1" data-gate-time="15" data-mautic-video="false">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://www.youtube.com/watch?v=1234" form-id="1" width="148" mautic-video="false"]',
                '<video height="360" width="148" data-form-id="1" data-gate-time="15" data-mautic-video="false">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
            array(
                '[mautic type="video" src="https://www.youtube.com/watch?v=1234" width="148" mautic-video="true"]',
                '<video height="360" width="148" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
            array(
                '[mauticvideo src="https://www.youtube.com/watch?v=1234" width="148" mautic-video="true"]',
                '<video height="360" width="148" data-gate-time="15" data-mautic-video="true">' .
                    '<source type="video/youtube" src="https://www.youtube.com/watch?v=1234" />' .
                '</video>'
            ),
        );
    }

    /**
     * @dataProvider provideShortCode
     * @param string $content
     * @param string $expected
     */
    public function test_shortcode($content, $expected)
    {
        $result = do_shortcode($content);
        $this->assertEquals($expected, $result);
    }

    public function provideShortCodeToBeEmptyWithoutUrl()
    {
        return array(
            array('[mauticform id="1"]'),
            array('[mautic type="form" id="1"]'),
            array('[mautictags values="tag,-tag2,tag3"]'),
            array('[mautic type="tags" values="tag,tag2,-tag3"]'),
            array('[mauticfocus id="1"]'),
            array('[mautic type="focus" id="1"]'),
        );
    }

    /**
     * @dataProvider provideShortCodeToBeEmpty
     * @param string $content
     */
    public function test_shortcode_with_empty_url($content)
    {
        update_option('wpmautic_options', array());

        $result = do_shortcode($content);
        $this->assertEmpty($result);
    }

    public function provideShortCodeToBeEmpty()
    {
        return array(
            array('[mauticform]'),
            array('[mautic type="form"]'),
            array('[mautictags]'),
            array('[mautic type="tags"]'),
            array('[mauticfocus]'),
            array('[mautic type="focus"]'),
        );
    }

    /**
     * @dataProvider provideShortCodeToBeEmpty
     * @param string $content
     */
    public function test_shortcode_to_be_empty($content)
    {
        $result = do_shortcode($content);
        $this->assertEmpty($result);
    }

    public function test_invalid_shortcode_type()
    {
        $result = do_shortcode("[mautic type='azerty']");
        $this->assertEmpty($result);
    }

    public function test_no_src_on_video_shortcode()
    {
        $result = do_shortcode("[mautic type='video']");
        $this->assertEquals(
            'You must provide a video source. Add a src="URL" attribute to your shortcode. '.
            'Replace URL with the source url for your video.',
            $result
        );
    }

    public function test_no_form_id_on_video_shortcode_when_mautic_tracking_disabled()
    {
        $result = do_shortcode("[mautic type='video' src='mavideo.mov' mautic-video='false']");
        $this->assertEquals(
            'You must provide a mautic form id. Add a form-id="#" attribute to your shortcode. '.
            'Replace # with the id of the form you want to use.',
            $result
        );
    }

    public function test_no_video_type_on_video_shortcode()
    {
        $result = do_shortcode("[mautic type='video' src='mavideo.mov' form-id='1']");
        $this->assertEquals(
            'Please define a valid video type with video-type="#".',
            $result
        );
    }
}
