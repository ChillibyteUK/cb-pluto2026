// Add your custom JS here.

// Prevent the browser from restoring a mid-page scroll position on reload —
// that happens before GSAP/ScrollTrigger has initialized, so pinned sections
// (e.g. cb-title-scroll-bullets) can start up already mid-pin with a broken
// spacer. Reloading now always starts at the top instead.
if ("scrollRestoration" in history) {
  history.scrollRestoration = "manual";
  window.scrollTo(0, 0);
}

// ScrollTrigger computes pin start/end in pixels on first load. If images or
// webfonts finish loading afterward and shift page height, those pixel
// values go stale and pinned sections drift out of sync with the rest of the
// page. Refresh once everything has actually finished loading.
window.addEventListener("load", function () {
  if (window.ScrollTrigger) {
    ScrollTrigger.refresh();
  }
});

// Translate `aos-<animation>` marker classes on native/Gutenberg blocks into
// the data-aos attributes AOS reads. Lets editors add scroll animations to any
// core block via the block's "Additional CSS class(es)" field. Must run BEFORE
// AOS.init() so the attributes exist when AOS scans the DOM.
(function () {
  // Valid AOS animation names — so we ignore AOS's own runtime classes
  // (aos-init / aos-animate) and the aos-delay-* helper handled below.
  var ANIMATIONS = new Set([
    'fade', 'fade-up', 'fade-down', 'fade-left', 'fade-right',
    'fade-up-right', 'fade-up-left', 'fade-down-right', 'fade-down-left',
    'flip-up', 'flip-down', 'flip-left', 'flip-right',
    'slide-up', 'slide-down', 'slide-left', 'slide-right',
    'zoom-in', 'zoom-in-up', 'zoom-in-down', 'zoom-in-left', 'zoom-in-right',
    'zoom-out', 'zoom-out-up', 'zoom-out-down', 'zoom-out-left', 'zoom-out-right',
  ]);

  document.querySelectorAll('[class*="aos-"]').forEach(function (el) {
    if (el.hasAttribute('data-aos')) return; // respect PHP-set blocks

    el.classList.forEach(function (cls) {
      if (cls.indexOf('aos-') !== 0) return;
      var name = cls.slice(4); // strip "aos-"
      if (ANIMATIONS.has(name)) {
        el.setAttribute('data-aos', name);
      } else if (/^delay-\d+$/.test(name)) {
        el.setAttribute('data-aos-delay', name.split('-')[1]); // ms
      }
    });
  });
})();

AOS.init({
  easing: 'ease-out',
  once: true,
  duration: 600,
});

