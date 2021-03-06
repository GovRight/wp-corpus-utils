(function($) {
    window.GovRight = window.GovRight || {};
    window.GovRight.$models = {};

    window.GovRight.api = function(modelName) {
        var modelSlug = modelName.toLowerCase();
        if(modelSlug[modelSlug.length - 1] !== 's') {
            modelSlug += 's';
        }
        if(!window.GovRight.$models[modelSlug]) {
            window.GovRight.$models[modelSlug] = model(modelName);
        }
        return window.GovRight.$models[modelSlug];
    };

    window.GovRight.getLocale = function(instance, languageCode) {
        var locales = instance.locales ? instance.locales : instance;
        if(languageCode && locales[languageCode]) {
            return locales[languageCode];
        }
        if(window.icl_lang && locales[window.icl_lang]) {
            return locales[window.icl_lang];
        }
        return locales[Object.keys(locales).shift()];
    };

    window.GovRight.getLocaleProp = function(instance, prop, languageCode) {
        var locale = window.GovRight.getLocale(instance, languageCode);
        return locale[prop] || '';
    };

    function model(modelName) {
        return {
            get: get.bind(null, modelName)
        }
    }

    function get(modelName, method, query) {
        if(!query && typeof method === 'object') {
            query = method;
            method = '';
        }
        var apiUrl = (window.GovRight && window.GovRight.corpusApiUrl) || 'http://corpus.govright.org/api';
        var methodUrl = method ? '/' + method : '';
        var queryString = serialize(query || {});
        queryString = queryString ? '?' + queryString : '';
        return $.get(apiUrl + '/' + modelName + methodUrl + queryString);
    }

    function serialize(params, prefix) {
        var str = [];
        for(var p in params) {
            if (params.hasOwnProperty(p)) {
                var k = prefix ? prefix + "[" + encodeURIComponent(p) + "]" : encodeURIComponent(p);
                var v = params[p];
                str.push(typeof v == "object" ? serialize(v, k) : k + "=" + encodeURIComponent(v));
            }
        }
        return str.join("&");
    }
}(jQuery));
