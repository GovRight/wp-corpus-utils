<?php

class ApiTest extends WP_UnitTestCase {
    public $api;

    function setUp() {
        $this->api = corpus_get_api();
    }

    function test_find() {
        $laws = $this->api->laws([
            'where' => [ 'defaultLocale' => 'en' ],
            'limit' => 3
        ]);
        $this->assertFalse(is_wp_error($laws), var_export($laws, true));
        $this->assertTrue(is_array($laws));
        $this->assertCount(3, $laws);
        foreach($laws as $law) {
            $this->assertEquals($law['defaultLocale'], 'en');
        }
    }

    function test_find_by_id() {
        $law = $this->api->law('567028d5219fffbb2d363f38');
        $this->assertFalse(is_wp_error($law), var_export($law, true));
        $this->assertEquals('morocco-penal-revision', $law['slug']);
    }

    function test_find_one() {
        $law = $this->api->Laws->findOne([
            'where' => [
                'slug' => 'morocco-penal-revision',
                'revisionIndex' => 1
            ]
        ]);
        $this->assertFalse(is_wp_error($law), var_export($law, true));
        $this->assertEquals('morocco-penal-revision', $law['slug']);
        $this->assertEquals(1, $law['revisionIndex']);
    }

    function test_count() {
        $res = $this->api->laws->count([
            'where' => [ 'slug' => 'morocco-penal-revision' ]
        ]);
        $this->assertFalse(is_wp_error($res), var_export($res, true));
        $this->assertEquals(2, $res['count']);
    }

    function test_relations() {
        $law = $this->api->law('567028d5219fffbb2d363f38', [
            'include' => [ 'user', 'discussions' ]
        ]);
        $this->assertFalse(is_wp_error($law), var_export($law, true));
        $this->assertTrue(is_array($law['user']));
        $this->assertTrue(is_array($law['discussions']));
    }

    function test_custom_remote() {
        $law = $this->api->law->package([
            'slug' => 'morocco-constitution-2011',
            'rev' => 0
        ]);
        $this->assertFalse(is_wp_error($law), var_export($law, true));
        $this->assertEquals('morocco-constitution-2011', $law['slug']);
        $this->assertTrue(is_array($law['nodes']));
    }

    function test_get_method() {
        $law = $this->api->law->get([
            'method' => 'package',
            'query' => [
                'slug' => 'morocco-constitution-2011',
                'rev' => 0
            ]
        ]);
        $this->assertFalse(is_wp_error($law), var_export($law, true));
        $this->assertEquals('morocco-constitution-2011', $law['slug']);
    }
}
