/**
 * This module lists files and directories.
 * 
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_FileList extends GUI_Module
{
    rootElement = null;
    options = [];
    path;

    /**
     * Initial method of the module
     * 
     * @param {*} options 
     */
    init(options = {})
    {
        this.options = options;
        this.rootElement = this.element();
        this.path = this.options.path;
    }

    getPath() {
        return this.path;
    }
}
Weblication.registerClass(GUI_FileList);