// Match CB Show Hide Card title panels and collapse bodies by visual row.
(function () {
  function getRows(block) {
    const toggles = Array.from(
      block.querySelectorAll(".cb-show-hide-cards__toggle"),
    );

    if (!toggles.length) return [];

    const rows = [];
    toggles.forEach((toggle) => {
      const col = toggle.closest(".cb-show-hide-cards__col") || toggle;
      const top = Math.round(col.getBoundingClientRect().top);
      let row = rows.find((item) => Math.abs(item.top - top) <= 2);

      if (!row) {
        row = { top, toggles: [] };
        rows.push(row);
      }

      row.toggles.push(toggle);
    });

    return rows;
  }

  function syncBlock(block) {
    const rows = getRows(block);

    if (!rows.length) return;

    rows.forEach((row) => {
      row.toggles.forEach((toggle) => {
        toggle.style.minHeight = "";
      });
    });

    rows.forEach((row) => {
      const height = Math.ceil(
        Math.max(...row.toggles.map((toggle) => toggle.getBoundingClientRect().height)),
      );

      row.toggles.forEach((toggle) => {
        toggle.style.minHeight = `${height}px`;
      });
    });

    syncRowCollapseHeights(block);
  }

  function measureCollapse(collapse) {
    const card = collapse.closest(".cb-show-hide-cards__card");
    const inner = collapse.querySelector(".cb-show-hide-cards__collapse-inner");
    const originalDisplay = collapse.style.display;
    const originalHeight = collapse.style.height;
    const originalPosition = collapse.style.position;
    const originalVisibility = collapse.style.visibility;
    const originalWidth = collapse.style.width;
    const originalInnerMinHeight = inner ? inner.style.minHeight : "";

    collapse.style.display = "block";
    collapse.style.height = "auto";
    collapse.style.position = "absolute";
    collapse.style.visibility = "hidden";
    collapse.style.width = card ? `${card.getBoundingClientRect().width}px` : originalWidth;
    if (inner) inner.style.minHeight = "";

    const height = Math.ceil(collapse.scrollHeight);

    collapse.style.display = originalDisplay;
    collapse.style.height = originalHeight;
    collapse.style.position = originalPosition;
    collapse.style.visibility = originalVisibility;
    collapse.style.width = originalWidth;
    if (inner) inner.style.minHeight = originalInnerMinHeight;

    return height;
  }

  function syncRowCollapseHeights(block) {
    const rows = getRows(block);

    rows.forEach((row) => {
      const cards = row.toggles.map((toggle) => toggle.closest(".cb-show-hide-cards__card"));
      const collapses = cards
        .map((card) => card && card.querySelector(".cb-show-hide-cards__collapse"))
        .filter(Boolean);

      collapses.forEach((collapse) => {
        const inner = collapse.querySelector(".cb-show-hide-cards__collapse-inner");
        if (inner) inner.style.minHeight = "";
      });

      if (!collapses.length) return;

      const targetHeight = Math.ceil(
        Math.max(...collapses.map(measureCollapse)),
      );

      collapses.forEach((collapse) => {
        const inner = collapse.querySelector(".cb-show-hide-cards__collapse-inner");
        if (inner) inner.style.minHeight = `${targetHeight}px`;
      });
    });
  }

  function syncAll() {
    document.querySelectorAll(".cb-show-hide-cards").forEach(syncBlock);
  }

  function init() {
    syncAll();

    document.querySelectorAll(".cb-show-hide-cards__toggle-image").forEach((img) => {
      if (img.complete) return;
      img.addEventListener("load", syncAll, { once: true });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  window.addEventListener("load", syncAll);

  let resizeTimer = null;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(syncAll, 150);
  });

  document.addEventListener("show.bs.collapse", (event) => {
    if (!event.target.classList.contains("cb-show-hide-cards__collapse")) return;
    const block = event.target.closest(".cb-show-hide-cards");
    if (block) syncRowCollapseHeights(block);
  });

  document.addEventListener("shown.bs.collapse", (event) => {
    if (!event.target.classList.contains("cb-show-hide-cards__collapse")) return;
    syncAll();
  });

  document.addEventListener("hide.bs.collapse", (event) => {
    if (!event.target.classList.contains("cb-show-hide-cards__collapse")) return;
    const block = event.target.closest(".cb-show-hide-cards");
    if (block) syncRowCollapseHeights(block);
  });

  document.addEventListener("hidden.bs.collapse", syncAll);
})();


// Add background to navbar on scroll
(function () {
  var navbar = document.getElementById("wrapper-navbar");

  if (document.body.classList.contains("single-post")) {
    navbar.classList.add("scrolled");
  }

  var addNavbarBackground = function () {
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      if (!document.body.classList.contains("single-post")) {
        navbar.classList.remove("scrolled");
      }
    }
  };

  window.addEventListener("scroll", addNavbarBackground);
})();

// (function() {
//   // Hide header on scroll
//   var doc = document.documentElement;
//   var w = window;

//   var prevScroll = w.scrollY || doc.scrollTop;
//   var curScroll;
//   var direction = 0;
//   var prevDirection = 0;

//   var header = document.getElementById('wrapper-navbar');

