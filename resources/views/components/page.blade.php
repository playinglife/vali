@props(['title' => null, 'style' => 'light'])
<div class="flex min-h-screen flex-col w-full items-center justify-center {{ $style == 'light' ? 'bg-white text-black' : 'bg-[#ebe6dd] text-black' }} p-[5em] gap-[5em]">
    <div class="w-[50%]">
        @if(isset($title))
            {{ $title }}
        @endif
    </div>
    @if(isset($content))
        <div class="font-family-two w-[50%]">
            {{ $content }}
        </div>
    @endif
    @if(isset($buttons))
    <div class="flex items-center justify-center w-full gap-[2rem]">
        {{ $buttons }}
    </div>
    @endif
    @if(isset($extra))
        <div class="flex items-center justify-center w-full">
            {{ $extra }}
        </div>
    @endif
</div>

