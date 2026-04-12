<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Administration') — Salonify Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body>

{{-- ══ TOPBAR ADMIN ════════════════════════════════════════════ --}}
<div class="admin-topbar">
  <div class="admin-topbar-inner">
    <a href="{{ route('home') }}" class="admin-brand">
      <div class="admin-brand-icon">S</div>
      <div>
        <div class="admin-brand-name">Salonify</div>
        <div class="admin-brand-sub">Administration</div>
      </div>
    </a>

    <nav class="admin-topnav">
      <a href="{{ route('admin.dashboard') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        Vue générale
      </a>
      <a href="{{ route('admin.salons.index') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.salons.*') ? 'active' : '' }}">
        Salons
        @php $attente = \App\Models\Salon::where('valide',0)->count(); @endphp
        @if($attente > 0)
          <span style="background:#E8562A;color:#fff;font-size:.6rem;font-weight:700;padding:.06rem .4rem;border-radius:8px;margin-left:.3rem">{{ $attente }}</span>
        @endif
      </a>
      <a href="{{ route('admin.users.index') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        Utilisateurs
      </a>
      <a href="{{ route('admin.avis.index') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.avis.*') ? 'active' : '' }}">
        Avis
      </a>
      <a href="{{ route('admin.villes.index') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.villes.*') ? 'active' : '' }}">
        Villes
      </a>
      <a href="{{ route('admin.statistiques.index') }}"
         class="admin-topnav-tab {{ request()->routeIs('admin.statistiques.*') ? 'active' : '' }}">
        Statistiques
      </a>
    </nav>

    <div class="admin-topbar-right">
      <div class="admin-notif-btn">&#9993;<div class="admin-notif-dot"></div></div>
      <div class="admin-user-chip">
        <div class="admin-user-avatar">
          <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=89986D&color=fff&size=64"
               alt="{{ auth()->user()->nomComplet() }}">
        </div>
        <div class="admin-user-name">{{ auth()->user()->nomComplet() }}</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin-left:.5rem">
        @csrf
        <button type="submit" style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.7);font-size:.72rem;padding:.4rem .8rem;cursor:pointer;font-family:var(--fb)">
          Déconnexion
        </button>
      </form>
    </div>
  </div>
</div>

{{-- ══ LAYOUT DEUX COLONNES ════════════════════════════════════ --}}
<div class="admin-layout">

  {{-- Sidebar gauche --}}
  <aside class="admin-sidebar">
    <span class="admin-sidebar-section-label">Vue d'ensemble</span>
    <a href="{{ route('admin.dashboard') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#9962;</div>Tableau de bord
    </a>
    <a href="{{ route('admin.statistiques.index') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.statistiques.*') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#9830;</div>Statistiques
    </a>

    <div class="admin-sidebar-divider"></div>
    <span class="admin-sidebar-section-label">Gestion</span>

    <a href="{{ route('admin.salons.index') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.salons.*') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#9986;</div>Salons
      @if(isset($attente) && $attente > 0)
        <span class="admin-sidebar-badge danger">{{ $attente }}</span>
      @endif
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#9786;</div>Utilisateurs
    </a>
    <a href="{{ route('admin.avis.index') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.avis.*') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#9733;</div>Avis
      @php $avisEnAttente = \App\Models\Avis::whereNull('reponse_salon')->count(); @endphp
      @if($avisEnAttente > 0)
        <span class="admin-sidebar-badge">{{ $avisEnAttente }}</span>
      @endif
    </a>
    <a href="{{ route('admin.villes.index') }}"
       class="admin-sidebar-link {{ request()->routeIs('admin.villes.*') ? 'active' : '' }}">
      <div class="admin-sidebar-icon">&#127968;</div>Villes
    </a>

    <div class="admin-sidebar-divider"></div>
    <span class="admin-sidebar-section-label">Compte</span>
    <a href="{{ route('home') }}" class="admin-sidebar-link">
      <div class="admin-sidebar-icon">&#127968;</div>Voir le site
    </a>
  </aside>

  {{-- Contenu principal --}}
  <main class="admin-main">

    {{-- Flash messages --}}
    @foreach(['success','error','warning','info'] as $type)
      @if(session($type))
        <div class="flash flash-{{ $type }}" style="margin-bottom:1.4rem">
          {{ session($type) }}
          <button class="flash-close" onclick="this.parentElement.remove()">&#10005;</button>
        </div>
      @endif
    @endforeach

    @yield('content')
  </main>
</div>

<script>
document.querySelectorAll('.flash').forEach(f => setTimeout(() => f.remove(), 5000));
</script>
@stack('scripts')
</body>
</html>
