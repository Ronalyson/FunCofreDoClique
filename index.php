<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
require __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cofre do Clique</title>
<style>
:root {
  --bg:#06130d; --panel:#0c1f17; --accent:#32e36d; --accent2:#0f9b48; --text:#e7fff2;
  --muted:#a4d9b8; --danger:#ff6b6b; --glow:0 15px 55px rgba(50,227,109,0.38);
}
*{box-sizing:border-box;}
body {
  margin:0; font-family:'Segoe UI', system-ui, -apple-system, sans-serif;
  background:
    radial-gradient(circle at 20% 20%, rgba(50,227,109,0.15), transparent 30%),
    radial-gradient(circle at 80% 0%, rgba(11,85,45,0.45), transparent 35%),
    linear-gradient(130deg, #041009 0%, #072015 45%, #021a0f 100%);
  color:var(--text); display:flex; justify-content:center; padding:24px;
  min-height:100vh; overflow-x:hidden; position:relative;
}
.scanline {
  position:fixed; inset:0; pointer-events:none;
  background:repeating-linear-gradient(180deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 3px, transparent 5px);
  mix-blend-mode:soft-light; opacity:0.35; animation:scan 8s linear infinite;
}
@keyframes scan {0%{background-position:0 0;}100%{background-position:0 100%;}}

.container {
  width: min(1100px, 100%);
  background:linear-gradient(150deg, rgba(12,31,23,0.9), rgba(5,18,11,0.94));
  border:1px solid rgba(255,255,255,0.05);
  border-radius:24px; padding:28px; box-shadow:0 25px 70px rgba(0,0,0,0.45), var(--glow);
  position:relative; overflow:hidden;
}
.orb {
  position:absolute; width:220px; height:220px; background:radial-gradient(circle, rgba(50,227,109,0.25), transparent 55%);
  filter:blur(12px); opacity:0.9; animation:float 12s ease-in-out infinite;
}
.orb.one { top:-80px; right:-60px; animation-delay:0s;}
.orb.two { bottom:-100px; left:-70px; width:280px; height:280px; animation-duration:14s; animation-delay:1s;}
@keyframes float {0%,100%{transform:translateY(0);}50%{transform:translateY(-18px);}}

header {display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;}
h1 {margin:0; letter-spacing:0.5px;}
.badge {background:rgba(50,227,109,0.08); padding:8px 12px; border-radius:12px; font-size:13px; border:1px solid rgba(50,227,109,0.22);} 
.grid {display:grid; gap:18px; grid-template-columns:1fr 1fr;}
.card {background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px; padding:16px; position:relative; overflow:hidden;}
.card::before{content:''; position:absolute; inset:-60% -60% auto auto; background:radial-gradient(circle, rgba(50,227,109,0.08), transparent 45%); transform:rotate(15deg);} 
.card h3 {margin-top:0;}
.big-total {font-size:44px; margin:8px 0; text-shadow:0 4px 25px rgba(50,227,109,0.28);} 
button {
  border:none; border-radius:12px; padding:14px 18px; font-size:16px;
  cursor:pointer; transition:transform .12s ease, box-shadow .2s ease, filter .2s ease;
}
button:hover {transform:translateY(-1px); filter:brightness(1.06);} 
button:active {transform:translateY(1px);} 
.btn-accent {background:linear-gradient(120deg,var(--accent),#74ffae); color:#04160b; box-shadow:0 10px 32px rgba(50,227,109,0.35);} 
.btn-secondary {background:linear-gradient(120deg,var(--accent2),#2bc76e); color:#03160a; box-shadow:0 8px 22px rgba(43,199,110,0.32);} 
.btn-danger {background:var(--danger); color:white;} 
.list {max-height:320px; overflow:auto; display:flex; flex-direction:column; gap:10px;} 
.item {padding:10px; border-radius:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.04); backdrop-filter:blur(4px);} 
.item small {color:var(--muted);} 
label {display:block; margin:8px 0 4px; color:var(--muted); font-size:14px;} 
input, textarea {
  width:100%; padding:12px; border-radius:10px; border:1px solid rgba(255,255,255,0.08);
  background:rgba(0,0,0,0.15); color:var(--text); font-size:15px;
}
textarea {resize:vertical; min-height:80px;} 
#modal {position:fixed; inset:0; background:rgba(0,0,0,0.7); display:none; align-items:center; justify-content:center; padding:18px;} 
.modal-body {background:var(--panel); padding:18px; border-radius:16px; width:min(420px, 100%); border:1px solid rgba(255,255,255,0.08); box-shadow:var(--glow);} 
.notice {margin-top:10px; color:var(--muted); font-size:14px;} 
.meme {margin-top:12px; padding:12px; border-radius:12px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);} 
.pulse {animation:pulse 2s infinite;} 
@keyframes pulse {0%{box-shadow:0 0 0 0 rgba(50,227,109,0.3);}70%{box-shadow:0 0 0 14px rgba(50,227,109,0);}100%{box-shadow:0 0 0 0 rgba(50,227,109,0);}} 
.sparkle {position:absolute; inset:0; pointer-events:none; background:radial-gradient(circle, rgba(255,255,255,0.25) 0, transparent 45%); opacity:0; animation:spark 2s ease-in-out infinite; mix-blend-mode:screen;}
.sparkle.two {animation-delay:0.8s;}
.sparkle.three {animation-delay:1.6s;}
@keyframes spark {0%{transform:translate(-30%, -30%) scale(0.7); opacity:0;}30%{opacity:0.5;}60%{transform:translate(60%, 40%) scale(1); opacity:0.2;}100%{opacity:0; transform:translate(110%, 70%) scale(1.3);}}
@media (max-width:800px){
  .grid{grid-template-columns:1fr; gap:14px;}
  header{flex-direction:column; align-items:flex-start;}
  .big-total{font-size:36px;}
}
</style>
</head>
<body>
<div class="scanline"></div>
<div class="container">
  <div class="orb one"></div>
  <div class="orb two"></div>
  <div class="sparkle one"></div>
  <div class="sparkle two"></div>
  <div class="sparkle three"></div>
  <header>
    <div>
      <h1>Cofre do Clique</h1>
      <div class="badge">Simulador / brincadeira. Nao e dinheiro real.</div>
    </div>
    <div class="badge" id="user-label"></div>
  </header>

  <div class="card">
    <div class="big-total">Total do Cofre: <span id="total">R$ 0,00</span></div>
    <div class="badge">Cliques: <span id="click-count">0</span></div>
    <div style="display:flex; gap:12px; margin-top:12px; flex-wrap:wrap;">
      <button class="btn-accent pulse" id="btn-click">CLICAR = +R$1</button>
      <button class="btn-secondary" id="btn-withdraw">SACAR</button>
    </div>
    <div class="notice">Ultimos cliques: <span id="last-clicks"></span></div>
    <div class="notice" id="meme"></div>
  </div>

  <div class="card">
    <h3>Historico de saques</h3>
    <div class="list" id="withdrawals"></div>
  </div>
</div>

<div id="modal">
  <div class="modal-body">
    <h3>Solicitar saque</h3>
    <form id="form-withdraw">
      <label>Motivo *</label>
      <textarea name="reason" required maxlength="500"></textarea>
      <label>Valor a sacar (inteiro) *</label>
      <input type="number" name="amount" min="1" required>
      <div style="display:flex; gap:10px; margin-top:12px;">
        <button type="submit" class="btn-secondary" style="flex:1;">Confirmar</button>
        <button type="button" class="btn-danger" id="close-modal">Cancelar</button>
      </div>
      <div class="notice">Simulador / brincadeira. Nao e dinheiro real.</div>
    </form>
  </div>
</div>

<script>
function getUsername() {
  const saved = localStorage.getItem('cofre_username');
  if (saved && saved.trim()) return saved.trim();
  let name = '';
  while (!name) {
    name = prompt('Qual seu nome?')?.trim();
  }
  localStorage.setItem('cofre_username', name);
  return name;
}

const username = getUsername();
const csrfToken = '<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>';
document.getElementById('user-label').textContent = `Ola, ${username}!`;

const fmt = v => (v/100).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
const totalEl = document.getElementById('total');
const clickEl = document.getElementById('click-count');
const withdrawList = document.getElementById('withdrawals');
const lastClicksEl = document.getElementById('last-clicks');
const memeEl = document.getElementById('meme');

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
let ambientStarted = false;

function playClickSound() {
  const now = audioCtx.currentTime;
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();
  osc.type = 'triangle';
  osc.frequency.setValueAtTime(420, now);
  osc.frequency.exponentialRampToValueAtTime(1200, now + 0.08);
  gain.gain.setValueAtTime(0.05, now);
  gain.gain.exponentialRampToValueAtTime(0.001, now + 0.18);
  osc.connect(gain).connect(audioCtx.destination);
  osc.start(now);
  osc.stop(now + 0.2);
}

function startAmbient() {
  if (ambientStarted) return;
  ambientStarted = true;
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();
  osc.type = 'sine';
  osc.frequency.value = 90;
  gain.gain.value = 0.008;
  osc.connect(gain).connect(audioCtx.destination);
  osc.start();
  const lfo = audioCtx.createOscillator();
  const lfoGain = audioCtx.createGain();
  lfo.frequency.value = 0.07;
  lfoGain.gain.value = 12;
  lfo.connect(lfoGain).connect(osc.frequency);
  lfo.start();
}

async function fetchState() {
  const res = await fetch('api.php?action=state', {
    headers:{'X-CSRF': csrfToken}
  });
  const data = await res.json();
  render(data);
}

function render(data) {
  totalEl.textContent = fmt(data.total_cents);
  clickEl.textContent = data.total_clicks ?? 0;
  withdrawList.innerHTML = data.withdrawals.map(w => `
    <div class="item">
      <strong>${escapeHtml(w.name)}</strong> sacou ${fmt(w.amount_cents)}
      <div>${escapeHtml(w.reason)}</div>
      <small>${new Date(w.created_at.replace(' ', 'T')).toLocaleString('pt-BR')}</small>
    </div>
  `).join('') || '<div class="notice">Sem saques ainda.</div>';

  lastClicksEl.textContent = data.last_clicks.map(c =>
    `${escapeHtml(c.name)} (${new Date(c.created_at.replace(' ', 'T')).toLocaleTimeString('pt-BR')})`
  ).join(' | ') || 'Aguardando cliques';

  if (data.meme) {
    memeEl.innerHTML = `<div class="meme">${escapeHtml(data.meme)}</div>`;
  }
}

function escapeHtml(str='') {
  return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

document.getElementById('btn-click').onclick = async () => {
  playClickSound();
  startAmbient();
  const res = await fetch('api.php?action=click', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF': csrfToken},
    body: JSON.stringify({name: username})
  });
  const data = await res.json();
  if (data.error) {
    alert(data.error);
    return;
  }
  render(data);
};

const modal = document.getElementById('modal');
document.getElementById('btn-withdraw').onclick = () => { modal.style.display = 'flex'; };
document.getElementById('close-modal').onclick = () => { modal.style.display = 'none'; };

document.getElementById('form-withdraw').onsubmit = async (e) => {
  e.preventDefault();
  playClickSound();
  startAmbient();
  const form = e.target;
  const payload = {
    name: username,
    reason: form.reason.value.trim(),
    amount: parseInt(form.amount.value, 10)
  };
  const res = await fetch('api.php?action=withdraw', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF': csrfToken},
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (data.error) {
    alert(data.error);
    return;
  }
  render(data);
  modal.style.display = 'none';
  form.reset();
  downloadVoucher({
    name: username,
    amount: payload.amount,
    reason: payload.reason,
    timestamp: new Date()
  });
};

setInterval(fetchState, 4000);
fetchState();

function downloadVoucher(info) {
  const canvas = document.createElement('canvas');
  canvas.width = 900;
  canvas.height = 500;
  const ctx = canvas.getContext('2d');

  const grd = ctx.createLinearGradient(0, 0, 900, 500);
  grd.addColorStop(0, '#0b2015');
  grd.addColorStop(1, '#0c3a22');
  ctx.fillStyle = grd;
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  ctx.strokeStyle = '#32e36d';
  ctx.lineWidth = 8;
  ctx.strokeRect(18, 18, canvas.width - 36, canvas.height - 36);

  ctx.fillStyle = '#e7fff2';
  ctx.font = 'bold 46px Segoe UI';
  ctx.fillText('Voucher de Resgate', 40, 90);

  ctx.font = '28px Segoe UI';
  ctx.fillText(`Nome: ${info.name}`, 40, 160);
  ctx.fillText(`Valor: R$ ${info.amount.toFixed(0)},00`, 40, 210);
  ctx.fillText('Motivo:', 40, 260);

  ctx.font = '24px Segoe UI';
  const lines = wrapText(ctx, info.reason, 40, 300, 820, 32);

  ctx.font = '22px Segoe UI';
  const ts = info.timestamp.toLocaleString('pt-BR');
  ctx.fillText(`Data/Hora: ${ts}`, 40, 300 + lines * 32 + 30);

  ctx.font = '20px Segoe UI';
  ctx.fillStyle = '#a4d9b8';
  ctx.fillText('Simulador / brincadeira - Nao e dinheiro real.', 40, canvas.height - 40);

  const link = document.createElement('a');
  link.download = `voucher-${info.name}-${Date.now()}.png`;
  link.href = canvas.toDataURL('image/png');
  link.click();
}

function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
  const words = (text || '').split(' ');
  let line = '';
  let lineCount = 0;
  for (let n = 0; n < words.length; n++) {
    const testLine = line + words[n] + ' ';
    const metrics = ctx.measureText(testLine);
    if (metrics.width > maxWidth && n > 0) {
      ctx.fillText(line, x, y);
      line = words[n] + ' ';
      y += lineHeight;
      lineCount++;
    } else {
      line = testLine;
    }
  }
  ctx.fillText(line, x, y);
  return lineCount + 1;
}
</script>
</body>
</html>
