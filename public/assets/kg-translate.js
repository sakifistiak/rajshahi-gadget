(function () {
    var TARGET = 'bn';
    var SOURCE = 'en';
    var STORAGE_KEY = 'kg_lang_pref';

    function walkTextNodes(root) {
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                var value = node.nodeValue;
                if (!value || !value.replace(/\s+/g, '')) return NodeFilter.FILTER_REJECT;
                var parent = node.parentElement;
                if (!parent) return NodeFilter.FILTER_REJECT;
                if (parent.closest('.notranslate, script, style, noscript')) return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        var nodes = [];
        var n;
        while ((n = walker.nextNode())) nodes.push(n);
        return nodes;
    }

    function chunkText(text, maxLen) {
        if (text.length <= maxLen) return [text];
        var parts = text.split(/(?<=[.!?।])\s+/);
        var chunks = [];
        var current = '';
        parts.forEach(function (p) {
            if (current && (current + ' ' + p).trim().length > maxLen) {
                chunks.push(current.trim());
                current = p;
            } else {
                current = (current + ' ' + p).trim();
            }
        });
        if (current) chunks.push(current);
        return chunks.length ? chunks : [text.slice(0, maxLen)];
    }

    function cacheKeyFor(text) {
        return 'kg_tr_bn_' + text.length + '_' + text.slice(0, 40).replace(/[^a-zA-Z0-9]/g, '');
    }

    function translateChunk(chunk) {
        return fetch('https://api.mymemory.translated.net/get?q=' + encodeURIComponent(chunk) + '&langpair=' + SOURCE + '|' + TARGET)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                return (data && data.responseData && data.responseData.translatedText) ? data.responseData.translatedText : chunk;
            })
            .catch(function () { return chunk; });
    }

    function translateText(text, done) {
        var key = cacheKeyFor(text);
        var cached = null;
        try { cached = localStorage.getItem(key); } catch (e) {}
        if (cached) { done(cached); return; }

        var chunks = chunkText(text, 480);
        Promise.all(chunks.map(translateChunk)).then(function (results) {
            var joined = results.join(' ');
            try { localStorage.setItem(key, joined); } catch (e) {}
            done(joined);
        });
    }

    function initTranslateToggle(scopeSelector) {
        var toggle = document.getElementById('kg-lang-toggle');
        var scope = document.querySelector(scopeSelector);
        if (!toggle || !scope) return;

        var nodes = walkTextNodes(scope);
        var originals = nodes.map(function (n) { return n.nodeValue; });
        var buttons = Array.prototype.slice.call(toggle.querySelectorAll('.kg-lang-btn'));

        function setActive(lang) {
            buttons.forEach(function (btn) {
                if (btn.dataset.lang === lang) {
                    btn.classList.add('bg-foreground', 'text-background');
                    btn.classList.remove('text-muted-foreground');
                } else {
                    btn.classList.remove('bg-foreground', 'text-background');
                    btn.classList.add('text-muted-foreground');
                }
            });
        }

        function setLoading(isLoading) {
            buttons.forEach(function (b) { b.disabled = isLoading; b.style.opacity = isLoading ? '0.5' : ''; });
        }

        function toEnglish() {
            nodes.forEach(function (n, i) { n.nodeValue = originals[i]; });
            try { localStorage.setItem(STORAGE_KEY, 'en'); } catch (e) {}
            setActive('en');
        }

        function toBangla() {
            if (!nodes.length) { setActive('bn'); return; }
            setLoading(true);
            var pending = nodes.length;
            nodes.forEach(function (n, i) {
                translateText(originals[i], function (translated) {
                    n.nodeValue = translated;
                    pending--;
                    if (pending === 0) setLoading(false);
                });
            });
            try { localStorage.setItem(STORAGE_KEY, 'bn'); } catch (e) {}
            setActive('bn');
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                if (btn.dataset.lang === 'bn') toBangla(); else toEnglish();
            });
        });

        var pref = null;
        try { pref = localStorage.getItem(STORAGE_KEY); } catch (e) {}
        if (pref === 'bn') { toBangla(); } else { setActive('en'); }
    }

    window.kgInitTranslateToggle = initTranslateToggle;
})();
