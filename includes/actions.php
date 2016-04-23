<?php

add_action('wp_head', function() {
    print '<script>
    window.GovRight = window.GovRight || {};
    window.GovRight.corpusApiUrl = "' . corpus_get_api_url() . '"
</script>';
});
