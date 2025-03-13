/**
 * Session Checker Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_SessionChecker extends GUI_Module
{
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {

    }

    hallo () {
        console.debug('hallo');
    }

    async checkSession()
    {
        try {
            const response = await this.request('checkSession', {}, {method : 'POST'});
            if (response.noSession) {
                const LogoutModul = $Weblication.getModule('logout');
                LogoutModul.reload();
            }
        }
        catch(error) {
            //console.log(error);
            window.location.reload();
        }
        finally {

        }
    }
}
Weblication.registerClass(GUI_SessionChecker);