<!-- Left Sidebar for Desktop -->
<aside class="hidden lg:flex lg:w-64 lg:flex-col shrink-0 bg-white border-r border-slate-200">
    @include('layouts.sidebar-menu')
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // The badge exists twice (desktop sidebar + mobile drawer), so target them all.
    var badges = document.querySelectorAll('.live-chat-unread-badge');
    if (!badges.length) return;

    // Ring an alert tone on a loop while there's unread customer messages,
    // so an admin/agent notices even if they're not looking at the screen.
    // Stops the moment they open the conversation (unread count hits 0).
    var Ctx = window.AudioContext || window.webkitAudioContext;
    var audioCtx = Ctx ? new Ctx() : null;
    var soundTimer = null;
    var pendingChime = false;

    function unlockAudio() {
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().then(function () {
                if (pendingChime) { pendingChime = false; playChime(); }
            });
        }
    }
    ['click', 'keydown', 'touchstart', 'scroll', 'mousemove'].forEach(function (evt) {
        document.addEventListener(evt, unlockAudio, { passive: true });
    });

    function playChime() {
        if (!audioCtx) return;
        if (audioCtx.state === 'suspended') {
            pendingChime = true;
            unlockAudio();
            return;
        }
        var now = audioCtx.currentTime;
        // Louder "ding-dong" doorbell-style two-note ring (Tawk.to-like), played
        // twice back-to-back per tick so it reads as one persistent, unmissable ring.
        [0, 0.55].forEach(function (ringOffset) {
            [[988, 0], [740, 0.16]].forEach(function (pair) {
                var freq = pair[0], delay = pair[1];
                var osc = audioCtx.createOscillator();
                var gain = audioCtx.createGain();
                osc.type = 'triangle';
                osc.frequency.value = freq;
                var t = now + ringOffset + delay;
                gain.gain.setValueAtTime(0.0001, t);
                gain.gain.exponentialRampToValueAtTime(0.6, t + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, t + 0.45);
                osc.connect(gain).connect(audioCtx.destination);
                osc.start(t);
                osc.stop(t + 0.5);
            });
        });
    }

    function poll() {
        fetch('{{ route('admin.live-chat.unread-count') }}').then(function (r) { return r.ok ? r.json() : null; }).then(function (d) {
            if (!d) return;
            if (d.count > 0) {
                badges.forEach(function (b) {
                    b.textContent = d.count > 99 ? '99+' : d.count;
                    b.classList.remove('hidden');
                });
                if (!soundTimer) {
                    playChime();
                    soundTimer = setInterval(playChime, 2000);
                }
            } else {
                badges.forEach(function (b) { b.classList.add('hidden'); });
                if (soundTimer) { clearInterval(soundTimer); soundTimer = null; }
            }
        }).catch(function () {});
    }
    poll();
    setInterval(poll, 5000);
});
</script>
