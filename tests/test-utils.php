<?php

class UtilsTest extends WP_UnitTestCase {

    public $locales = array(
        'en' => array('title' => 'English title'),
        'fr' => array('title' => 'Titre français'),
        'ar' => array('title' => 'عنوان العربية')
    );

    function test_get_api_url() {
        $this->assertEquals(corpus_get_api_url(), 'http://corpus.govright.org/api');
    }

    function test_get_model() {
        // Make sure this the same instance
        $model1 = corpus_get_model('Law');
        $model2 = corpus_get_model('Law');
        $model3 = corpus_get_model('Laws');
        $this->assertEquals($model1, $model2);
        $this->assertEquals($model1, $model3);
    }

    function test_get_locale() {
        // Should return the first available locale
        $this->assertEquals(corpus_get_locale($this->locales), $this->locales['en']);
    }

    function test_get_locale_with_code() {
        // Test with a language code provided
        $this->assertEquals(corpus_get_locale($this->locales, 'ar'), $this->locales['ar']);
    }

    // This test must go after other locale tests
    // because you can't 'undefine' the WPML constant
    function test_get_locale_with_wpml() {
        // Simulate WPML language code constant
        define('ICL_LANGUAGE_CODE', 'fr');
        $this->assertEquals(corpus_get_locale($this->locales), $this->locales['fr']);
    }

    function test_atts_string() {
        $atts = array(
            0 => 'required',
            'src' => 'http://example.com',
            'data-slug' => 'some-law-slug'
        );
        $this->assertEquals('required src="http://example.com" data-slug="some-law-slug"', corpus_atts_string($atts, false));
        $this->assertEquals('required src="http://example.com" data-slug="some-law-slug" data-locale="fr"', corpus_atts_string($atts));
    }
}

