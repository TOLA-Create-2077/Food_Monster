@include('layout.header')

@include('layout.sidebar')

<section class="content">

<header class="topbar">

<div class="topbar-left">
<div class="page-title">{{ $pageTitle ?? '' }}</div>
</div>

</header>

<main class="main">

@yield('content')

</main>

</section>

@include('layout.footer')