@once
    <style>
    .root-products__checkbox .round,
    .root-products__radio .round {
        position: relative;
    }

    .root-products__checkbox .round label,
    .root-products__radio .round label {
        display: inline-flex;
        align-items: flex-start;
        gap: 0.5em;
        cursor: pointer;
        margin: 0;
        background: none;
        border: none;
        height: auto;
        width: auto;
        padding: 0;
        font-size: var(--text-size-small);
    }

    /*
     * Size with rem / --text-size-small, not em: em follows the label’s inherited
     * font-size, which differs between .root-products__checkbox-list and table
     * cells, so category vs option checkboxes looked different sizes.
     */
    .root-products__checkbox-visual {
        flex-shrink: 0;
        box-sizing: border-box;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        width: calc(1.7 * var(--text-size-small));
        height: calc(1.7 * var(--text-size-small));
        display: block;
        position: relative;
    }

    .root-products__checkbox-visual:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        width: calc(0.75 * var(--text-size-tiny));
        height: calc(0.25 * var(--text-size-tiny));
        left: calc(0.56 * var(--text-size-tiny));
        top: calc(0.76 * var(--text-size-tiny));
        opacity: 0;
        position: absolute;
        transform: rotate(-45deg);
    }


    .root-products__radio .round label.root-products__radio-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5em;
        justify-content: center;
        text-align: center;
    }

    .root-products__checkbox .round input[type="checkbox"],
    .root-products__radio .round input[type="radio"] {
        visibility: hidden;
        display: none;
        opacity: 0;
    }

    .root-products__checkbox .round input[type="checkbox"]:checked + label .root-products__checkbox-visual,
    .root-products__radio .round input[type="radio"]:checked + label .root-products__checkbox-visual {
        background-color: var(--color-one);
        border-color: var(--color-one);
    }

    .root-products__checkbox .round input[type="checkbox"]:checked + label .root-products__checkbox-visual:after,
    .root-products__radio .round input[type="radio"]:checked + label .root-products__checkbox-visual:after {
        opacity: 1;
    }
    </style>
@endonce
