{{-- Icon library, self-hosted and pinned (was https://unpkg.com/lucide@latest: a third-party host, a 302 redirect
     hop on every page load, and a render-blocking script that could change version under us).
     `defer` keeps it off the critical path. Deferred scripts run before DOMContentLoaded, and every call site
     (theme.js, the admin layout, the CMS template) creates icons from a DOMContentLoaded handler or after user
     input, so nothing depends on it being available while the page is still parsing.
     To upgrade: drop the new dist/umd/lucide.min.js in public/assets/vendor/ and change the version here. --}}
<script src="/assets/vendor/lucide-1.48.0.min.js" defer></script>
