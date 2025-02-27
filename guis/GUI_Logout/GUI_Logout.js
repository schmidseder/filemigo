/**
 * Logout Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_Logout extends GUI_Module
{
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        const button = this.element('button');
        button.addEventListener('click', this.onLogout);
    }

    onLogout = async (evt) => {
        const button = evt.target;
        button.disabled = true;
        try {
            const response = await this.request('doLogout', {}, { method: 'POST'});
            if (response.success) {
                document.cookie = `${response.session_name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
                window.location.reload();
            }
        }
        catch(error) {
            console.log(error);
        }
        finally {
            button.disabled = false;
        }
    }
}
Weblication.registerClass(GUI_Logout);