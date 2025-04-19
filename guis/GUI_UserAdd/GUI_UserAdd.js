/**
 * User Add Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
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