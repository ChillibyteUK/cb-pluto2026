/**
 * CB Team filter
 *
 * Client-side filter for the merged cb-team block. Matches cards by
 * data-name (substring) and data-team (slug list). Hides empty groups
 * and the whole section if everything is filtered out. Syncs ?team= and
 * ?q= URL params.
 *
 * @package cb-pluto2026
 */
(() => {
  const section = document.querySelector(".cb-team[data-cb-team-block]");
  if (!section) return;

  const teamSel = section.querySelector("#cb-team-team");
  const qInput = section.querySelector("#cb-team-q");
  const resetBtn = section.querySelector("#cb-team-filter-reset");
  const status = section.querySelector("#cb-team-filter-status");

  // No filter UI present (single team) → nothing to do.
  if (!teamSel && !qInput) return;

  const debounce = (fn, ms) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  const cards = Array.from(section.querySelectorAll(".cb-team__card")).map(
    (el) => ({
      el,
      col: el.closest(".cb-team__col") || el,
      teams: (el.getAttribute("data-team") || "").split(/\s+/).filter(Boolean),
      name: (el.getAttribute("data-name") || "").toLowerCase(),
      group: el.closest(".cb-team__group"),
    }),
  );

  const groups = Array.from(section.querySelectorAll(".cb-team__group"));

  const applyFilter = () => {
    const team = (teamSel && teamSel.value) || "";
    const q = (qInput && qInput.value.toLowerCase().trim()) || "";

    let shown = 0;
    const groupCounts = new Map();
    groups.forEach((g) => groupCounts.set(g, 0));

    cards.forEach(({ col, teams, name, group }) => {
      const matchTeam = !team || teams.includes(team);
      const matchName = !q || name.includes(q);
      const visible = matchTeam && matchName;
      col.style.display = visible ? "" : "none";
      if (visible) {
        shown++;
        if (group) groupCounts.set(group, (groupCounts.get(group) || 0) + 1);
      }
    });

    groups.forEach((g) => {
      const n = groupCounts.get(g) || 0;
      g.classList.toggle("is-empty", n === 0);
    });

    section.classList.toggle("is-empty", shown === 0);

    if (status) {
      status.textContent = `${shown} team member${shown === 1 ? "" : "s"} shown`;
    }

    // Sync URL.
    const params = new URLSearchParams(window.location.search);
    if (team) params.set("team", team);
    else params.delete("team");
    if (q) params.set("q", q);
    else params.delete("q");
    const url = `${window.location.pathname}${params.toString() ? "?" + params.toString() : ""}`;
    history.replaceState(null, "", url);
  };

  const initFromURL = () => {
    const params = new URLSearchParams(window.location.search);
    const team = params.get("team") || "";
    const q = params.get("q") || "";
    if (teamSel && team) teamSel.value = team;
    if (qInput && q) qInput.value = q;
    applyFilter();
  };

  if (teamSel) teamSel.addEventListener("change", applyFilter);
  if (qInput) qInput.addEventListener("input", debounce(applyFilter, 200));
  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      if (teamSel) teamSel.value = "";
      if (qInput) qInput.value = "";
      applyFilter();
    });
  }

  initFromURL();
})();
