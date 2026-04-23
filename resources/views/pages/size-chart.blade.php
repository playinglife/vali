@extends('layouts.app')

@section('title','Size Chart')

@section('content')
    <div class="root-views-size-chart">
        <div class="grid root-views-size-chart__grid">
            <div class="grid grid-middle grid-center root-views-size-chart__main-title">
                <h2 class="dark"> {{ __('components.product.size_chart') }} </h2>
            </div>
            <x-miniviews.panel :padding="false">
                <div class="size-chart-wrap">
                    <table class="size-chart-table">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Chest size (cm)</th>
                                <th>Neck size (cm)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>XXXS</td><td>78</td><td>30</td></tr>
                            <tr><td>XXS</td><td>82</td><td>32</td></tr>
                            <tr><td>XS</td><td>86</td><td>34</td></tr>
                            <tr><td>S</td><td>92</td><td>36</td></tr>
                            <tr><td>M</td><td>98</td><td>38</td></tr>
                            <tr><td>L</td><td>104</td><td>40</td></tr>
                            <tr><td>XL</td><td>110</td><td>42</td></tr>
                            <tr><td>XXL</td><td>116</td><td>44</td></tr>
                            <tr><td>XXXL</td><td>122</td><td>46</td></tr>
                        </tbody>
                    </table>
                </div>
            </x-miniviews.panel>
        </div>
        @include('layouts.footer', ['backgroundImage' => 'none'])
    </div>
@endsection

@once
    <style lang="scss" scoped>
        .root-views-size-chart {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
            overflow: auto;
            position: relative;
            isolation: isolate;
            padding-top: 4em;
            padding-bottom: 0em;
            &::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("{{ asset('images/sizechart.png') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                filter: blur(8px);
                transform: scale(1.05);
                z-index: -1;
                pointer-events: none;
            }
            & > * {
                position: relative;
                z-index: 1;
            }
            & > .page-1 {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
        .root-views-size-chart__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-views-size-chart__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .size-chart-wrap {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            justify-content: center;
            padding: var(--padding-large);
        }
        .size-chart-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--color-background-transparent-light);
            border-radius: var(--border-radius-small);
            overflow: hidden;
            & > thead  > tr > th {
                font-weight: 700;
                font-family: var(--font-family-one);
                color: var(--color-text-dark);
            }
            & > tbody  > tr > td {
                color: var(--color-text-dark);
            }
        }
        .size-chart-table th, .size-chart-table td {
            padding: 0.6rem 0.8rem;
            border: 1px solid color-mix(in srgb, var(--color-border) 65%, transparent);
            text-align: left;
        }
        .size-chart-table th {
            font-weight: 700;
            background: var(--color-background-transparent-light);
        }
    </style>
@endonce
