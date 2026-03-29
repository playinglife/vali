@props(['text' => 'text', 'url' => null, 'target' => '_self', 'light' => 'true', 'type' => 'button'])

@if($url)
    <a href="{{ $url }}" target="{{ $target }}">
        <button type="{{ $type }}" class="self-center rounded-[99rem] bg-five-500 {{ $light == 'true' ? 'text-white' : 'text-white' }} 
                hover:bg-five-700 hover:text-white duration-300 transition-colors border px-8 py-2.5
                text-[0.8rem] font-normal text-center p-[2rem] gap-[0.2rem] flex flex-col items-center justify-center cursor-pointer">
            {{ $text }}
        </button>        
    </a>
@else
    <button type="{{ $type }}" class="self-center rounded-[99rem] bg-five-500 {{ $light == 'true' ? 'text-white' : 'text-white' }} 
            hover:bg-five-700 hover:text-white duration-300 transition-colors border px-8 py-2.5
            text-[0.8rem] font-normal text-center p-[2rem] gap-[0.2rem] flex flex-col items-center justify-center cursor-pointer">
        {{ $text }}
    </button>
@endif