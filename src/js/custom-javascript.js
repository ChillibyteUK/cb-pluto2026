// Add your custom JS here.

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

  const animateValue = (element) => {
    const target = Number(element.dataset.statTarget || 0);

    if (!Number.isFinite(target)) {
      return;
    }

    if (prefersReducedMotion || target === 0) {
      element.textContent = String(target);
      return;
    }

    const duration = 1200;
    const startTime = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = String(Math.round(target * eased));

      if (progress < 1) {
        window.requestAnimationFrame(tick);
      }
    };

    window.requestAnimationFrame(tick);
  };

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        entry.target
          .querySelectorAll(
            ".stat-hero__stat-value, .cb-stats__stat-value, .cb-ticker-x3__stat-value",
          )
          .forEach(animateValue);

        obs.unobserve(entry.target);
      });
    },
    {
      threshold: 0.35,
    },
  );

  statHeroes.forEach((hero) => observer.observe(hero));
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
