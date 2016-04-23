<?php
/**
 * Return Corpus API url
 * @return string
 */
function corpus_get_api_url() {
    if(defined('CORPUS_API_URL')) {
        $url = CORPUS_API_URL;
    } else {
        $url = rtrim(get_option('corpus_api_url'), '/');
    }
    return ($url ?: 'http://corpus.govright.org/api');
}

/**
 * Get Corpus API object
 * @return CorpusApiServer
 */
function corpus_get_api() {
    return CorpusAPI::getInstance();
}

/**
 * Get Corpus API model object
 * @param $name string - Model name
 * @return CorpusApiModel
 */
function corpus_get_model($name) {
    return CorpusAPI::getInstance()->$name;
}

/**
 * Extract translations from the model.
 * @param $item array
 * @param null $languageCode string
 * @return array
 */
function corpus_get_locale($item, $languageCode = null) {
    $locales = isset($item['locales']) ? $item['locales'] : $item;
    if($languageCode && !empty($locales[$languageCode])) {
        return $locales[$languageCode];
    }
    if(defined('ICL_LANGUAGE_CODE') && !empty($locales[ICL_LANGUAGE_CODE])) {
        return $locales[ICL_LANGUAGE_CODE];
    }
    $val = array_values($locales);
    return array_shift($val);
}

/**
 * Join shortcode attributes into a string
 * @param array $atts
 * @param bool|true $include_locale
 * @return string
 */
function corpus_atts_string($atts, $include_locale = true) {
    if(empty($atts)) {
        $atts = array();
    }
    if($include_locale && empty($atts['data-locale']) && defined('ICL_LANGUAGE_CODE')) {
        $atts['data-locale'] = ICL_LANGUAGE_CODE;
    }
    $strings = array();
    foreach ($atts as $key => $val) {
        if(is_numeric($key)) {
            $strings[] = $val;
        } else {
            $strings[] = $key . '="' . $val . '"';
        }
    }
    return implode(' ', $strings);
}
