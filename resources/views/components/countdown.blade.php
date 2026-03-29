@props(['date' => 'date'])
<div class="flex items-center justify-center w-full gap-6 count-down-main">
    <div class="timer">
        <div
            class="pr-1.5 pl-2 relative bg-indigo-50 w-max before:contents-[''] before:absolute before:h-full before:w-0.5 before:top-0 before:left-1/2 before:-translate-x-1/2 before:bg-white before:z-10 ">
            <h3
                class="countdown-element days font-manrope font-semibold text-2xl text-indigo-600 tracking-[15.36px] max-w-[44px] text-center relative z-20">
            </h3>

        </div>
        <p class="text-sm font-normal text-gray-900 mt-1 text-center w-full">days</p>
    </div>
    <div class="timer">
        <div
            class="pr-1.5 pl-2 relative bg-indigo-50 w-max before:contents-[''] before:absolute before:h-full before:w-0.5 before:top-0 before:left-1/2 before:-translate-x-1/2 before:bg-white before:z-10 ">
            <h3
                class="countdown-element hours font-manrope font-semibold text-2xl text-indigo-600 tracking-[15.36px] max-w-[44px] text-center relative z-20">
            </h3>

        </div>
        <p class="text-sm font-normal text-gray-900 mt-1 text-center w-full">hours</p>
    </div>
    <div class="timer">
        <div
            class="pr-1.5 pl-2 relative bg-indigo-50 w-max before:contents-[''] before:absolute before:h-full before:w-0.5 before:top-0 before:left-1/2 before:-translate-x-1/2 before:bg-white before:z-10 ">
            <h3
                class="countdown-element minutes font-manrope font-semibold text-2xl text-indigo-600 tracking-[15.36px] max-w-[44px] text-center relative z-20">
            </h3>

        </div>
        <p class="text-sm font-normal text-gray-900 mt-1 text-center w-full">minutes</p>
    </div>
    <div class="timer">
        <div
            class="pr-1.5 pl-2 relative bg-indigo-50 w-max before:contents-[''] before:absolute before:h-full before:w-0.5 before:top-0 before:left-1/2 before:-translate-x-1/2 before:bg-white before:z-10 ">
            <h3
                class="countdown-element seconds font-manrope font-semibold text-2xl text-indigo-600 tracking-[15.36px] max-w-[44px] text-center relative z-20">
            </h3>

        </div>
        <p class="text-sm font-normal text-gray-900 mt-1 text-center w-full">seconds</p>
    </div>
</div>