@php

use App\Http\Controllers\CartController;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductOptionValue;
use App\Models\ProductOption;
   
    $transferData = [];
    $countries = [
        "Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda",
        "Argentina","Armenia","Australia","Austria","Azerbaijan",
        "Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan",
        "Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi",
        "Cabo Verde","Cambodia","Cameroon","Canada","Central African Republic","Chad","Chile","China",
        "Colombia","Comoros","Congo (Congo-Brazzaville)","Costa Rica","Croatia","Cuba","Cyprus","Czechia",
        "Democratic Republic of the Congo","Denmark","Djibouti","Dominica","Dominican Republic",
        "Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia",
        "Fiji","Finland","France",
        "Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana",
        "Haiti","Honduras","Hungary",
        "Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy",
        "Jamaica","Japan","Jordan",
        "Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan",
        "Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg",
        "Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico",
        "Micronesia","Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar",
        "Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway",
        "Oman",
        "Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal",
        "Qatar",
        "Romania","Russia","Rwanda",
        "Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino",
        "Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia",
        "Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria",
        "Tajikistan","Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu",
        "Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan",
        "Vanuatu","Vatican City","Venezuela","Vietnam",
        "Yemen",
        "Zambia","Zimbabwe"
    ];
@endphp



<!-- TEMPLATE -->
<x-menu-height-compensator />
<div data-reference="checkout" class="root-checkout">
    <script type="application/json" class="product-detail-json">
        @json($transferData)
    </script>
    <div class="grid root-checkout__grid">
        <div class="grid grid-middle grid-center root-checkout__main-title">
            <h2 class="dark"> {{ __('pages.checkout.title1') }} </h2>
        </div>



        <!-- checkout form -->
            <x-miniviews.panel :padding="false">
                <div class="root-checkout__line">
                    <form method="post" action="{{ route('confirm_order') }}" class="root-checkout__line-content" novalidate>
                        @csrf

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.name') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <input type="text" name="name" value="{{ old('name') }}" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" />
                                @error('name')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">    
                                <h5 class="dark"> {{ __('pages.checkout.email') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" />
                                @error('email')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.company') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <input type="text" name="company" value="{{ old('company') }}" aria-invalid="{{ $errors->has('company') ? 'true' : 'false' }}" />
                                @error('company')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.country') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <select name="country" aria-invalid="{{ $errors->has('country') ? 'true' : 'false' }}">
                                    <option value="" @selected(old('country') === null || old('country') === '')>{{ __('pages.checkout.country') }}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}" @selected(old('country') === $country)>{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.city') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <input type="text" name="city" value="{{ old('city') }}" aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}" />
                                @error('city')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.phone') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <input type="text" name="phone" value="{{ old('phone') }}" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" />
                                @error('phone')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.notes') }} </h5>
                            </div>
                            <div class="root-checkout__line-content-item-value">
                                <textarea name="notes" aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-tiny root-checkout__error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item">
                            <div class="root-checkout__line-content-item-title">
                                <h5 class="dark"> {{ __('pages.checkout.message') }} </h5>
                            </div>
                        </div>

                        <div class="root-checkout__line-content-item-buttons">
                            <x-button type="submit" text="{{ __('pages.checkout.send_order') }}" aria-label="{{ __('pages.checkout.send_order') }}" />
                        </div>

                    </form>
                </div>
            </x-miniviews.panel>

    </div>
</div>



<!-- STYLES -->
@once
    <style lang="scss" scoped>
        .root-checkout__title {
            padding: var(--padding-large);
        }
        .root-checkout {
            box-sizing: border-box;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
        }
        .root-checkout__grid {
            gap: var(--gap-large);
            padding: 0 25% var(--padding-huge) 25%;
        }
        .root-checkout__group {
            padding: var(--padding-small) var(--padding-large);
            gap: var(--gap-large);

            &.grid > .col:first-child {
                flex: 0 1 auto;
                max-width: none;
            }

            &.grid > .col:last-child {
                flex: 1 1 0%;
                min-width: 0;
            }
        }
        .root-checkout__line-content {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: var(--gap-medium);
            padding: var(--padding-huge);
            box-sizing: border-box;
            & > .root-checkout__line-content-item { 
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: var(--gap-small);
                & > .root-checkout__line-content-item-title {
                    & > h5 {
                        margin: 0;
                    }
                }
                & > .root-checkout__line-content-item-value {
                    & > input, & > textarea, & > select {
                        width: 100%;
                    }
                }
            }
        }
        .root-checkout__main-title {
            flex: 1 1 auto;
            min-width: 0;
            padding-right: var(--gap-medium);
            padding-top: var(--padding-medium);
        }
        .root-checkout__line {
            width: 100%;
        }
        .root-checkout__line-title {
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            padding-right: var(--gap-medium);
        }

        .root-checkout__remove {
            flex-shrink: 0;
        }

        .root-checkout__line-total {
            font-family: var(--font-family-one);
            font-weight: var(--font-weight-bold);
            font-size: var(--text-size-small);
            color: var(--color-text-dark);
        }

        .root-checkout__remove-button {
            justify-content: flex-end;
        }

        .root-checkout__total {
            width: 100%;
            gap: var(--gap-medium);
            & > .col {
                display: flex;
            }
            & > .col:last-child {
                justify-content: flex-end;
            }
            & > .col:first-child > table > tbody > tr > td {
                font-family: var(--font-family-one);
                font-weight: var(--font-weight-bold);
                font-size: var(--text-size-normal);
                color: var(--color-text-dark);
                padding: 0 var(--padding-tiny);
            }
            & > .col:first-child > table > tbody > tr > td.root-checkout__total-currency {
                padding-left: 0;
            }
        }

        .root-checkout__total-text {
            text-align: right;
        }
        .root-checkout__total-value {
            text-align: right;
        }
        .root-checkout__total {
            padding: var(--padding-large);
        }
        .root-checkout__options {
            display: flex;
            flex-direction: column;
            font-size: var(--text-size-tiny);
            gap: var(--gap-medium);
        }
        .root-checkout__total-buttons {
            display: flex;
            flex-direction: row;
            justify-content: flex-end;
            gap: var(--gap-medium);
            padding: var(--padding-small);
            & > form {
                margin: 0;
                padding: 0;
            }
        }
        .root-checkout__line-content-item-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: var(--gap-medium);
            padding: var(--padding-small);
            & > form {
                margin: 0;
                padding: 0;
            }
        }
        .root-checkout__error {
            color: #b00020;
            margin: var(--padding-tiny) 0 0 0;
        }
    </style>
@endonce
