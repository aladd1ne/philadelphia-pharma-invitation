(function () {
    var hero = document.querySelector('.hero');
    var content = document.querySelector('.hero__content');
    var side = document.querySelector('.hero__side');
    var heroGrid = document.querySelector('.hero__grid-lines');
    var heroLogoClient = document.querySelector('.hero__logo--client');
    var heroAgency = document.querySelector('.hero__agency');
    var scrollHint = document.querySelector('.scroll-hint');
    var programme = document.getElementById('programme');
    var programmeVanta = document.getElementById('programmeVanta');
    var programmeContainer = programme ? programme.querySelector('.container') : null;
    var preloader = document.getElementById('pagePreloader');

    var mouseX = 0, mouseY = 0;
    var currentX = 0, currentY = 0;

    function getScrollY() {
        if (typeof window.scrollY === 'number') {
            return window.scrollY;
        }
        var se = document.scrollingElement || document.documentElement;
        return se.scrollTop || 0;
    }

    /** 0 = off, 1 = full (reduced motion / coarse touch get lower values). */
    function getScrollParallaxStrength() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return 0.35;
        }
        var narrow = window.matchMedia('(max-width: 768px)').matches;
        var coarse = window.matchMedia('(pointer: coarse)').matches;
        if (narrow && coarse) {
            return 0;
        }
        if (narrow) {
            return 0.5;
        }
        return 1;
    }

    function lerp(a, b, t) {
        return a + (b - a) * t;
    }

    function clamp(v, lo, hi) {
        return Math.min(hi, Math.max(lo, v));
    }

    /** 0 = section below / barely in view, 1 = fully “entered”; reverses when scrolling up. */
    function programmeScrollProgress(el) {
        if (!el) return 0;
        var rect = el.getBoundingClientRect();
        var vh = window.innerHeight;
        var start = vh * 0.9;
        var span = Math.max(vh * 0.62, 320);
        return clamp((start - rect.top) / span, 0, 1);
    }

    function updateParallax() {
        currentX = lerp(currentX, mouseX, 0.06);
        currentY = lerp(currentY, mouseY, 0.06);

        var sy = getScrollY();
        var S = getScrollParallaxStrength();
        var heroH = hero ? hero.offsetHeight : 1;
        var heroT = S > 0 ? clamp(sy, 0, heroH) : 0;

        var progU = 0;
        if (S > 0 && programme) {
            var progH = programme.offsetHeight;
            var progTopDoc = programme.getBoundingClientRect().top + sy;
            progU = clamp(sy - progTopDoc, 0, progH);
        }

        var cx = currentX * -0.15;
        var heroShift = heroT * 0.45 * S;
        var cy = currentY * -0.15 - heroShift;
        if (content) {
            content.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0)';
        }

        var sx = currentX * 0.12;
        var syM = currentY * 0.12 + heroT * 0.3 * S;
        if (side) {
            side.style.transform = 'translate3d(' + sx + 'px,' + syM + 'px,0)';
        }

        if (hero) {
            var bgNudge = S > 0 ? (heroT / heroH) * 12 * S : 0;
            var bgY = 50 + currentY * 0.06 + bgNudge;
            hero.style.backgroundPosition =
                (50 + currentX * 0.06) + '% ' + bgY + '%';
        }

        if (heroGrid) {
            heroGrid.style.transform =
                S > 0 ? 'translate3d(0,' + heroT * 0.38 * S + 'px,0)' : '';
        }
        var heroBrandShift = S > 0 ? 'translate3d(0,' + heroT * 0.15 * S + 'px,0)' : '';
        if (heroLogoClient) {
            heroLogoClient.style.transform = heroBrandShift;
        }
        if (heroAgency) {
            heroAgency.style.transform = heroBrandShift;
        }
        if (scrollHint) {
            var narrowViewport = window.matchMedia('(max-width: 768px)').matches;
            /* Mobile: keep transform from CSS (centered -50% X); inline would override reveal + break centering. */
            if (narrowViewport) {
                scrollHint.style.transform = '';
            } else if (S > 0) {
                scrollHint.style.transform =
                    'translate3d(-50%,' + heroT * 0.12 * S + 'px,0)';
            } else {
                scrollHint.style.transform = '';
            }
        }

        if (programmeVanta) {
            programmeVanta.style.transform =
                S > 0 ? 'translate3d(0,' + progU * 0.24 * S + 'px,0)' : '';
        }
        if (programmeContainer) {
            programmeContainer.style.transform =
                S > 0 ? 'translate3d(0,' + progU * -0.16 * S + 'px,0)' : '';
        }

        if (programme) {
            var ps = window.matchMedia('(prefers-reduced-motion: reduce)').matches
                ? 1
                : programmeScrollProgress(programme);
            programme.style.setProperty('--prog-scroll', ps.toFixed(4));
        }

        requestAnimationFrame(updateParallax);
    }

    window.addEventListener('mousemove', function (e) {
        mouseX = (e.clientX / window.innerWidth  - 0.5) * 10;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 10;
    });

    requestAnimationFrame(updateParallax);

    window.addEventListener('load', function () {
        document.body.classList.add('is-loaded');

        setTimeout(function () {
            if (preloader) {
                preloader.classList.add('page-preloader--hidden');
            }
            setTimeout(function () {
                document.body.classList.add('hero-revealed');
            }, 250);
        }, 1100);

        startCountdown();
        if (typeof AOS !== 'undefined' && AOS.refresh) {
            AOS.refresh();
        }
    });

    var registrationSheet = document.getElementById('registrationSheet');
    var registrationAudio = document.getElementById('registrationThemeAudio');
    var registrationBackdrop = document.getElementById('registrationSheetBackdrop');
    var registrationClose = document.getElementById('registrationSheetClose');
    var registrationForm = document.getElementById('registrationForm');
    var blockSingle = document.getElementById('registrationBlockSingle');
    var blockDouble = document.getElementById('registrationBlockDouble');
    var registrationAudioViz = document.getElementById('registrationAudioViz');
    var registrationAudioCtx = null;
    var registrationAnalyser = null;
    var registrationMediaSource = null;
    var registrationFreqData = null;
    var registrationVizRaf = null;
    var registrationAudioGraphReady = false;

    var performanceVideoModal = document.getElementById('performanceVideoModal');
    var performanceVideoEmbed = document.getElementById('performanceVideoEmbed');
    var performanceVideoOpen = document.getElementById('performanceVideoOpen');
    var performanceVideoClose = document.getElementById('performanceVideoClose');

    function closePerformanceVideoModal() {
        if (!performanceVideoModal) return;
        performanceVideoModal.classList.remove('is-open');
        performanceVideoModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('video-modal--open');
        if (performanceVideoEmbed) {
            performanceVideoEmbed.innerHTML = '';
        }
        if (performanceVideoOpen) {
            performanceVideoOpen.focus();
        }
    }

    function openPerformanceVideoModal() {
        if (!performanceVideoModal || !performanceVideoEmbed || !performanceVideoOpen) return;
        var rawId = performanceVideoOpen.getAttribute('data-youtube-id') || '';
        var id = rawId.replace(/[^a-zA-Z0-9_-]/g, '');
        if (id.length < 6) return;
        /* Empty embed first so the overlay can paint before the heavy YouTube iframe loads. */
        performanceVideoEmbed.innerHTML = '';
        performanceVideoModal.classList.add('is-open');
        performanceVideoModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('video-modal--open');
        if (performanceVideoClose) {
            performanceVideoClose.focus();
        }
        var src =
            'https://www.youtube-nocookie.com/embed/' + id +
            '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
        function injectIframe() {
            if (!performanceVideoModal || !performanceVideoModal.classList.contains('is-open')) {
                return;
            }
            performanceVideoEmbed.innerHTML =
                '<iframe class="video-modal__iframe" title="Performance — Mehdi Ayachi" ' +
                'src="' + src + '" ' +
                'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ' +
                'allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>';
        }
        /* Double rAF: wait for layout + paint of the modal before loading the iframe. */
        requestAnimationFrame(function () {
            requestAnimationFrame(injectIframe);
        });
    }

    function resetRegistrationVizBars() {
        if (!registrationAudioViz) return;
        registrationAudioViz.classList.remove('audio-viz--active', 'audio-viz--fallback');
        registrationAudioViz.querySelectorAll('.audio-viz__bar').forEach(function (bar) {
            bar.style.transform = 'scaleY(0.18)';
        });
    }

    function stopRegistrationViz() {
        if (registrationVizRaf) {
            cancelAnimationFrame(registrationVizRaf);
            registrationVizRaf = null;
        }
        resetRegistrationVizBars();
    }

    function ensureRegistrationAudioGraph() {
        if (registrationAudioGraphReady || !registrationAudio) {
            return registrationAudioGraphReady;
        }
        var AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) {
            return false;
        }
        try {
            registrationAudioCtx = new AC();
            registrationMediaSource = registrationAudioCtx.createMediaElementSource(registrationAudio);
            registrationAnalyser = registrationAudioCtx.createAnalyser();
            registrationAnalyser.fftSize = 128;
            registrationAnalyser.smoothingTimeConstant = 0.75;
            registrationMediaSource.connect(registrationAnalyser);
            registrationAnalyser.connect(registrationAudioCtx.destination);
            registrationFreqData = new Uint8Array(registrationAnalyser.frequencyBinCount);
            registrationAudioGraphReady = true;
            return true;
        } catch (err) {
            return false;
        }
    }

    function startRegistrationViz() {
        if (!registrationAudioViz) return;
        var bars = registrationAudioViz.querySelectorAll('.audio-viz__bar');
        if (!bars.length) return;

        if (registrationVizRaf) {
            cancelAnimationFrame(registrationVizRaf);
            registrationVizRaf = null;
        }

        if (!ensureRegistrationAudioGraph() || !registrationAnalyser || !registrationFreqData) {
            registrationAudioViz.classList.add('audio-viz--active', 'audio-viz--fallback');
            return;
        }

        registrationAudioViz.classList.add('audio-viz--active');
        registrationAudioViz.classList.remove('audio-viz--fallback');

        if (registrationAudioCtx && registrationAudioCtx.state === 'suspended') {
            registrationAudioCtx.resume().catch(function () {});
        }

        var nBars = bars.length;
        var bufLen = registrationFreqData.length;

        function tick() {
            if (!registrationSheet || !registrationSheet.classList.contains('is-open')) {
                registrationVizRaf = null;
                return;
            }
            if (!registrationAudio || registrationAudio.paused || registrationAudio.ended) {
                registrationVizRaf = null;
                resetRegistrationVizBars();
                return;
            }

            registrationAnalyser.getByteFrequencyData(registrationFreqData);
            var slice = Math.max(1, Math.floor(bufLen / nBars));
            for (var i = 0; i < nBars; i++) {
                var sum = 0;
                var start = i * slice;
                var end = Math.min(start + slice, bufLen);
                for (var j = start; j < end; j++) {
                    sum += registrationFreqData[j];
                }
                var avg = sum / (end - start) / 255;
                var h = 0.18 + avg * 0.92;
                bars[i].style.transform = 'scaleY(' + h + ')';
            }
            registrationVizRaf = requestAnimationFrame(tick);
        }

        registrationVizRaf = requestAnimationFrame(tick);
    }

    if (registrationAudio) {
        registrationAudio.addEventListener('ended', function () {
            stopRegistrationViz();
        });
    }

    function openRegistrationSheet() {
        if (!registrationSheet) return;
        closePerformanceVideoModal();
        registrationSheet.classList.add('is-open');
        registrationSheet.setAttribute('aria-hidden', 'false');
        document.body.classList.add('registration-sheet--open');
        if (registrationClose) {
            registrationClose.focus();
        }
        if (registrationAudio) {
            registrationAudio.load();
            registrationAudio.currentTime = 0;
            var p = registrationAudio.play();
            var onPlaying = function () {
                startRegistrationViz();
            };
            if (p && typeof p.then === 'function') {
                p.then(onPlaying).catch(function () {
                    stopRegistrationViz();
                });
            } else {
                onPlaying();
            }
        }
    }

    function closeRegistrationSheet() {
        if (!registrationSheet) return;
        registrationSheet.classList.remove('is-open');
        registrationSheet.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('registration-sheet--open');
        stopRegistrationViz();
        if (registrationAudio) {
            registrationAudio.pause();
        }
    }

    function syncRoomFields() {
        if (!registrationForm || !blockSingle || !blockDouble) return;
        var doubleSelected = registrationForm.querySelector('input[name="room_type"]:checked');
        var isDouble = doubleSelected && doubleSelected.value === 'double';
        blockSingle.classList.toggle('registration-form__block--hidden', isDouble);
        blockDouble.classList.toggle('registration-form__block--hidden', !isDouble);
        var singleReq = ['first_name', 'last_name', 'email', 'phone'];
        blockSingle.querySelectorAll('input, textarea').forEach(function (el) {
            if (!el.name) return;
            el.required = !isDouble && singleReq.indexOf(el.name) !== -1;
        });
        blockDouble.querySelectorAll('input, textarea').forEach(function (el) {
            var n = el.name || '';
            el.required = isDouble && (
                n === 'p1_first_name' || n === 'p1_last_name' || n === 'p1_email' ||
                n === 'p2_first_name' || n === 'p2_last_name' || n === 'p2_email'
            );
        });
    }

    if (registrationForm) {
        registrationForm.querySelectorAll('input[name="room_type"]').forEach(function (radio) {
            radio.addEventListener('change', syncRoomFields);
        });
        syncRoomFields();
        registrationForm.addEventListener('submit', function (e) {
            syncRoomFields();
            if (!registrationForm.checkValidity()) {
                e.preventDefault();
                registrationForm.reportValidity();
                return;
            }
            /* Native POST to action (home): server saves via RegisteredDoctorRegistrationService + redirect + flash */
        });
    }

    if (registrationBackdrop) {
        registrationBackdrop.addEventListener('click', closeRegistrationSheet);
    }
    if (registrationClose) {
        registrationClose.addEventListener('click', closeRegistrationSheet);
    }
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (registrationSheet && registrationSheet.classList.contains('is-open')) {
            closeRegistrationSheet();
            return;
        }
        if (performanceVideoModal && performanceVideoModal.classList.contains('is-open')) {
            closePerformanceVideoModal();
        }
    });

    if (performanceVideoOpen) {
        performanceVideoOpen.addEventListener('click', function (e) {
            e.preventDefault();
            openPerformanceVideoModal();
        });
    }
    if (performanceVideoClose) {
        performanceVideoClose.addEventListener('click', closePerformanceVideoModal);
    }
    if (performanceVideoModal) {
        performanceVideoModal.addEventListener('click', function (e) {
            var t = e.target;
            if (t && t.getAttribute && t.getAttribute('data-video-modal-close') !== null) {
                closePerformanceVideoModal();
            }
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            if (href === '#register') {
                e.preventDefault();
                openRegistrationSheet();
                return;
            }
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    function startCountdown() {
        var eventDate = new Date('2026-04-25T09:00:00').getTime();
        var prev = {};

        function setDigit(id, value) {
            var el = document.getElementById(id);
            if (!el) return;
            var str = value.toString().padStart(2, '0');
            if (prev[id] !== str) {
                el.textContent = str;
                el.classList.add('tick');
                setTimeout(function () { el.classList.remove('tick'); }, 200);
                prev[id] = str;
            }
        }

        function update() {
            var diff = eventDate - Date.now();

            if (diff <= 0) {
                var el = document.getElementById('countdown');
                if (el) {
                    el.querySelector('.countdown__track').style.display = 'none';
                    el.querySelector('.countdown__label').textContent = "L'événement a commencé !";
                    el.querySelector('.countdown__label').style.color = 'var(--blue-soft)';
                }
                return;
            }

            setDigit('days',    Math.floor(diff / 86400000));
            setDigit('hours',   Math.floor((diff / 3600000) % 24));
            setDigit('minutes', Math.floor((diff / 60000)   % 60));
            setDigit('seconds', Math.floor((diff / 1000)    % 60));
        }

        update();
        setInterval(update, 1000);
    }
})();

/**
 * Vanta (programme background only): Three.js + vanta.cells — not on event card.
 */
(function () {
    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src;
            s.async = false;
            s.onload = function () {
                resolve();
            };
            s.onerror = function () {
                reject(new Error(src));
            };
            document.body.appendChild(s);
        });
    }

    function initProgrammeVanta() {
        if (typeof VANTA === 'undefined' || typeof THREE === 'undefined') {
            return;
        }
        var programmeEl = document.getElementById('programmeVanta');
        if (!programmeEl) {
            return;
        }
        VANTA.CELLS({
            el: programmeEl,
            THREE: THREE,
            color1: 0x04101f,
            color2: 0x153a47,
            size: 2.0,
            speed: 1.5,
            scale: 1.0,
            minHeight: 200,
            minWidth: 200,
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
        });
    }

    function loadVantaProgramme() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }
        var threeUrl = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js';
        var cellsUrl = 'https://cdn.jsdelivr.net/npm/vanta@0.5.24/dist/vanta.cells.min.js';
        loadScript(threeUrl)
            .then(function () {
                return loadScript(cellsUrl);
            })
            .then(initProgrammeVanta)
            .catch(function () {});
    }

    function scheduleVanta() {
        function run() {
            if ('requestIdleCallback' in window) {
                window.requestIdleCallback(
                    function () {
                        loadVantaProgramme();
                    },
                    { timeout: 2400 }
                );
            } else {
                window.setTimeout(loadVantaProgramme, 0);
            }
        }
        if (document.readyState === 'complete') {
            run();
        } else {
            window.addEventListener('load', run);
        }
    }

    scheduleVanta();
})();

(function () {
    if (typeof AOS === 'undefined') {
        return;
    }
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    AOS.init({
        duration: 700,
        easing: 'ease-out-cubic',
        once: true,
        mirror: false,
        offset: 72,
        anchorPlacement: 'top-bottom',
        disable: reducedMotion
    });
})();