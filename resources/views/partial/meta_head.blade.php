@if($jsapp['page']['title'])
<title>{{ $jsapp['page']['title'] }}</title>
<meta property="og:title" content="{{ $jsapp['page']['title'] }}" />
<meta name="twitter:title" content="{{ $jsapp['page']['title'] }}" />
@else

<title>{{ $jsapp['shop']['name'] }}</title>
<meta property="og:title" content="{{ $jsapp['shop']['name'] }}" />
<meta name="twitter:title" content="{{ $jsapp['shop']['name'] }}" />
@endif
<meta property="og:url" content="{{ url()->full() }}" />
<meta property="og:type" content="website" data-qmeta="ogType" />
<meta property="og:site_name" content="{{ $jsapp['shop']['name'] }}" />

<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="{{ request()->getHost() }}" />
<meta property="twitter:url" content="{{ url()->full() }}">

@if(isset($jsapp['page']['description']) && $jsapp['page']['description'])
<meta name="description" content="{{ $jsapp['page']['description'] }}" data-qmeta="description" />
<meta property="og:description" content="{{ $jsapp['page']['description'] }}" data-qmeta="description" />
<meta name="twitter:description" content="{{ $jsapp['page']['description'] }}" data-qmeta="description" />
@else
<meta name="description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="description" />
<meta property="og:description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="description" />
<meta name="twitter:description" content="{{ $jsapp['shop']['description'] }}" data-qmeta="description" />
@endif

@if(isset($jsapp['page']['featured_image']) && $jsapp['page']['featured_image'])
<meta property="og:image" content="{{ $jsapp['page']['featured_image'] }}" />
<meta name="twitter:image" content="{{ $jsapp['page']['featured_image'] }}" />
@endif

<meta name="csrf-token" content="{{ csrf_token() }}" />

<meta name=apple-mobile-web-app-title content="{{ $jsapp['shop']['name'] }}">

@isset($jsapp['head']['midtrans_script'])
<script type="text/javascript"
		src="{{ $jsapp['head']['midtrans_script'] }}"
      data-client-key="{{ $jsapp['head']['midtrans_client_key'] }}"></script>
@endisset