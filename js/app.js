const select = document.getElementById('gameSelect');
const btn = document.getElementById('launchBtn');

btn.onclick = () => {
  const rom = select.value;
  const system = select.selectedOptions[0].dataset.system;

  // Variables globales utilisees par EmulatorJS
  window.EJS_player = '#game';
  window.EJS_core = system;
  window.EJS_gameUrl = rom;
  window.EJS_biosUrl = ''; //inutile jsp a quoi ca sert
  window.EJS_startOnLoaded = true;

  // Charge le moteur EmulatorJS
  const script = document.createElement('script');
  script.src = 'data/loader.js';
  document.body.appendChild(script);
};
