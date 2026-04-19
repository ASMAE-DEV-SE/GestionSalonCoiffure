<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Mon espace') — Salonify</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body>

{{-- ══ TOPBAR SIMPLE ═══════════════════════════════════════════ --}}
<header class="nav" style="position:sticky;top:0;z-index:999">
  <div class="nav-r1">
    <a href="{{ route('home') }}" class="brand">
      <div class="brand-box">
        <img src="{{ asset('images/logo1.png') }}" alt="Salonify" class="logo-img">
      </div>
      <div>
        <div class="brand-name">Salonify</div>
        <div class="brand-sub">{{ auth()->user()->isSalon() ? 'Espace Salon' : 'Espace Client' }}</div>
      </div>
    </a>
    <div class="nav-acts" style="margin-left:auto">
      @if(auth()->user()->isSalon())
        <a href="{{ route('home') }}" class="btn-log">Voir le site</a>
      @else
        <a href="{{ route('villes.index') }}" class="btn-log">Trouver un salon</a>
      @endif
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-ins" style="cursor:pointer;border:none">Déconnexion</button>
      </form>
    </div>
  </div>
  <span class="nav-line"></span>

  {{-- Sous-navigation selon le rôle --}}
  @if(auth()->user()->isSalon())
    {{-- Topbar gérant (dash-top style) --}}
    <div class="dt-inner" style="overflow-x:auto;display:flex;background:linear-gradient(135deg,#1C1A14,#2E3D1E);padding:0 2.5rem">
      <a href="{{ route('salon.dashboard') }}"
         class="dt-tab {{ request()->routeIs('salon.dashboard') ? 'on' : '' }}">Vue générale</a>
      <a href="{{ route('salon.services.index') }}"
         class="dt-tab {{ request()->routeIs('salon.services.*') ? 'on' : '' }}">Services</a>
      <a href="{{ route('salon.employes.index') }}"
         class="dt-tab {{ request()->routeIs('salon.employes.*') ? 'on' : '' }}">Équipe</a>
      <a href="{{ route('salon.disponibilites.index') }}"
         class="dt-tab {{ request()->routeIs('salon.disponibilites.*') ? 'on' : '' }}">Disponibilités</a>
      <a href="{{ route('salon.reservations.index') }}"
         class="dt-tab {{ request()->routeIs('salon.reservations.*') ? 'on' : '' }}">Réservations
        @php $rdvEnAttente = auth()->user()->salon?->reservations()->where('statut','en_attente')->where('date_heure','>=',now())->count(); @endphp
        @if($rdvEnAttente > 0)
          <span style="background:#E8562A;color:#fff;font-size:.6rem;font-weight:700;padding:.06rem .4rem;border-radius:8px;margin-left:.3rem">{{ $rdvEnAttente }}</span>
        @endif
      </a>
      <a href="{{ route('salon.avis.index') }}"
         class="dt-tab {{ request()->routeIs('salon.avis.*') ? 'on' : '' }}">Avis</a>
      <a href="{{ route('salon.profil.edit') }}"
         class="dt-tab {{ request()->routeIs('salon.profil.*') ? 'on' : '' }}">Mon salon</a>
    </div>
  @endif
</header>

{{-- ══ FLASH ════════════════════════════════════════════════════ --}}
@foreach(['success','error','warning','info'] as $type)
  @if(session($type))
    <div class="flash flash-{{ $type }}">
      {{ session($type) }}
      <button class="flash-close" onclick="this.parentElement.remove()">&#10005;</button>
    </div>
  @endif
@endforeach

{{-- ══ LAYOUT POUR CLIENTS (sidebar + main) ════════════════════ --}}
@if(auth()->user()->isClient())
<div class="client-dash-wrap">

  <aside class="client-sidebar">
    {{-- Profil --}}
    <div class="client-sidebar-profile">
      <div class="sidebar-avatar-wrap">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=9CAB84&color=fff&size=96"
             class="sidebar-avatar-img" alt="{{ auth()->user()->nomComplet() }}">
        <div class="sidebar-avatar-status"></div>
      </div>
      <div class="sidebar-client-name">{{ auth()->user()->nomComplet() }}</div>
      <div class="sidebar-client-email">{{ auth()->user()->email }}</div>
      <div class="sidebar-level-badge">&#9733; Client Salonify</div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
      <span class="sidebar-nav-section-label">Mon espace</span>
      <a href="{{ route('client.dashboard') }}"
         class="sidebar-nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
        <div class="sidebar-nav-icon">&#9962;</div>Tableau de bord
      </a>
      <a href="{{ route('client.reservations.index') }}"
         class="sidebar-nav-link {{ request()->routeIs('client.reservations.*') ? 'active' : '' }}">
        <div class="sidebar-nav-icon">&#128197;</div>Mes réservations
        @php $aVenir = auth()->user()->reservations()->whereIn('statut',['en_attente','confirmee'])->where('date_heure','>=',now())->count(); @endphp
        @if($aVenir > 0)
          <span class="sidebar-nav-badge">{{ $aVenir }}</span>
        @endif
      </a>
      <a href="{{ route('client.avis.index') }}"
         class="sidebar-nav-link {{ request()->routeIs('client.avis.*') ? 'active' : '' }}">
        <div class="sidebar-nav-icon">&#9733;</div>Mes avis
      </a>
      <a href="{{ route('client.profil.edit') }}"
         class="sidebar-nav-link {{ request()->routeIs('client.profil.*') ? 'active' : '' }}">
        <div class="sidebar-nav-icon">&#9786;</div>Mon profil
      </a>
      <a href="{{ route('client.notifications.index') }}"
         class="sidebar-nav-link {{ request()->routeIs('client.notifications.*') ? 'active' : '' }}">
        <div class="sidebar-nav-icon">&#9993;</div>Notifications
        @php $nbNotif = auth()->user()->notificationsNonLues()->count(); @endphp
        @if($nbNotif > 0)
          <span class="sidebar-nav-badge">{{ $nbNotif }}</span>
        @endif
      </a>

      <div class="sidebar-divider"></div>
      <span class="sidebar-nav-section-label">Découvrir</span>
      <a href="{{ route('villes.index') }}" class="sidebar-nav-link">
        <div class="sidebar-nav-icon">&#127968;</div>Salons près de moi
      </a>
    </nav>

    <div class="sidebar-logout">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">&#10006; Déconnexion</button>
      </form>
    </div>
  </aside>

  <main class="client-main">
    @yield('content')
  </main>

</div>

{{-- ══ LAYOUT GÉRANT (plein écran sous la topbar) ══════════════ --}}
@else
<main style="max-width:1200px;margin:2.2rem auto 4rem;padding:0 2.5rem">
  @yield('content')
</main>
@endif

<script>
document.querySelectorAll('.flash').forEach(f => setTimeout(() => f.remove(), 5000));
</script>
@stack('scripts')
</body>
</html>
