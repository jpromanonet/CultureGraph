/**
 * CultureGraph — interactions + force graph
 */
(function () {
  'use strict';

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function $$(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function initSidebar() {
    var shell = $('.app-shell');
    var toggle = $('#sidebar-toggle');
    var backdrop = $('.sidebar-backdrop');
    if (!shell || !toggle) return;

    function open() {
      shell.classList.add('sidebar-open');
      toggle.setAttribute('aria-expanded', 'true');
    }
    function close() {
      shell.classList.remove('sidebar-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
    toggle.addEventListener('click', function () {
      shell.classList.contains('sidebar-open') ? close() : open();
    });
    if (backdrop) backdrop.addEventListener('click', close);
  }

  function resolveTheme(pref) {
    if (pref === 'dark' || pref === 'light') return pref;
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
    return 'light';
  }

  function applyTheme(pref) {
    var root = document.documentElement;
    var theme = resolveTheme(pref);
    root.setAttribute('data-theme', theme);
    root.setAttribute('data-theme-pref', pref);
    try {
      localStorage.setItem('cg-theme', pref);
    } catch (e) {}
    return theme;
  }

  function initThemeToggle() {
    var root = document.documentElement;
    var pref = root.getAttribute('data-theme-pref') || 'system';
    try {
      var stored = localStorage.getItem('cg-theme');
      if (stored) pref = stored;
    } catch (e) {}
    applyTheme(pref);

    if (window.matchMedia) {
      var mq = window.matchMedia('(prefers-color-scheme: dark)');
      var onChange = function () {
        var currentPref = root.getAttribute('data-theme-pref') || 'system';
        if (currentPref === 'system') applyTheme('system');
      };
      if (mq.addEventListener) mq.addEventListener('change', onChange);
      else if (mq.addListener) mq.addListener(onChange);
    }

    $$('[data-theme-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var current = root.getAttribute('data-theme') || 'light';
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);

        var url = btn.getAttribute('data-theme-url');
        var csrf = btn.getAttribute('data-csrf') || '';
        if (!url) return;
        var body = new FormData();
        body.append('_csrf', csrf);
        body.append('theme', next);
        fetch(url, { method: 'POST', body: body, credentials: 'same-origin' }).catch(function () {});
      });
    });

    var settingsTheme = $('#settings-theme');
    if (settingsTheme) {
      settingsTheme.addEventListener('change', function () {
        applyTheme(settingsTheme.value);
      });
    }
  }

  function initTypeFields() {
    var typeSelect = $('#work-type');
    if (!typeSelect) return;
    function sync() {
      var type = typeSelect.value;
      $$('[data-type-field]').forEach(function (el) {
        var types = (el.getAttribute('data-type-field') || '').split(',');
        el.style.display = types.indexOf(type) >= 0 ? '' : 'none';
      });
    }
    typeSelect.addEventListener('change', sync);
    sync();
  }

  var COLORS_LIGHT = {
    work: '#152533',
    creator: '#C9922A',
    genre: '#1F6F78',
    era: '#4F6F5C',
    theme: '#6B7C8A',
    experience: '#B85A45'
  };

  var COLORS_DARK = {
    work: '#E6EDF2',
    creator: '#E0B04A',
    genre: '#3D9AA3',
    era: '#6F9A7E',
    theme: '#9AABBC',
    experience: '#D47A66'
  };

  function graphColors() {
    return document.documentElement.getAttribute('data-theme') === 'dark' ? COLORS_DARK : COLORS_LIGHT;
  }

  function parseData(el) {
    try {
      return {
        nodes: JSON.parse(el.getAttribute('data-nodes') || '[]'),
        edges: JSON.parse(el.getAttribute('data-edges') || '[]')
      };
    } catch (e) {
      return { nodes: [], edges: [] };
    }
  }

  function forceGraph(canvas, data, options) {
    if (!canvas || !data.nodes.length) return;
    options = options || {};
    var ctx = canvas.getContext('2d');
    var dpr = window.devicePixelRatio || 1;
    var cssW = canvas.clientWidth || canvas.width;
    var cssH = options.height || Math.max(280, Math.round(cssW * 0.55));
    canvas.width = Math.floor(cssW * dpr);
    canvas.height = Math.floor(cssH * dpr);
    canvas.style.height = cssH + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    var nodes = data.nodes.map(function (n, i) {
      var angle = (i / data.nodes.length) * Math.PI * 2;
      return {
        id: n.id,
        label: n.label,
        kind: n.kind,
        href: n.href,
        x: cssW / 2 + Math.cos(angle) * (cssW * 0.28),
        y: cssH / 2 + Math.sin(angle) * (cssH * 0.28),
        vx: 0,
        vy: 0
      };
    });
    var index = {};
    nodes.forEach(function (n) { index[n.id] = n; });
    var links = data.edges
      .map(function (e) {
        return { source: index[e.source], target: index[e.target], rel: e.rel };
      })
      .filter(function (l) { return l.source && l.target; });

    var drag = null;
    var interactive = !!options.interactive;

    function tick() {
      var i, j, a, b, dx, dy, dist, f;
      for (i = 0; i < nodes.length; i++) {
        for (j = i + 1; j < nodes.length; j++) {
          a = nodes[i];
          b = nodes[j];
          dx = a.x - b.x;
          dy = a.y - b.y;
          dist = Math.sqrt(dx * dx + dy * dy) || 0.01;
          f = 420 / (dist * dist);
          a.vx += (dx / dist) * f;
          a.vy += (dy / dist) * f;
          b.vx -= (dx / dist) * f;
          b.vy -= (dy / dist) * f;
        }
      }
      links.forEach(function (l) {
        dx = l.target.x - l.source.x;
        dy = l.target.y - l.source.y;
        dist = Math.sqrt(dx * dx + dy * dy) || 0.01;
        f = (dist - 90) * 0.018;
        l.source.vx += (dx / dist) * f;
        l.source.vy += (dy / dist) * f;
        l.target.vx -= (dx / dist) * f;
        l.target.vy -= (dy / dist) * f;
      });
      nodes.forEach(function (n) {
        if (drag === n) return;
        n.vx += (cssW / 2 - n.x) * 0.004;
        n.vy += (cssH / 2 - n.y) * 0.004;
        n.vx *= 0.86;
        n.vy *= 0.86;
        n.x = Math.max(18, Math.min(cssW - 18, n.x + n.vx));
        n.y = Math.max(18, Math.min(cssH - 18, n.y + n.vy));
      });
    }

    function draw() {
      ctx.clearRect(0, 0, cssW, cssH);
      ctx.strokeStyle = document.documentElement.getAttribute('data-theme') === 'dark'
        ? 'rgba(198,208,218,0.28)'
        : 'rgba(21,37,51,0.22)';
      ctx.lineWidth = 1;
      links.forEach(function (l) {
        ctx.beginPath();
        ctx.moveTo(l.source.x, l.source.y);
        ctx.lineTo(l.target.x, l.target.y);
        ctx.stroke();
      });
      nodes.forEach(function (n) {
        var r = n.kind === 'work' ? 8 : 6;
        ctx.beginPath();
        ctx.fillStyle = graphColors()[n.kind] || graphColors().work;
        ctx.strokeStyle = document.documentElement.getAttribute('data-theme') === 'dark' ? '#C5D0DA' : '#152533';
        ctx.lineWidth = 1.5;
        ctx.arc(n.x, n.y, r, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();
        if (options.labels !== false && (n.kind === 'work' || nodes.length < 50)) {
          ctx.fillStyle = document.documentElement.getAttribute('data-theme') === 'dark' ? '#E6EDF2' : '#152533';
          ctx.font = '11px "IBM Plex Mono", monospace';
          ctx.fillText(n.label.slice(0, 22), n.x + 10, n.y + 3);
        }
      });
    }

    var frames = 0;
    function loop() {
      tick();
      draw();
      frames++;
      if (frames < 220 || drag) {
        requestAnimationFrame(loop);
      }
    }
    loop();

    if (!interactive) return;

    function pick(x, y) {
      for (var i = nodes.length - 1; i >= 0; i--) {
        var n = nodes[i];
        var dx = n.x - x;
        var dy = n.y - y;
        if (dx * dx + dy * dy < 140) return n;
      }
      return null;
    }

    canvas.addEventListener('mousedown', function (ev) {
      var rect = canvas.getBoundingClientRect();
      drag = pick(ev.clientX - rect.left, ev.clientY - rect.top);
      frames = 0;
      loop();
    });
    window.addEventListener('mousemove', function (ev) {
      if (!drag) return;
      var rect = canvas.getBoundingClientRect();
      drag.x = ev.clientX - rect.left;
      drag.y = ev.clientY - rect.top;
      drag.vx = 0;
      drag.vy = 0;
    });
    window.addEventListener('mouseup', function () {
      drag = null;
    });
    canvas.addEventListener('click', function (ev) {
      var rect = canvas.getBoundingClientRect();
      var n = pick(ev.clientX - rect.left, ev.clientY - rect.top);
      if (n && n.href) window.location.href = n.href;
    });
  }

  function initGraphs() {
    var mini = $('#graph-mini');
    if (mini) {
      forceGraph($('#graph-mini-canvas'), parseData(mini), { height: 280, labels: false });
    }
    var stage = $('#graph-stage');
    if (stage) {
      forceGraph($('#graph-canvas'), parseData(stage), { height: 620, interactive: true, labels: true });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initThemeToggle();
    initTypeFields();
    initGraphs();
  });
})();
