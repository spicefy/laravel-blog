{{-- resources/views/feeds/rss.blade.php --}}<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title>Kusoma – Education &amp; Tech News</title>
    <link>{{ config('app.url') }}</link>
    <description>Breaking education and technology news from Kenya and Africa</description>
    <language>en-ke</language>
    <lastBuildDate>{{ now()->toRfc822String() }}</lastBuildDate>
    <atom:link href="{{ route('feed.rss') }}" rel="self" type="application/rss+xml"/>
    <image>
      <url>{{ asset('images/logo.png') }}</url>
      <title>Kusoma</title>
      <link>{{ config('app.url') }}</link>
    </image>

    @foreach($posts as $post)
    <item>
      <title><![CDATA[{{ $post->title }}]]></title>
      <link>{{ route('post.show', $post->slug) }}</link>
      <guid isPermaLink="true">{{ route('post.show', $post->slug) }}</guid>
      <pubDate>{{ $post->published_at->toRfc822String() }}</pubDate>
      <dc:creator><![CDATA[{{ $post->author->name }}]]></dc:creator>
      <category><![CDATA[{{ $post->category->name }}]]></category>
      <description><![CDATA[{{ $post->seo_description }}]]></description>
      <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded>
      @if($post->featured_image)
      <enclosure url="{{ asset($post->featured_image) }}" type="image/jpeg" length="0"/>
      @endif
    </item>
    @endforeach

  </channel>
</rss>
