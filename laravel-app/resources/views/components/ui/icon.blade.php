@props([
    'name',
    'class' => 'h-5 w-5',
])

@php
    $icons = [
        'menu' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>',
        'sun' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.75a.75.75 0 01.75-.75V3a.75.75 0 00-1.5 0v1a.75.75 0 01.75.75zM12 20.25a.75.75 0 01-.75.75V21a.75.75 0 001.5 0v-0.0a.75.75 0 01-.75-.75z"/></svg>',
        'moon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>',
        'bell' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
        'chevron-down' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>',
        'x' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>',
        'shield' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l7 3v4c0 5-3.58 9.74-7 11-3.42-1.26-7-6-7-11V6l7-3z"/></svg>',
        'rocket' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3L7 8l-4 1 4 1 5 5 1-5 5-5-1-4-5 1z"/></svg>',
        'map' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5-2V6l5 2 5-2 5 2v12l-5-2-5 2z"/></svg>',
        'users' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-1a4 4 0 00-4-4h-1M9 20H4v-1a4 4 0 014-4h1m0-6a4 4 0 100-8 4 4 0 000 8z"/></svg>',
        'activity' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12h3l3 8 4-16 3 8h4"/></svg>',
        'settings' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.5V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.5 1.65 1.65 0 00-1.82.33l-.06.06A2 2 0 014.29 17.9l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.5-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.5-1 1.65 1.65 0 00-.33-1.82L4.2 5.71A2 2 0 016.99 2.88l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001 1.5V5a2 2 0 114 0v.09c0 .62.37 1.17 1 1.5h.09a1.65 1.65 0 001.82-.33l.06-.06A2 2 0 0119.4 4.1l-.06.06a1.65 1.65 0 00-.33 1.82v.09c.33.63.88 1 1.5 1H21a2 2 0 110 4h-.09c-.62 0-1.17.37-1.5 1v.09c-.33.63-.88 1-1.5 1h-.09c-.62 0-1.17.37-1.5 1v.09z"/></svg>',
        'download' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v12m0 0l-4-4m4 4l4-4M21 21H3"/></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>',
        'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M12 3l7 3v4a9 9 0 01-7 8.7A9 9 0 015 13V6l7-3z"/></svg>',
        'external' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>',
        'edit' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4h7l2 2v7M16 3l5 5M2 20l4-1 9-9 1 1-9 9-1 4-4-4z"/></svg>',
        'trash' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7L5 7M10 11v6M14 11v6M6 7l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12"/></svg>',
        'filter' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M6 12h12M10 18h4"/></svg>',
        'eye' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
        'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>',
        'arrow-up' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 10l7-7 7 7M12 3v18"/></svg>',
        'grid' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z"/></svg>',
        'circle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8"/></svg>',
        'funding' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1c-1.1 0-2 .9-2 2v1.06A7 7 0 005 11v2a7 7 0 007 7 7 7 0 007-7v-2a7 7 0 00-5-6.94V3c0-1.1-.9-2-2-2zm1 15.5h-2v-1h2v1zm1.07-4.75c-.2.97-1.02 1.5-2.07 1.5h-1v-1.5c0-.83.67-1.5 1.5-1.5.83 0 1.5-.67 1.5-1.5S12.83 5.5 12 5.5c-1.1 0-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 1.14-.57 1.97-1.07 2.25z"/></svg>',
    ];
@endphp

<span {!! $attributes->merge(['class' => $class]) !!} aria-hidden="true">{!! $icons[$name] ?? '<span></span>' !!}</span>
