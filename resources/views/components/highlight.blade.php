@props(['text' => 'text', 'image' => 'image', 'imagePosition' => 'imagePosition'])
<div class="flex flex-row w-3/4 items-center justify-center text-[2em] bg-five-200 rounded-[0.1em] p-[1em] h-[12em] shadow-xl rotate-3d">
    <div class="flex w-1/2 items-center justify-center text-[0.8em] text-center rotate-3d-text">
        <span class="text-shadow-lg">{{ $text }}</span>
    </div>
    <div class="flex w-1/2 h-full items-center justify-center">
        <div class="flex h-full aspect-square items-center justify-center overflow-hidden rounded-[0.1em] rotate-3d-image">
            <img src="{{ asset($image) }}" class="w-full h-full object-cover" alt="Clef Play">
        </div>
    </div>
</div>