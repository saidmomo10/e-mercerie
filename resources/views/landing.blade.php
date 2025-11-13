<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSRF token + WebPush public key for client-side subscription -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="webpush-public-key" content="{{ config('webpush.vapid.public') ?? config('services.webpush.public') }}">
<title>Liste des Merceries – Prodmast</title>
<style>
/* === FONT & RESET === */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap");
* { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif; }

:root {
  --main-color:#4F0341;
  --accent-color:#7a1761;
  --bg-light:#faf8fc;
  --text-light:#fff;
  --text-dark:#2a2a2a;
  --radius:20px;
  --transition:all .4s ease;
}

/* === BODY === */
body {
  background:var(--bg-light);
  color:var(--text-dark);
  overflow-x:hidden;
  scroll-behavior:smooth;
}

/* === HEADER === */
header {
  position:fixed;
  top:0; left:0;
  width:100%;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 80px;
  background:transparent;
  transition:var(--transition);
  z-index:100;
}
header.scrolled {
  background:rgba(255,255,255,.95);
  box-shadow:0 4px 15px rgba(0,0,0,.1);
  padding:10px 50px;
}
.logo {
  font-weight:700;
  font-size:1.5rem;
  color:var(--text-light);
  transition:var(--transition);
}
header.scrolled .logo { color:var(--main-color); }

.btn-signin {
  background:var(--main-color);
  color:var(--text-light);
  padding:10px 22px;
  border:none;
  border-radius:25px;
  font-weight:600;
  cursor:pointer;
  text-decoration:none;
  transition:var(--transition);
}
.btn-signin:hover { background:var(--accent-color); transform:translateY(-2px); }

/* === DROPDOWN MODERN STYLE === */
.profile-box {
  position: relative;
}

.dropdown-menu {
  display: none;
  position: absolute;
  right: 0;
  top: calc(100% + 10px);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border-radius: 12px;
  min-width: 220px;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.15);
  z-index: 200;
  padding: 10px 0;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s ease;
}

.dropdown-menu.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

