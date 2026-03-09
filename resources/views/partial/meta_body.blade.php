
@if(isset($jsapp['page']['featured_image']) && $jsapp['page']['featured_image'])

  <link itemprop="thumbnailUrl" href="{{ $jsapp['page']['featured_image'] }}" >
  <span itemprop="thumbnail" itemscope itemtype="https://schema.org/ImageObject">
    <link itemprop="url" href="{{ $jsapp['page']['featured_image'] }}">
  </span>
@endif