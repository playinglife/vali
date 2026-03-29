@props([
    'name' => 'booking',
    'labelMonth' => 'Month',
    'labelDay' => 'Day',
    'labelTime' => 'Time',
    'monthsAhead' => 6,
    'startHour' => 9,
    'endHour' => 17,
    'slotMinutes' => 30,
    'weekdaysOnly' => false,
])

@php
    $id = $attributes->get('id') ?? 'cbp-' . preg_replace('/[^\w-]/', '', str_replace(['[', ']'], '-', $name));
@endphp

<div
    id="{{ $id }}"
    data-call-booking-picker
    data-name="{{ $name }}"
    data-months-ahead="{{ (int) $monthsAhead }}"
    data-start-hour="{{ (int) $startHour }}"
    data-end-hour="{{ (int) $endHour }}"
    data-slot-minutes="{{ (int) $slotMinutes }}"
    data-weekdays-only="{{ $weekdaysOnly ? 'true' : 'false' }}"
    {{ $attributes->except('id')->merge(['class' => 'flex w-full max-w-md flex-col gap-4 font-family-two']) }}
>
    <div class="flex flex-col gap-1">
        <label for="{{ $id }}-month" class="text-[0.75rem] tracking-wide text-one font-bold">{{ $labelMonth }}</label>
        <select
            id="{{ $id }}-month"
            name="{{ $name }}[month]"
            data-cbp-month
            data-placeholder-month="{{ $labelMonth }}"
            class="w-full rounded-lg border border-[#ccc] bg-white p-1 text-[0.9rem] text-[#242121] focus:border-five-500 focus:outline-none focus:ring-1 focus:ring-five-500"
            required
        ></select>
    </div>

    <div class="flex flex-col gap-1">
        <label for="{{ $id }}-day" class="text-[0.75rem] tracking-wide text-two font-bold">{{ $labelDay }}</label>
        <select
            id="{{ $id }}-day"
            name="{{ $name }}[day]"
            data-cbp-day
            data-placeholder-day="{{ $labelDay }}"
            class="w-full rounded-lg border border-[#ccc] bg-white p-1 text-[0.9rem] text-[#242121] focus:border-five-500 focus:outline-none focus:ring-1 focus:ring-five-500"
            required
        ></select>
    </div>

    <div class="flex flex-col gap-1">
        <label for="{{ $id }}-time" class="text-[0.75rem] tracking-wide text-three font-bold">{{ $labelTime }}</label>
        <select
            id="{{ $id }}-time"
            name="{{ $name }}[time]"
            data-cbp-time
            data-placeholder-time="{{ $labelTime }}"
            class="w-full rounded-lg border border-[#ccc] bg-white p-1 text-[0.9rem] text-[#242121] focus:border-five-500 focus:outline-none focus:ring-1 focus:ring-five-500"
            required
        ></select>
    </div>
</div>
