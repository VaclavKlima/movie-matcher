@props(['id', 'class' => ''])

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
    @switch($id)
        @case('clapper')
            <path d="M4 9l4-4h12l-4 4H4z" />
            <rect x="4" y="9" width="16" height="11" rx="2" />
            @break
        @case('reel')
            <circle cx="12" cy="12" r="8" />
            <circle cx="12" cy="12" r="2" />
            <circle cx="8.5" cy="10" r="1.2" />
            <circle cx="15.5" cy="10" r="1.2" />
            <circle cx="12" cy="15.5" r="1.2" />
            @break
        @case('ticket')
            <path d="M5 7h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V9a2 2 0 0 1 2-2z" />
            <path d="M9 9v6" />
            @break
        @case('projector')
            <rect x="3.5" y="9" width="10" height="7" rx="2" />
            <circle cx="8.5" cy="12.5" r="2" />
            <path d="M13.5 10.5h4.5l2.5-2v10l-2.5-2h-4.5" />
            @break
        @case('star')
            <path d="M12 3l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4-3.9-3.8 5.4-.8z" />
            @break
        @default
            <circle cx="8" cy="7" r="2.2" />
            <circle cx="12" cy="6" r="2.4" />
            <circle cx="16" cy="7" r="2.2" />
            <rect x="6" y="9.5" width="12" height="10.5" rx="2" />
    @endswitch
</svg>
