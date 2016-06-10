<?php

add_action('wp_head', function() {
    print '<script>
    window.GovRight = window.GovRight || {};
    window.GovRight.corpusApiUrl = "' . corpus_get_api_url() . '";
</script>';
    $src = plugins_url('wp-corpus-utils/assets/corpus-api.js');
    wp_enqueue_script('wp-corpus-utils-corpus-api', $src, array('jquery'));
}, -999);
