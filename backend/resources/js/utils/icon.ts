import { createApp, h } from 'vue';
import * as LucideIcons from '@lucide/vue';

/**
 * Renders a Lucide icon component to an inline SVG string at runtime.
 */
export function getLucideSvgString(name: string): string {
    const IconComponent = (LucideIcons as Record<string, any>)[name];
    if (!IconComponent) return '';
    
    const container = document.createElement('div');
    const app = createApp({
        render() {
            return h(IconComponent, {
                width: 24,
                height: 24,
                strokeWidth: 2,
                stroke: 'currentColor',
                fill: 'none'
            });
        }
    });
    
    app.mount(container);
    const svgHtml = container.innerHTML;
    app.unmount();
    return svgHtml;
}

/**
 * Checks if a string value is a raw SVG.
 */
export function isSvgString(value: string | null | undefined): boolean {
    if (!value) return false;
    return value.trim().startsWith('<svg');
}
