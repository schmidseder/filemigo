/**
 * Breadcrumb module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_Breadcrumb extends GUI_Module
{
    /**
     *
     */
    options;

    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        this.options = options;
        //console.debug('GUI_Breadcrumb');
    }
}
Weblication.registerClass(GUI_Breadcrumb);