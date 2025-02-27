/**
 * Login Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_Login extends GUI_Module
{
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        const user_field = this.element('input#user');
        user_field.focus();
    }
}
Weblication.registerClass(GUI_Login);