//   var checkScroll = function() {
//       // Find the direction of scroll (0 - initial, 1 - up, 2 - down)
//       curScroll = w.scrollY || doc.scrollTop;
//       if (curScroll > prevScroll) {
//           // Scrolled down
//           direction = 2;

// Equalize image heights per multi-module row in content grid.
(function () {
  function syncRow(row) {
    const covers = row.querySelectorAll(".img-cover");
    if (!covers || covers.length < 2) return;

    row.classList.remove("content-grid-row-sync");

    let maxHeight = 0;
    covers.forEach((cover) => {
      cover.style.height = "auto";
    });

    covers.forEach((cover) => {
      const rect = cover.getBoundingClientRect();
      if (rect.height > maxHeight) maxHeight = rect.height;
    });

    if (maxHeight > 0) {
      covers.forEach((cover) => {
        cover.style.height = `${Math.ceil(maxHeight)}px`;
      });
      row.classList.add("content-grid-row-sync");
    } else {
      covers.forEach((cover) => {
        cover.style.height = "";
      });
      row.classList.remove("content-grid-row-sync");
    }
  }

  function syncAll() {
    const rows = document.querySelectorAll(".content-grid .row");
    rows.forEach(syncRow);
  }

  function init() {
    syncAll();
    const imgs = document.querySelectorAll(".content-grid .img-cover img");
    imgs.forEach((img) => {
      if (img.complete) return;
      img.addEventListener("load", syncAll, { once: true });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  let resizeTimer = null;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(syncAll, 150);
  });
})();
//       } else if (curScroll < prevScroll) {
//           // Scrolled up
//           direction = 1;
//       }

//       if (direction !== prevDirection) {
//           toggleHeader(direction, curScroll);
//       }

//       prevScroll = curScroll;
//   };

//   var toggleHeader = function(direction, curScroll) {
//       if (direction === 2 && curScroll > 125) {
//           // Replace 52 with the height of your header in px
//           if (!document.getElementById('navbar').classList.contains('show')) {
//               header.classList.add('hide');
//               prevDirection = direction;
//           }
//       } else if (direction === 1) {
//           header.classList.remove('hide');
//           prevDirection = direction;
//       }
//   };

//   window.addEventListener('scroll', checkScroll);
// }
// )();

// Count up stat hero values when they enter view.
(function () {
  const statHeroes = document.querySelectorAll(
    ".stat-hero, .cb-stats, .cb-ticker-x3",
  );

  if (!statHeroes.length) return;

  const prefersReducedMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const toRoman = (num) => {
    const lookup = [
      ["M", 1000], ["CM", 900], ["D", 500], ["CD", 400],
      ["C", 100], ["XC", 90], ["L", 50], ["XL", 40],
      ["X", 10], ["IX", 9], ["V", 5], ["IV", 4], ["I", 1],
    ];
    let roman = "";
    for (const [letter, value] of lookup) {
      while (num >= value) {
        roman += letter;
        num -= value;
      }
    }
    return roman;
  };

  const formatValue = (element, value) => {
    if (element.classList.contains("cb-ticker-x3__stat-value--roman")) {
      return toRoman(Math.max(1, value));
    }
    const fmt =
      typeof Intl !== "undefined" && Intl.NumberFormat
        ? new Intl.NumberFormat()
        : { format: (n) => String(n) };
    return fmt.format(value);
  };

  const animateValue = (element) => {
    const target = Number(element.dataset.statTarget || 0);

    if (!Number.isFinite(target)) {
      return;
    }

    if (prefersReducedMotion || target === 0) {
      element.textContent = formatValue(element, target);
      return;
    }

    const duration = 3000;
    const startTime = performance.now();

    const tick = (now) => {
      // Clamp elapsed to >= 0: the rAF timestamp can be earlier than the
      // performance.now() captured just before requestAnimationFrame(),
      // which would otherwise produce a brief negative value on frame 1.
      const elapsed = Math.max(0, now - startTime);
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      let current = Math.round(target * eased);
      // Roman numerals have no zero — start at I
      if (element.classList.contains("cb-ticker-x3__stat-value--roman")) {
        current = Math.max(1, current);
      }
      element.textContent = formatValue(element, current);

      if (progress < 1) {
        window.requestAnimationFrame(tick);
      }
    };

    window.requestAnimationFrame(tick);
  };

  // AOS duration used for the fade-in (matches AOS.init duration above). When a
  // value sits inside a staggered [data-aos-delay] card, its count-up is held
  // back until that card has finished fading in, so the count chains off the
  // fade rather than firing immediately.
  const AOS_DURATION = 600;

  const startValue = (element) => {
    const delayHost = element.closest("[data-aos-delay]");
    const aosDelay = delayHost
      ? parseInt(delayHost.getAttribute("data-aos-delay"), 10) || 0
      : 0;

    if (prefersReducedMotion || aosDelay === 0) {
      animateValue(element);
      return;
    }

    window.setTimeout(() => animateValue(element), aosDelay + AOS_DURATION);
  };

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        entry.target
          .querySelectorAll(
            ".stat-hero__stat-value, .cb-stats__stat-value, .cb-ticker-x3__stat-value",
          )
          .forEach(startValue);

        obs.unobserve(entry.target);
      });
    },
    {
      threshold: 0.35,
    },
  );

  statHeroes.forEach((hero) => observer.observe(hero));
})();