.dropdown-menu .divider {
  height: 1px;
  background: linear-gradient(to right, transparent, #eee, transparent);
  margin: 8px 0;
}

.dropdown-menu a,
.dropdown-menu button {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  color: #333;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 500;
  transition: all 0.25s ease;
  background: transparent;
  border: none;
  width: 100%;
  cursor: pointer;
}

.dropdown-menu a i,
.dropdown-menu button i {
  color: var(--accent-color);
  font-size: 1rem;
}

.dropdown-menu a:hover,
.dropdown-menu button:hover {
  background: #f9f4ff;
  color: var(--accent-color);
}

/* Header adaptation on scroll (dropdown contrast) */
header.scrolled .dropdown-menu {
  background: #fff;
}

/* User info block inside dropdown */
.dropdown-menu .author-info {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #faf8fc;
  border-radius: 8px;
  margin: 0 10px 8px;
}

.dropdown-menu .author-info img {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  object-fit: cover;
}

.dropdown-menu .author-info h4 {
  font-size: 0.95rem;
  color: #222;
  margin: 0;
}

.dropdown-menu .author-info a {
  font-size: 0.8rem;
  color: #777;
  text-decoration: none;
}


/* === HERO === */
.hero {
  height:60vh;
  background:linear-gradient(rgba(79,3,65,0.6),rgba(79,3,65,0.7)),
              url('{{ asset("images/supplies.jpg") }}') center/cover no-repeat;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  text-align:center;
  color:var(--text-light);
  padding:0 20px;
}
.hero h1 {
  font-size:2.5rem;
  font-weight:700;
  margin-bottom:10px;
}
.hero p { font-size:1.1rem; max-width:650px; color:#eee; }

.hero-buttons { margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; }
.btn { border: none; cursor: pointer; font-weight: 600; border-radius: 30px; padding: 12px 28px; transition: var(--transition); }
.btn-primary { background: var(--text-light); color: var(--main-color); text-decoration: none; }
.btn-primary:hover { background: var(--accent-color); color: var(--text-light); transform: translateY(-3px); }
.btn-outline { background: transparent; color: var(--text-light); border: 2px solid var(--text-light); }
.btn-outline:hover { background: var(--text-light); color: var(--main-color); transform: translateY(-3px); }

/* === TITLE SECTION === */
.section-title {
  text-align:center;
  margin-top:100px;
  font-size:2rem;
  font-weight:700;
  color:var(--main-color);
  position:relative;
}
.section-title::after {
  content:'';
  width:80px; height:4px;
  background:var(--accent-color);
  display:block;
  margin:10px auto 0;
  border-radius:2px;
}

/* === MERCERIES === */
.merceries {
  padding:70px 80px 100px;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
  gap:35px;
  justify-items:center;
}
.merceries .card {
  background:#fff;
  border-radius:var(--radius);
  box-shadow:0 10px 25px rgba(0,0,0,0.08);
  overflow:hidden;
  transform:translateY(30px);
  opacity:0;
  transition:transform .5s ease, opacity .5s ease;
}
.card.visible { transform:translateY(0); opacity:1; }
.card:hover { transform:translateY(-8px) scale(1.02); box-shadow:0 18px 35px rgba(79,3,65,0.15); }
.card img { width:100%; height:220px; object-fit:cover; }

.card-content { padding:22px; }
.card-content h3 {
  font-size:1.25rem;
  color:var(--main-color);
  margin-bottom:8px;
}
.card-content p {
  color:#555;
  font-size:0.95rem;
  line-height:1.4;
  margin-bottom:15px;
}
.info {
  display:flex;
  justify-content:space-between;
  align-items:center;
  font-size:0.9rem;
}
.location { color:#777; }
.rating {
  background:#f6effb;
  color:var(--accent-color);
  padding:5px 12px;
  border-radius:10px;
  font-weight:600;
}
.card-content .btn {
  display:block;
  width:100%;
  text-align:center;
  background:var(--main-color);
  color:#fff;
  border:none;
  border-radius:10px;
  padding:10px 0;
  margin-top:15px;
  cursor:pointer;
  font-weight:600;
  transition:var(--transition);
}
.card-content .btn:hover { background:var(--accent-color); }

/* === FOOTER === */
footer {
  background:var(--main-color);
  color:var(--text-light);
  text-align:center;
  padding:40px 20px;
  font-size:0.9rem;
  margin-top:80px;
}

/* === RESPONSIVE === */
@media(max-width:1024px){
  header{padding:15px 40px;}
  .merceries{padding:60px 40px;}
}
@media(max-width:768px){
  header{padding:15px 25px;}
  .logo{font-size:1.3rem;}
  .btn-signin{padding:8px 16px; font-size:14px;}
  .hero{height:50vh;}
  .hero h1{font-size:2rem;}
  .section-title{font-size:1.6rem;}
  .merceries{padding:50px 25px;}
}
@media(max-width:480px){
  .hero{height:45vh;}
  .hero h1{font-size:1.7rem;}
}
</style>
</head>
<body>

<header id="header">
  <a href="{{ route('landing') }}" class="logo">E-mercerie</a>
  @if(!Auth::check())
    <a href="{{ route('login.form') }}" class="btn-signin">Se connecter</a>
  @else
    @php $user = auth()->user(); @endphp
    <div class="profile-box" style="margin-left:12px;">
      <button class="dropdown-toggle bg-transparent border-0 btn-signin" type="button" id="profile"
        data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-info" style="display:flex;align-items:center;gap:10px;">
          <div class="image">
            <img src="{{ $user->avatar_url ?? asset('images/placeholder-60.png') }}" alt="Avatar" class="rounded-circle" width="36" style="object-fit:cover;">
          </div>
          <div class="d-none d-md-block" style="text-align:left;">
            <div style="font-weight:600;font-size:0.95rem;color:var(--text-light);">{{ $user->name }}</div>
            <div style="font-size:0.8rem;color:rgba(255,255,255,0.85);">{{ $user->email }}</div>
          </div>
        </div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile" style="min-width:200px;">
        <li>
          <div class="author-info" style="display:flex;align-items:center;padding:8px;">
            <div class="image">
              <img src="{{ $user->avatar_url ?? asset('images/placeholder-60.png') }}" alt="image" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
            </div>
            <div class="content" style="margin-left:8px;">
              <h4 style="margin:0;font-size:0.95rem;">{{ $user->name }}</h4>
              <a href="#" style="font-size:0.8rem;color:#666;">{{ $user->email }}</a>
            </div>
          </div>
        </li>
        <li class="divider"></li>
        <li>
          <a href="{{ route('merceries.profile.edit') }}" class="dropdown-item">
            <i class="fa-solid fa-user"></i> Mon Profile
          </a>
        </li>
        <li>
          @if(auth()->user()->isCouturier())
          <a href="{{ route('merceries.index') }}" class="dropdown-item">
            <i class="fa-solid fa-gauge"></i> Tableau de bord
          </a>
          @elseif(auth()->user()->isMercerie())
          <a href="{{ route('orders.index') }}" class="dropdown-item">
            <i class="fa-solid fa-gauge"></i> Tableau de bord
          </a>
          @endif
        </li>
        <li class="divider"></li>
        <li style="padding:8px;">
          <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="bg-transparent border-0" style="width:100%;text-align:left;padding:6px 8px;">Déconnexion</button>
          </form>
        </li>
      </ul>
    </div>
  @endif
</header>

<section class="hero">
    <h1>Trouvez votre mercerie idéale</h1>
    <p>Découvrez les meilleures merceries de votre région et leurs produits uniques.</p>
    <div class="hero-buttons">
      @auth
        @if(auth()->user()->isMercerie())
          <a href="{{ route('merchant.supplies.index') }}" class="btn btn-primary">Ajouter une fourniture</a>
        @elseif(auth()->user()->isCouturier())
          <a href="{{ route('supplies.selection') }}" class="btn btn-primary">Comparer les prix</a>
        @endif
      @else
        <a href="#" class="btn btn-primary">Démarrer</a>
      @endauth
        <!-- <button class="btn btn-outline">Try Demo</button> -->
    </div>
</section>
 
<h2 class="section-title">Liste des Merceries</h2>

<!-- SEARCH (landing) -->
<div style="display:flex;justify-content:center;margin-top:18px;">
  <div style="width:100%;max-width:720px;padding:14px;">
    <div style="background:#fff;border-radius:999px;box-shadow:0 6px 18px rgba(0,0,0,0.06);display:flex;align-items:center;padding:10px 16px;">
      <i class="fa fa-search"></i>
      <input id="search-merceries-landing" type="text" placeholder="Rechercher une mercerie ou une ville..." style="flex:1;border:0;outline:0;padding:8px 12px;font-size:15px;color:#222;">
      <div id="merceries-loader-landing" style="width:18px;height:18px;border-radius:50%;border:3px solid #f3f3f3;border-top:3px solid #7a1761;animation:spin 1s linear infinite;display:none;margin-left:8px;"></div>
    </div>
  </div>
</div>

<section class="merceries" id="merceries-container">
  @if(isset($merceries) && $merceries->isNotEmpty())
    @foreach($merceries as $m)
      <div class="card">
        <img src="{{ $m->avatar_url ? asset($m->avatar_url) : 'https://via.placeholder.com/600x300?text=Mercerie' }}" alt="{{ $m->name }}">
        <div class="card-content">
          <h3>{{ $m->name }}</h3>
          <!-- <p>{{ $m->address ? (strlen($m->address) > 120 ? substr($m->address,0,117).'...' : $m->address) : 'Aucune description fournie.' }}</p> -->
          <div class="info">
            <div class="location">📍 {{ $m->city }}@if(isset($m->quarter) && $m->quarter) — {{ $m->quarter->name ?? $m->quarter }}@endif</div>
            <!-- <div class="rating">⭐ {{ number_format($m->rating ?? 4.5, 1) }}</div> -->
          </div>
          <a href="{{ route('merceries.show',$m->id) }}" class="btn">Voir plus</a>
        </div>
      </div>
    @endforeach
  @else
    <div style="grid-column:1/-1;text-align:center;padding:40px;font-size:1.1rem;">Aucune mercerie trouvée pour le moment.</div>
  @endif
</section>

<footer>© 2025 Prodmast — Tous droits réservés.</footer>

<script>
// HEADER SCROLL EFFECT
const header=document.getElementById('header');
window.addEventListener('scroll',()=>{header.classList.toggle('scrolled',window.scrollY>50);});

// FADE-IN ON SCROLL
const observer=new IntersectionObserver(entries=>{
  entries.forEach(entry=>{
    if(entry.isIntersecting) entry.target.classList.add('visible');
  });
},{threshold:0.2});
document.querySelectorAll('.card').forEach(c=>observer.observe(c));
</script>

<script>
// LIVE SEARCH FOR LANDING
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('search-merceries-landing');
  const container = document.getElementById('merceries-container');
  const loader = document.getElementById('merceries-loader-landing');
  const rootUrl = "{{ url('/') }}";
  let timer = null;

  function renderCards(items) {
    // clear existing content
    container.innerHTML = '';
    if (!items || items.length === 0) {
      container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;font-size:1.1rem;">Aucune mercerie trouvée pour le moment.</div>';
      return;
    }

    items.forEach(m => {
      const avatar = m.avatar_url || (rootUrl + '/images/placeholder-600x300.png');
      const html = `
        <div class="card">
          <img src="${avatar}" alt="${escapeHtml(m.name)}">
          <div class="card-content">
            <h3>${escapeHtml(m.name)}</h3>
            <p>${escapeHtml(m.description || 'Aucune description fournie.')}</p>
            <div class="info">
              <div class="location">📍 ${escapeHtml(m.city || '—')}${m.quarter ? ' — ' + escapeHtml(m.quarter) : ''}</div>
              <div class="rating">⭐ ${Number(m.rating || 4.5).toFixed(1)}</div>
            </div>
            <a href="${rootUrl}/couturier/merceries/${m.id}" class="btn">Voir plus</a>
          </div>
        </div>`;
      container.insertAdjacentHTML('beforeend', html);
    });

    // re-attach intersection observer for fade-in
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, {threshold:0.2});
    document.querySelectorAll('.card').forEach(c => observer.observe(c));
  }

  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const q = input.value.trim();
      loader.style.display = 'inline-block';
      fetch(`{{ route('api.merceries.search') }}?search=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
        .then(renderCards)
        .catch(() => {
          container.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;font-size:1.1rem;color:#b91c1c;">Erreur lors de la recherche.</div>';
        })
        .finally(() => loader.style.display = 'none');
    }, 300);
  });
});
</script>

<script>
// Dropdown fallback: if Bootstrap's dropdown isn't available, toggle manually
document.addEventListener('DOMContentLoaded', function () {
  const profileBtn = document.getElementById('profile');
  if (!profileBtn) return;
  const menu = profileBtn.closest('.profile-box').querySelector('.dropdown-menu');
  // If Bootstrap provides dropdown via data-bs-toggle, it will handle it. Only add fallback if not.
  let bootstrapAvailable = false;
  try { bootstrapAvailable = typeof bootstrap !== 'undefined' && typeof bootstrap.Dropdown !== 'undefined'; } catch (e) { bootstrapAvailable = false; }
  if (bootstrapAvailable) return;

  profileBtn.addEventListener('click', function (e) {
    e.preventDefault();
    menu.classList.toggle('show');
  });
  // close when clicking outside
  document.addEventListener('click', function (e) {
    if (!profileBtn.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove('show');
    }
  });
});
</script>

<!-- Service Worker + Web Push subscription initializer (runs on landing to ensure user receives push) -->
<script>
document.addEventListener('DOMContentLoaded', async function () {
  // Only attempt if browser supports service workers and push
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

  // Only subscribe when user is authenticated (server-side check via Blade)
  @if(Auth::check())
  try {
    // Register service worker if not already
    const sw = await navigator.serviceWorker.register('/sw.js').catch(e => { console.debug('SW register failed', e); return null; });
    if (!sw) return;

    // get existing subscription
    const reg = await navigator.serviceWorker.getRegistration();
    const existing = await reg.pushManager.getSubscription();
    if (existing) return; // already subscribed

    // Get public VAPID key from meta
    const vapidMeta = document.querySelector('meta[name="webpush-public-key"]');
    const vapidKey = vapidMeta ? vapidMeta.getAttribute('content') : null;
    if (!vapidKey) { console.warn('No VAPID public key found'); return; }

    // Convert base64 public key to Uint8Array
    function urlBase64ToUint8Array(base64String) {
      const padding = '='.repeat((4 - base64String.length % 4) % 4);
      const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
      const rawData = window.atob(base64);
      const outputArray = new Uint8Array(rawData.length);
      for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
      }
      return outputArray;
    }

    const subscription = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidKey)
    });

    // Send subscription to server to store it
  await fetch('{{ route('push.subscribe') }}', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ subscription })
    });

    console.info('Push subscription registered');
  } catch (err) {
    console.warn('Push subscription failed', err);
  }
  @endif
});
</script>
</body>
</html>
