/**
 * ScriptureQuote — custom CKEditor 5 plugin for PTM
 *
 * Adds a toolbar button with a quote/scroll icon. When clicked, wraps the
 * selected text (or current block) in a <div class="ptm-scripture"> element
 * styled with a left accent-color vertical bar, distinct from blockQuote.
 *
 * Clicking again toggles it off (unwraps the scripture div back to a paragraph).
 *
 * The CSS for both the editor (.ck-content .ptm-scripture) and the front-end
 * (.ptm-scripture) lives in ckeditor5.css and the public theme respectively.
 */

import { Plugin, ButtonView, Element, View } from 'ckeditor5';

export default class ScriptureQuote extends Plugin {
    static get pluginName() {
        return 'ScriptureQuote';
    }

    init() {
        const editor = this.editor;

        // ── Schema: define the scriptureQuote block element ──────────────
        editor.model.schema.register('scriptureQuote', {
            inheritAllFrom: '$block',
            allowChildren: ['$block'],
            isObject: false,
            allowAttributes: ['class'],
        });

        // ── Model → View conversion (data/downcast) ──────────────────────
        // Renders as <div class="ptm-scripture">…</div> in saved HTML.
        editor.conversion.for('dataDowncast').elementToElement({
            model: 'scriptureQuote',
            view: {
                name: 'div',
                classes: ['ptm-scripture'],
            },
        });

        // ── Model → View conversion (editing/downcast) ──────────────────
        // Same element, but with extra editing classes for the editor UI.
        editor.conversion.for('editingDowncast').elementToElement({
            model: 'scriptureQuote',
            view: (modelElement, { writer }) => {
                const div = writer.createContainerElement('div', {
                    class: 'ptm-scripture',
                });
                return div;
            },
        });

        // ── View → Model conversion (upcast) ─────────────────────────────
        // When loading HTML, any <div class="ptm-scripture"> becomes a
        // scriptureQuote model element.
        editor.conversion.for('upcast').elementToElement({
            view: {
                name: 'div',
                classes: 'ptm-scripture',
            },
            model: 'scriptureQuote',
        });

        // ── Toolbar button ──────────────────────────────────────────────
        editor.ui.componentFactory.add('scriptureQuote', locale => {
            const view = new ButtonView(locale);
            const t = editor.t;

            view.set({
                label: t('Scripture Quote'),
                icon: scriptureIcon,
                tooltip: true,
                isToggleable: true,
            });

            // Reflect current state in button (active when inside a scripture block)
            const updateIsOn = () => {
                const position = editor.model.document.selection.getFirstPosition();
                if (!position) return;

                let block = position.parent;
                while (block && block.name !== 'scriptureQuote') {
                    block = block.parent;
                }
                view.isOn = !!block && block.name === 'scriptureQuote';
            };

            editor.model.document.on('change', updateIsOn);
            updateIsOn();

            view.on('execute', () => {
                editor.model.change(writer => {
                    const position = editor.model.document.selection.getFirstPosition();
                    if (!position) return;

                    // Find the current top-level block
                    let block = position.parent;
                    while (block && block.parent && block.parent.name !== 'root') {
                        block = block.parent;
                    }

                    if (block && block.name === 'scriptureQuote') {
                        // ── Toggle OFF: unwrap back to a paragraph ──────────
                        const parent = block.parent;
                        const start = writer.createPositionBefore(block);
                        const end = writer.createPositionAfter(block);

                        // Move children out
                        const children = Array.from(block.getChildren());
                        for (const child of children) {
                            writer.move(writer.createPositionAt(child, 'before'), start);
                            break; // move the whole range below
                        }

                        // Move all children before removing the block
                        writer.move(writer.createRangeIn(block), start);
                        writer.remove(block);
                    } else if (block && block.name !== 'scriptureQuote') {
                        // ── Toggle ON: wrap the block in a scriptureQuote ──
                        const scriptureElement = writer.createElement('scriptureQuote');
                        writer.insert(scriptureElement, block, 'before');
                        writer.move(writer.createRangeOn(block), scriptureElement, 0);
                    }
                });
            });

            return view;
        });
    }
}

// SVG icon — open book / scroll
const scriptureIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 4.5c-1.25-1.02-3.36-1.5-5-1.5v12c1.64 0 3.75.48 5 1.5 1.25-1.02 3.36-1.5 5-1.5v-12c-1.64 0-3.75.48-5 1.5z"/></svg>';
