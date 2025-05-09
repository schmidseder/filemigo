/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
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