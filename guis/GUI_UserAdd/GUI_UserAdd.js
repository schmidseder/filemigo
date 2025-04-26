/**
 * Copyright (C) 2025 schmidseder.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * User Add Module
 *
 * @author Christian Schmidseder
 */
class GUI_UserAdd extends GUI_Module
{
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        const user = this.element('input#generated-user');
        user.focus();

        const copyButton = this.element('button#copy-button');
        if (copyButton)
            copyButton.addEventListener('click', this.copy);
    }

    copy = (evt) =>
    {
        this.copyToClipboard();
    }

    async copyToClipboard()
    {
        const code = this.element('pre code').innerText;
        const buttonTextSpan = this.element('span.button-text');
        try {
            await navigator.clipboard.writeText(code);
            buttonTextSpan.textContent = 'Kopiert!';
            setTimeout(() => {
                buttonTextSpan.textContent = 'Zeile kopieren';
            }, 1000);
        } catch (err) {
            console.error('Fehler beim Kopieren: ', err);
            // alert('Kopieren fehlgeschlagen.');
        }
    }
}
Weblication.registerClass(GUI_UserAdd);