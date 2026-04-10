import { createDialogComponent } from './dialog.js';

const registry = {
    dialog: createDialogComponent,
};

/**
 * Minimal Clefplay-style dynamic component registry (plain DOM, no Vue mount).
 */
export function useDynamicComponents() {
    return {
        /**
         * @param {string} type
         * @param {Record<string, unknown>} props
         */
        createComponent(type, props) {
            const factory = registry[type];
            if (typeof factory !== 'function') {
                throw new Error(`useDynamicComponents: unknown component "${type}"`);
            }
            return factory(props);
        },
    };
}
