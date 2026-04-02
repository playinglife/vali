<div class="loading" role="status" aria-live="polite" aria-busy="true" aria-label="Loading">
    <div class="loading__inner">
        <span class="loading__letter">L</span>
        <span class="loading__letter">O</span>
        <span class="loading__letter">A</span>
        <span class="loading__letter">D</span>
        <span class="loading__letter">I</span>
        <span class="loading__letter">N</span>
        <span class="loading__letter">G</span>
    </div>
</div>

@once
    <style>
        .loading {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-background-transparent-dark);
            backdrop-filter: blur(2px);
        }

        .loading__inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: var(--gap-small);
            min-height: 4rem;
            font-family: var(--font-family-one);
            font-size: clamp(1.25rem, 4vw, 2rem);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.35);
        }

        .loading__letter {
            display: inline-block;
            color: var(--color-text-light);
            filter: blur(0);
            animation: loading-letter 1.5s ease-in-out infinite alternate;
        }

        .loading__letter:nth-child(1) { animation-delay: 0s; }
        .loading__letter:nth-child(2) { animation-delay: 0.12s; }
        .loading__letter:nth-child(3) { animation-delay: 0.24s; }
        .loading__letter:nth-child(4) { animation-delay: 0.36s; }
        .loading__letter:nth-child(5) { animation-delay: 0.48s; }
        .loading__letter:nth-child(6) { animation-delay: 0.6s; }
        .loading__letter:nth-child(7) { animation-delay: 0.72s; }

        @keyframes loading-letter {
            0% {
                filter: blur(0);
                color: var(--color-text-light);
                opacity: 1;
            }
            100% {
                filter: blur(3px);
                color: var(--color-three);
                opacity: 0.85;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .loading__letter {
                animation: none;
                filter: none;
                opacity: 1;
            }
        }

    </style>
@endonce
