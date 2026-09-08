/**
 * Khan Gadget - Bidirectional Blog & Content Translator (Google GTX Neural Engine)
 * Translates page body, titles, excerpts, rich-text, search placeholder, and document title.
 * - Switching to 'EN': Everything becomes English (including posts originally written in Bengali).
 * - Switching to 'বাং': Everything becomes Bengali (including posts originally written in English).
 * - Header, footer, navigation drawers, and live chat are strictly protected and never modified.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'kg_lang_pref';
    var CACHE_PREFIX = 'kg_tr_v5_';
    var currentLang = 'en';
    var isTranslating = false;
    var activeScopes = [];
    var mutationObserver = null;

    // Fast string hash for local storage cache keys
    function hashString(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0;
        }
        return Math.abs(hash).toString(36) + '_' + str.length;
    }

    function getCache(targetLang, text) {
        try {
            return localStorage.getItem(CACHE_PREFIX + targetLang + '_' + hashString(text));
        } catch (e) {
            return null;
        }
    }

    function setCache(targetLang, text, translated) {
        try {
            localStorage.setItem(CACHE_PREFIX + targetLang + '_' + hashString(text), translated);
        } catch (e) {}
    }

    // Split leading and trailing whitespace to preserve inline element spacing
    function splitWhitespace(str) {
        var match = str.match(/^(\s*)([\s\S]*?)(\s*)$/);
        if (!match) return { leading: '', core: str, trailing: '' };
        return {
            leading: match[1] || '',
            core: match[2] || '',
            trailing: match[3] || ''
        };
    }

    // Check if an element or its ancestor is excluded from translation
    function isExcluded(el) {
        if (!el || el.nodeType !== Node.ELEMENT_NODE) return false;
        return !!el.closest('header, footer, nav[aria-label="Mobile footer navigation"], #kg-mobile-drawer, #kg-lang-toggle, .notranslate, [translate="no"], script, style, noscript, svg, code');
    }

    // Check if a string needs translation to targetLang
    function needsTranslation(text, targetLang) {
        if (!text) return false;
        var trimmed = text.trim();
        if (!trimmed) return false;

        // Skip if there are no alphabetic or Bengali characters (e.g. symbols, numbers only)
        if (!/[a-zA-Z\u0980-\u09FF]/.test(trimmed)) return false;

        if (targetLang === 'bn') {
            // Needs translation to Bengali if it contains any English / Latin letters
            return /[a-zA-Z]/.test(trimmed);
        } else if (targetLang === 'en') {
            // Needs translation to English if it contains any Bengali characters
            return /[\u0980-\u09FF]/.test(trimmed);
        }
        return false;
    }

    // Google Translate GTX endpoint
    function translateSingle(text, targetLang) {
        return new Promise(function (resolve) {
            if (!text || !text.trim()) {
                resolve(text);
                return;
            }

            var cached = getCache(targetLang, text);
            if (cached) {
                resolve(cached);
                return;
            }

            var url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=' + targetLang + '&dt=t&q=' + encodeURIComponent(text);

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.timeout = 10000;

            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data && data[0] && Array.isArray(data[0])) {
                            var translated = data[0].map(function (chunk) {
                                return (chunk && chunk[0]) ? chunk[0] : '';
                            }).join('');
                            if (translated && translated.trim()) {
                                setCache(targetLang, text, translated);
                                resolve(translated);
                                return;
                            }
                        }
                    } catch (err) {}
                }
                resolve(text);
            };

            xhr.onerror = function () { resolve(text); };
            xhr.ontimeout = function () { resolve(text); };
            xhr.send();
        });
    }

    // Split long paragraphs if necessary (>1200 chars)
    function translateTextWithChunking(text, targetLang) {
        if (text.length <= 1200) {
            return translateSingle(text, targetLang);
        }

        var parts = text.split(/(?<=[।!?.\n])\s+/);
        var chunks = [];
        var cur = '';

        parts.forEach(function (p) {
            if (cur && (cur + ' ' + p).length > 1000) {
                chunks.push(cur);
                cur = p;
            } else {
                cur = cur ? (cur + ' ' + p) : p;
            }
        });
        if (cur) chunks.push(cur);

        return Promise.all(chunks.map(function (c) {
            return translateSingle(c, targetLang);
        })).then(function (results) {
            var joined = results.join(' ');
            setCache(targetLang, text, joined);
            return joined;
        });
    }

    // Run array of tasks with concurrency limit
    function runConcurrent(tasks, limit) {
        return new Promise(function (resolve) {
            var results = new Array(tasks.length);
            var currentIndex = 0;
            var completed = 0;

            if (tasks.length === 0) {
                resolve(results);
                return;
            }

            function worker() {
                if (currentIndex >= tasks.length) return;
                var idx = currentIndex++;
                tasks[idx]().then(function (res) {
                    results[idx] = res;
                    completed++;
                    if (completed === tasks.length) {
                        resolve(results);
                    } else {
                        worker();
                    }
                }).catch(function () {
                    results[idx] = null;
                    completed++;
                    if (completed === tasks.length) {
                        resolve(results);
                    } else {
                        worker();
                    }
                });
            }

            for (var i = 0; i < Math.min(limit, tasks.length); i++) {
                worker();
            }
        });
    }

    // Collect all translatable text nodes, input placeholders, and img alts
    function collectItems(root) {
        var textItems = [];
        var placeholderItems = [];
        var altItems = [];

        // 1. Text nodes
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                var val = node.nodeValue;
                if (!val || !val.trim()) return NodeFilter.FILTER_REJECT;
                var parent = node.parentElement;
                if (!parent || isExcluded(parent)) return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });

        var n;
        while ((n = walker.nextNode())) {
            if (n.__origText === undefined) {
                n.__origText = n.nodeValue;
            }
            var parts = splitWhitespace(n.__origText);
            if (parts.core) {
                textItems.push({
                    node: n,
                    leading: parts.leading,
                    core: parts.core,
                    trailing: parts.trailing
                });
            }
        }

        // 2. Input / textarea placeholders
        var inputs = root.querySelectorAll('input[placeholder], textarea[placeholder]');
        inputs.forEach(function (el) {
            if (isExcluded(el)) return;
            if (el.__origPlaceholder === undefined) {
                el.__origPlaceholder = el.placeholder;
            }
            var parts = splitWhitespace(el.__origPlaceholder);
            if (parts.core) {
                placeholderItems.push({
                    element: el,
                    leading: parts.leading,
                    core: parts.core,
                    trailing: parts.trailing
                });
            }
        });

        // 3. Image alt texts
        var imgs = root.querySelectorAll('img[alt]');
        imgs.forEach(function (img) {
            if (isExcluded(img)) return;
            if (img.__origAlt === undefined) {
                img.__origAlt = img.alt || '';
            }
            var parts = splitWhitespace(img.__origAlt);
            if (parts.core) {
                altItems.push({
                    element: img,
                    leading: parts.leading,
                    core: parts.core,
                    trailing: parts.trailing
                });
            }
        });

        return {
            textItems: textItems,
            placeholderItems: placeholderItems,
            altItems: altItems
        };
    }

    // Update UI toggle button state
    function updateToggleButtons(lang, loading) {
        var toggles = document.querySelectorAll('#kg-lang-toggle');
        toggles.forEach(function (toggle) {
            var buttons = toggle.querySelectorAll('.kg-lang-btn');
            buttons.forEach(function (btn) {
                if (loading) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                } else {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }

                if (btn.dataset.lang === lang) {
                    btn.classList.add('bg-foreground', 'text-background', 'shadow-xs');
                    btn.classList.remove('text-muted-foreground');
                    if (loading) {
                        btn.classList.add('animate-pulse');
                    } else {
                        btn.classList.remove('animate-pulse');
                    }
                } else {
                    btn.classList.remove('bg-foreground', 'text-background', 'shadow-xs', 'animate-pulse');
                    btn.classList.add('text-muted-foreground');
                }
            });
        });
    }

    // Translate page to target language ('en' or 'bn')
    function applyLanguage(targetLang) {
        if (isTranslating && targetLang === currentLang) return;
        currentLang = targetLang;
        try {
            localStorage.setItem(STORAGE_KEY, targetLang);
        } catch (e) {}

        isTranslating = true;
        updateToggleButtons(targetLang, true);

        // Store original document title
        if (document.__origTitle === undefined) {
            document.__origTitle = document.title;
        }

        // Collect all items across all active scopes
        var allTextItems = [];
        var allPlaceholderItems = [];
        var allAltItems = [];

        activeScopes.forEach(function (scope) {
            var res = collectItems(scope);
            allTextItems = allTextItems.concat(res.textItems);
            allPlaceholderItems = allPlaceholderItems.concat(res.placeholderItems);
            allAltItems = allAltItems.concat(res.altItems);
        });

        // Find unique core strings that need translation to targetLang
        var uniqueCoresMap = {};
        var uniqueCoresList = [];

        function registerCore(core) {
            if (needsTranslation(core, targetLang) && !uniqueCoresMap[core]) {
                uniqueCoresMap[core] = true;
                uniqueCoresList.push(core);
            }
        }

        allTextItems.forEach(function (it) { registerCore(it.core); });
        allPlaceholderItems.forEach(function (it) { registerCore(it.core); });
        allAltItems.forEach(function (it) { registerCore(it.core); });

        // Check document title
        if (document.__origTitle && needsTranslation(document.__origTitle, targetLang)) {
            registerCore(document.__origTitle);
        }

        // Translation map
        var translationMap = {};

        // If no strings need translation, just apply existing / original and finish
        if (uniqueCoresList.length === 0) {
            applyResultsToDOM(allTextItems, allPlaceholderItems, allAltItems, translationMap, targetLang);
            isTranslating = false;
            updateToggleButtons(targetLang, false);
            return;
        }

        // Create tasks for concurrent execution
        var tasks = uniqueCoresList.map(function (core) {
            return function () {
                return translateTextWithChunking(core, targetLang).then(function (translated) {
                    translationMap[core] = translated;
                });
            };
        });

        // Run with concurrency pool of 6
        runConcurrent(tasks, 6).then(function () {
            applyResultsToDOM(allTextItems, allPlaceholderItems, allAltItems, translationMap, targetLang);
            isTranslating = false;
            updateToggleButtons(targetLang, false);
        }).catch(function (err) {
            console.error('Translation error:', err);
            applyResultsToDOM(allTextItems, allPlaceholderItems, allAltItems, translationMap, targetLang);
            isTranslating = false;
            updateToggleButtons(targetLang, false);
        });
    }

    // Apply translations or restored originals to DOM
    function applyResultsToDOM(allTextItems, allPlaceholderItems, allAltItems, translationMap, targetLang) {
        // Text nodes
        allTextItems.forEach(function (it) {
            if (translationMap[it.core]) {
                it.node.nodeValue = it.leading + translationMap[it.core] + it.trailing;
            } else if (!needsTranslation(it.core, targetLang)) {
                // If it already matches the target language or doesn't need translation, restore original
                it.node.nodeValue = it.node.__origText;
            }
        });

        // Placeholders
        allPlaceholderItems.forEach(function (it) {
            if (translationMap[it.core]) {
                it.element.placeholder = it.leading + translationMap[it.core] + it.trailing;
            } else if (!needsTranslation(it.core, targetLang)) {
                it.element.placeholder = it.element.__origPlaceholder;
            }
        });

        // Image alts
        allAltItems.forEach(function (it) {
            if (translationMap[it.core]) {
                it.element.alt = it.leading + translationMap[it.core] + it.trailing;
            } else if (!needsTranslation(it.core, targetLang)) {
                it.element.alt = it.element.__origAlt;
            }
        });

        // Document title
        if (document.__origTitle) {
            if (translationMap[document.__origTitle]) {
                document.title = translationMap[document.__origTitle];
            } else if (!needsTranslation(document.__origTitle, targetLang)) {
                document.title = document.__origTitle;
            }
        }
    }

    // Initialize toggle and scopes
    function initTranslateToggle(scopeSelector) {
        var scope = document.querySelector(scopeSelector || '#blog-translate-scope');
        if (!scope) return;

        if (activeScopes.indexOf(scope) === -1) {
            activeScopes.push(scope);
        }

        // Wire up all buttons inside #kg-lang-toggle
        var toggles = document.querySelectorAll('#kg-lang-toggle');
        toggles.forEach(function (toggle) {
            var buttons = toggle.querySelectorAll('.kg-lang-btn');
            buttons.forEach(function (btn) {
                if (btn.__kgBound) return;
                btn.__kgBound = true;

                btn.addEventListener('click', function () {
                    var target = btn.dataset.lang;
                    if (target) {
                        applyLanguage(target);
                    }
                });
            });
        });

        // Setup MutationObserver to automatically translate newly added content (e.g. infinite scroll)
        if (!mutationObserver && window.MutationObserver) {
            var debounceTimer = null;
            mutationObserver = new MutationObserver(function (mutations) {
                var hasNewNodes = false;
                mutations.forEach(function (m) {
                    if (m.addedNodes && m.addedNodes.length > 0) {
                        for (var i = 0; i < m.addedNodes.length; i++) {
                            var node = m.addedNodes[i];
                            if (node.nodeType === Node.ELEMENT_NODE && !isExcluded(node)) {
                                hasNewNodes = true;
                                break;
                            }
                        }
                    }
                });

                if (hasNewNodes) {
                    if (debounceTimer) clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        applyLanguage(currentLang);
                    }, 150);
                }
            });

            activeScopes.forEach(function (sc) {
                mutationObserver.observe(sc, {
                    childList: true,
                    subtree: true
                });
            });
        }

        // Listen for custom content update events (e.g. load-more ajax completion)
        window.addEventListener('kg-content-updated', function () {
            setTimeout(function () {
                applyLanguage(currentLang);
            }, 100);
        });

        // Check stored preference
        var pref = 'en';
        try {
            pref = localStorage.getItem(STORAGE_KEY) || 'en';
        } catch (e) {}

        applyLanguage(pref);
    }

    // Export globally
    window.kgInitTranslateToggle = initTranslateToggle;
    window.kgApplyLanguage = applyLanguage;
})();