// Staggered reveal for cb-feature-title blocks.
//
// Consecutive .cb-feature-title siblings form a GROUP. Each group reveals as a
// unit when its first block scrolls into view, fading its members in one after
// another (stagger). The stagger counter resets per group, so a run broken by
// other content starts the next group's stagger from zero.
(function () {
  if (!window.gsap || !window.ScrollTrigger) return;

  var blocks = Array.prototype.slice.call(
    document.querySelectorAll(".cb-feature-title"),
  );
  if (!blocks.length) return;

  gsap.registerPlugin(ScrollTrigger);

  var prefersReducedMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Build groups of DOM-adjacent .cb-feature-title blocks (document order).
  var groups = [];
  blocks.forEach(function (block) {
    var prev = block.previousElementSibling;
    if (
      groups.length &&
      prev &&
      prev.classList.contains("cb-feature-title")
    ) {
      groups[groups.length - 1].push(block);
    } else {
      groups.push([block]);
    }
  });

  groups.forEach(function (group) {
    var targets = group
      .map(function (b) {
        return b.querySelector(".cb-feature-title__inner");
      })
      .filter(Boolean);
    if (!targets.length) return;

    // Reduced motion: just reveal, no animation.
    if (prefersReducedMotion) {
      gsap.set(targets, { autoAlpha: 1, y: 0 });
      return;
    }

    gsap.set(targets, { autoAlpha: 0 });

    ScrollTrigger.create({
      trigger: group[0],
      start: "top 85%",
      once: true,
      onEnter: function () {
        gsap.to(targets, {
          autoAlpha: 1,
          duration: 0.6,
          ease: "power2.out",
          stagger: 0.15, // resets per group — each group is its own tween
          overwrite: true,
        });
      },
    });
  });
})();

// Staggered reveal for cb-text-stats stat boxes.
(function () {
  if (!window.gsap || !window.ScrollTrigger) return;

  var grids = document.querySelectorAll(".cb-text-stats__grid");
  if (!grids.length) return;

  gsap.registerPlugin(ScrollTrigger);

  var prefersReducedMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  grids.forEach(function (grid) {
    var targets = Array.prototype.slice.call(
      grid.querySelectorAll(".cb-text-stats__stat-wrap"),
    );
    if (!targets.length) return;

    if (prefersReducedMotion) {
      gsap.set(targets, { autoAlpha: 1, y: 0 });
      return;
    }

    gsap.set(targets, { autoAlpha: 0, y: 30 });

    ScrollTrigger.create({
      trigger: grid,
      start: "top 85%",
      once: true,
      onEnter: function () {
        gsap.to(targets, {
          autoAlpha: 1,
          y: 0,
          duration: 0.5,
          ease: "power2.out",
          stagger: 0.15,
          overwrite: true,
        });
      },
    });
  });
})();

