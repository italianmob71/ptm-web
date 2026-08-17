/**
 * TableHeaderToggle — custom CKEditor 5 plugin for PTM
 *
 * Adds a toolbar button that toggles the first row of the current table
 * between a header row (<thead>) and a normal data row.
 *
 * Uses the TableUtils plugin's setHeadingRowsCount() method to set
 * the headingRows model attribute (0 = no header, 1 = first row is header).
 *
 * The button is only enabled when the cursor is inside a table.
 * The button shows an active/on state when the table already has a header row.
 */

import { Plugin, ButtonView } from 'ckeditor5';

export default class TableHeaderToggle extends Plugin {
    static get pluginName() {
        return 'TableHeaderToggle';
    }

    static get requires() {
        // TableUtils provides setHeadingRowsCount
        return ['TableUtils'];
    }

    init() {
        const editor = this.editor;
        const t = editor.t;

        editor.ui.componentFactory.add('tableHeaderToggle', locale => {
            const view = new ButtonView(locale);

            view.set({
                label: t('Toggle Header Row'),
                icon: headerIcon,
                tooltip: true,
                isToggleable: true,
            });

            // Bind enabled state to whether we're in a table
            view.bind('isEnabled').to(
                editor.commands,
                'isEnabled',
                () => {
                    const position = editor.model.document.selection.getFirstPosition();
                    if (!position) return false;
                    return this.findTable(position) !== null;
                }
            );

            // Reflect active state (has header row)
            const updateState = () => {
                const position = editor.model.document.selection.getFirstPosition();
                if (!position) {
                    view.isOn = false;
                    view.isEnabled = false;
                    return;
                }
                const table = this.findTable(position);
                if (!table) {
                    view.isOn = false;
                    view.isEnabled = false;
                    return;
                }
                view.isEnabled = true;
                view.isOn = (table.getAttribute('headingRows') || 0) > 0;
            };

            editor.model.document.on('change', updateState);
            updateState();

            view.on('execute', () => {
                editor.model.change(writer => {
                    const position = editor.model.document.selection.getFirstPosition();
                    if (!position) return;

                    const table = this.findTable(position);
                    if (!table) return;

                    const currentHeadingRows = table.getAttribute('headingRows') || 0;

                    // Toggle: if 0 header rows → set to 1, otherwise → set to 0
                    const newHeadingRows = currentHeadingRows > 0 ? 0 : 1;

                    // Use TableUtils to apply the change
                    const tableUtils = editor.plugins.get('TableUtils');
                    tableUtils.setHeadingRowsCount(table, newHeadingRows, writer);
                });
            });

            return view;
        });
    }

    /**
     * Walk up the model tree to find the containing table element.
     */
    findTable(position) {
        let element = position.parent;
        while (element && element.name !== 'table' && element.parent) {
            element = element.parent;
        }
        return element && element.name === 'table' ? element : null;
    }
}

// SVG icon — table with bold top row (header)
const headerIcon = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M3 3h18c.55 0 1 .45 1 1v16c0 .55-.45 1-1 1H3c-.55 0-1-.45-1-1V4c0-.55.45-1 1-1zm1 8h16v-3H4v3zm0 5h16v-3H4v3zM4 7h16V5H4v2z"/></svg>';
