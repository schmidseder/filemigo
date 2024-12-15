/**
 * This module lists files and directories.
 * 
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_FileResponder extends GUI_Module
{
    rootElement = null;
    options = [];

    /**
     * Initial method of the module
     * 
     * @param {*} options 
     */
    init(options = {})
    {
        this.options = options;
        this.rootElement = this.element();
    }
}
Weblication.registerClass(GUI_FileResponder);