// Pin + scrub reveal for cb-title-scroll-bullets blocks.
//
// Desktop (>=992px): the title is pinned in place while the block scrolls;
// each bullet fades/slides in as a segment of that scroll range (scrubbed,
// not staggered on enter). Below that, no pin — bullets just fade in one at
// a time as they're scrolled to, same as the other reveal patterns above.
(function () {
  if (!window.gsap || !window.ScrollTrigger) return;

  var blocks = Array.prototype.slice.call(
    document.querySelectorAll(".cb-title-scroll-bullets"),
  );
  if (!blocks.length) return;

  gsap.registerPlugin(ScrollTrigger);

  var prefersReducedMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Measured from the live fixed header rather than --h-top-desktop, since
  // that var doesn't account for the wp-admin toolbar when logged in.
  var headerEl = document.getElementById("wrapper-navbar");
  var headerOffset = headerEl
    ? headerEl.getBoundingClientRect().bottom
    : 90;

  ScrollTrigger.matchMedia({
    "(min-width: 992px)": function () {
      blocks.forEach(function (block) {
        var items = Array.prototype.slice.call(
          block.querySelectorAll(".cb-title-scroll-bullets__item"),
        );
        if (!items.length) return;

        if (prefersReducedMotion) {
          gsap.set(items, { autoAlpha: 1, x: 0 });
          return;
        }

        gsap.set(items, { autoAlpha: 0, x: 60 });

        // Pin the whole block (title + bullets), not just the title: the
        // bullets need to stay put on screen while they're scrubbed in, not
        // scroll past underneath a pinned title.
        var tl = gsap.timeline({
          scrollTrigger: {
            trigger: block,
            start: "top top+=" + headerOffset,
            end: function () {
              return "+=" + items.length * window.innerHeight * 0.4; // this changes the speed - 5 x 0.6 = 3 scrolls, lower is faster
            },
            pin: block,
            scrub: 0.5,
            invalidateOnRefresh: true,
          },
        });

        items.forEach(function (item, i) {
          tl.to(item, { autoAlpha: 1, x: 0, duration: 1 }, i);
        });
      });
    },

    "(max-width: 991.98px)": function () {
      blocks.forEach(function (block) {
        var items = Array.prototype.slice.call(
          block.querySelectorAll(".cb-title-scroll-bullets__item"),
        );
        if (!items.length) return;

        if (prefersReducedMotion) {
          gsap.set(items, { autoAlpha: 1, x: 0 });
          return;
        }

        gsap.set(items, { autoAlpha: 0, x: 40 });

        items.forEach(function (item) {
          ScrollTrigger.create({
            trigger: item,
            start: "top 85%",
            once: true,
            onEnter: function () {
              gsap.to(item, {
                autoAlpha: 1,
                x: 0,
                duration: 0.5,
                ease: "power2.out",
                overwrite: true,
              });
            },
          });
        });
      });
    },
  });
})();

/*

  // Header background
  document.addEventListener('scroll', function() {
      var nav = document.getElementById('navbar');
    //   var primaryNav = document.getElementById('primaryNav');
    //   if (!primaryNav.classList.contains('show')) {
    //       nav.classList.toggle('scrolled', window.scrollY > nav.offsetHeight);
    //   }
      document.querySelectorAll('.dropdown-menu').forEach(function(dropdown) {
          dropdown.classList.remove('show');
      });
      document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
          toggle.classList.remove('show');
          toggle.blur();
      });
  });

*/
