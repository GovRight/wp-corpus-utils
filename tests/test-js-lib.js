var test = QUnit.test;
var api = GovRight.api;
var laws = api('laws');
var timeout = function(cb) {
    setTimeout(cb, 500);
};
//GovRight.corpusApiUrl = 'http://localhost:3000/api';

test('GovRight api method should exist', function(assert) {
    assert.ok(api);
    assert.strictEqual(typeof api, 'function');
});

test('GovRight api method should return a model object', function(assert) {
    assert.ok(laws);
    assert.ok(laws.get);
    assert.strictEqual(typeof laws.get, 'function');
});

test('GovRight api method should cache a model object', function(assert) {
    assert.strictEqual(laws, api('law'));
    assert.strictEqual(laws, api('Law'));
    assert.strictEqual(laws, api('Laws'));
});

test('Model object should reach Corpus API', function(assert) {
    var done = assert.async();
    laws.get().then(function(data) {
        assert.ok(data instanceof Array);
        data.forEach(function(l) {
            assert.ok(l.slug);
        });
        timeout(done);
    });
});

test('Model object should reach models by id', function(assert) {
    var done = assert.async();
    laws.get('567028d5219fffbb2d363f38').then(function(data) {
        assert.ok(data instanceof Object);
        assert.strictEqual(data.slug, 'morocco-penal-revision');
        timeout(done);
    });
});

test('Model object should find in a set', function(assert) {
    var done = assert.async();
    var filter = {
        where: {
            slug: 'morocco-penal-revision'
        }
    };
    laws.get({filter: filter}).then(function(data) {
        assert.ok(data instanceof Array);
        data.forEach(function(l) {
            assert.strictEqual(l.slug, 'morocco-penal-revision');
        });
        timeout(done);
    });
});

test('Model object should reach models through `findOne` method', function(assert) {
    var done = assert.async();
    var filter = {
        where: {
            slug: 'morocco-penal-revision',
            revisionIndex: 1
        }
    };
    laws.get('findOne', {filter: filter}).then(function(data) {
        assert.ok(data instanceof Object);
        assert.strictEqual(data.slug, 'morocco-penal-revision');
        assert.strictEqual(data.revisionIndex, 1);
        timeout(done);
    });
});

test('Model object should get instances count', function(assert) {
    var done = assert.async();
    var where = {
        slug: 'morocco-penal-revision'
    };
    laws.get('count', {where: where}).then(function(data) {
        assert.ok(data instanceof Object);
        assert.strictEqual(data.count, 2);
        timeout(done);
    });
});

test('Model object should get relations', function(assert) {
    var done = assert.async();
    var filter = {
        include: ['user', 'discussions']
    };
    laws.get('567028d5219fffbb2d363f38', {filter: filter}).then(function(data) {
        assert.ok(data instanceof Object);
        assert.strictEqual(data.slug, 'morocco-penal-revision');
        assert.ok(data.user instanceof Object);
        assert.ok(data.discussions instanceof Array);
        timeout(done);
    });
});

test('Model object should get custom remote', function(assert) {
    var done = assert.async();
    var params = {
        slug: 'morocco-penal-revision',
        rev: 0
    };
    laws.get('package', params).then(function(data) {
        assert.ok(data instanceof Object);
        assert.strictEqual(data.slug, 'morocco-penal-revision');
        assert.strictEqual(data.revisionIndex, 0);
        assert.ok(data.nodes instanceof Array);
        timeout(done);
    });
});
