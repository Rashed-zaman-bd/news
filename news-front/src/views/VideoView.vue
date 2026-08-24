<template>
    <div class="video-container">
    @foreach($videos as $video)
        <a href="{{ url('/video/' . $video->slug) }}" class="video-card">
            <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}" class="thumb">
            <span class="title">{{ $video->title }}</span>
        </a>
    @endforeach
</div>
</template>