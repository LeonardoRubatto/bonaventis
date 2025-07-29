document.addEventListener("DOMContentLoaded", () => {
  const cfgEl = document.getElementById("aos-config");
  let cfg;
  try {
    cfg = JSON.parse(cfgEl.textContent);
  } catch (e) {
    console.error("AOS : erreur JSON inline", e);
    return;
  }

  const excludes = cfg.excludeSelectors;

  // Fonction utilitaire pour savoir si on doit sauter un élément
  function isExcluded(el) {
    return excludes.some(sel => el.closest(sel));
  }

  // 1) Texte (h1, p, li, etc.) hors exclusion
  cfg.text.selectors.forEach(sel => {
    document.querySelectorAll(sel).forEach(el => {
      if (isExcluded(el)) return;
      el.setAttribute("data-aos", cfg.text.effect);
    });
  });

  // 2) Images hors exclusion, avec direction selon position
  const leftClasses  = cfg.positionClasses.left;
  const rightClasses = cfg.positionClasses.right;
  document
    .querySelectorAll(cfg.image.selectors.join(","))
    .forEach(img => {
      if (isExcluded(img)) return;
      let dir = cfg.image.directions.default;
      if (leftClasses.some(c => img.closest("." + c)))  dir = cfg.image.directions.left;
      if (rightClasses.some(c => img.closest("." + c))) dir = cfg.image.directions.right;
      img.setAttribute("data-aos", dir);
    });

  // 3) Initialisation AOS
  AOS.init({ once: true });
});
