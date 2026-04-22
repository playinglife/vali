@props([
    'id',
    'mode' => 'zoom',
    'ariaLabel' => '',
    'closeLabel' => 'Close',
    'lightboxMinZoom' => 1,
    'lightboxMaxZoom' => 12,
    'openOnLoad' => false,
    'showCloseButton' => true,
    'showOkButton' => false,
    'okLabel' => 'OK',
    'dismissOnBackdrop' => false,
    'allowEscapeKey' => true,
])

@php
    $mode = $mode === 'message' ? 'message' : 'zoom';
    $lightboxMinZoom = max(0.05, min(50.0, (float) $lightboxMinZoom));
    $lightboxMaxZoom = max(1.0, min(50.0, (float) $lightboxMaxZoom));
    if ($lightboxMinZoom > $lightboxMaxZoom) {
        $lightboxMinZoom = $lightboxMaxZoom;
    }
@endphp

<dialog id="{{ $id }}" class="modal-dialog {{ $mode === 'zoom' ? 'modal-dialog--zoom' : 'modal-dialog--message' }}" aria-modal="true"
    @if(filled($ariaLabel)) aria-label="{{ $ariaLabel }}" @endif
    @if($mode === 'zoom')
        data-lightbox-min-zoom="{{ $lightboxMinZoom }}"
        data-lightbox-max-zoom="{{ $lightboxMaxZoom }}"
    @endif
    @if($openOnLoad) data-open-on-load="1" @endif
    @if($mode === 'message' && $dismissOnBackdrop) data-dismiss-on-backdrop="1" @endif
    @if($mode === 'message' && ! $allowEscapeKey) data-prevent-escape="1" @endif
>
    @if($mode === 'zoom')
        <div class="modal-dialog__pane">
            <button type="button" class="modal-dialog__close" aria-label="{{ $closeLabel }}">
                ×
            </button>
            <div class="modal-dialog__lightbox-zoom-wrap">
                {{ $slot }}
            </div>
        </div>
    @else
        <div class="modal-dialog__message-shell">
            @if ($showCloseButton)
                <button type="button" class="modal-dialog__close modal-dialog__close--message" aria-label="{{ $closeLabel }}">
                    ×
                </button>
            @endif
            <div class="modal-dialog__message-card">
                {{ $slot }}
                @if ($showOkButton)
                    <div class="modal-dialog__message-actions">
                        <button type="button" class="modal-dialog__ok">
                            {{ $okLabel }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</dialog>

@once
    <style>
        dialog.modal-dialog {
            margin: 0;
            padding: 0;
            border: none;
            max-width: none;
            max-height: none;
            width: 100%;
            height: 100%;
            background: transparent;
            box-sizing: border-box;
            z-index: 250;
        }
        dialog.modal-dialog::backdrop {
            background: var(--color-background-transparent-dark);
        }
        .modal-dialog--message::backdrop {
            background: color-mix(in srgb, var(--color-background-transparent-dark) 92%, black);
        }
        .modal-dialog__pane {
            box-sizing: border-box;
            width: 100%;
            min-height: 100%;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(0.75rem, 4vw, 2rem);
            cursor: zoom-out;
            overflow: auto;
            overscroll-behavior: contain;
        }
        .modal-dialog__lightbox-zoom-wrap {
            --lb-zoom: 1;
            box-sizing: border-box;
            flex-shrink: 0;
            width: calc(80vw * var(--lb-zoom));
            height: min(calc(80vh * var(--lb-zoom)), calc(80dvh * var(--lb-zoom)));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-dialog__lightbox-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
            cursor: default;
        }
        .modal-dialog__close {
            position: fixed;
            top: clamp(0.5rem, 1.5vw, 1rem);
            right: clamp(0.5rem, 1.5vw, 1rem);
            z-index: 1;
            margin: 0;
            padding: 0.2em 0.55em;
            border: 0;
            border-radius: var(--border-radius-small);
            background: color-mix(in srgb, var(--color-background) 92%, white);
            color: var(--color-text-dark);
            font-family: var(--font-family-one);
            font-size: 1.35rem;
            line-height: 1;
            cursor: pointer;
            opacity: 0.92;
        }
        .modal-dialog__close:hover {
            opacity: 1;
        }
        .modal-dialog__close:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }
        .modal-dialog__message-shell {
            box-sizing: border-box;
            min-height: 100%;
            min-height: 100dvh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 4vw, 2.5rem);
        }
        .modal-dialog__close--message {
            position: fixed;
        }
        .modal-dialog__message-card {
            box-sizing: border-box;
            max-width: 28rem;
            width: 100%;
            margin: 0;
            padding: var(--padding-small);
            border-radius: var(--border-radius-medium);
            background: var(--color-background-light);
            color: var(--color-text-dark);
            box-shadow: 0 0.5rem 2rem color-mix(in srgb, black 18%, transparent);
        }
        .modal-dialog__message-card p {
            margin: 0;
        }
        .modal-dialog__message-actions {
            margin-top: var(--gap-large);
            display: flex;
            justify-content: center;
        }
        .modal-dialog__ok {
            margin: 0;
            padding: 0.55em 1.75em;
            border: 1px solid color-mix(in srgb, var(--color-border) 50%, transparent);
            border-radius: var(--border-radius-medium);
            background: color-mix(in srgb, var(--color-one) 18%, var(--color-background));
            color: var(--color-text-dark);
            font-family: var(--font-family-one);
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
        }
        .modal-dialog__ok:hover {
            opacity: 0.9;
        }
        .modal-dialog__ok:focus-visible {
            outline: 2px solid var(--color-one);
            outline-offset: 2px;
        }
    </style>
@endonce

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('dialog.modal-dialog[data-open-on-load="1"]').forEach(function (dlg) {
                if (typeof dlg.showModal === 'function') {
                    dlg.showModal();
                }
            });

            document.querySelectorAll('dialog.modal-dialog--message[data-dismiss-on-backdrop="1"]').forEach(function (dlg) {
                dlg.addEventListener('click', function (e) {
                    if (e.target === dlg) {
                        dlg.close();
                        return;
                    }
                    const shell = dlg.querySelector('.modal-dialog__message-shell');
                    if (shell && e.target === shell) {
                        dlg.close();
                    }
                });
            });

            document.querySelectorAll('.modal-dialog__ok').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const dlg = btn.closest('dialog');
                    if (dlg && typeof dlg.close === 'function') {
                        dlg.close();
                    }
                });
            });

            document.querySelectorAll('dialog[data-prevent-escape="1"]').forEach(function (dlg) {
                dlg.addEventListener('cancel', function (e) {
                    e.preventDefault();
                });
            });
        });
    </script>
@endonce
