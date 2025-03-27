/**
 * ZipFolder Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
 */
class GUI_ZipFolder extends GUI_Module
{
    /**
     * the dialog elem
     */
    dialog;
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        this.dialog = this.element('dialog');

        const button = this.element('button.zip-folder');
        button.addEventListener('click', this.openDialog);

        const close = this.element('button.close-button');
        close.addEventListener('click', this.closeDialog);

        const cancel = this.element('button.cancel-button');
        cancel.addEventListener('click', this.closeDialog);

        const download = this.element('button.download-button');
        download.addEventListener('click', this.download);
    }

    openDialog = () =>
    {
        try {
            const path = $Weblication.getModule('fileList').getPath();
            this.element('div.path-info').innerHTML = path;
            this.dialog.showModal();
        } catch(error) {
            console.log(error);
        }
    }

    closeDialog = () =>
    {
        this.dialog.close();
    }

    download = async () =>
    {
        try {
            const path  = $Weblication.getModule('fileList').getPath();
            const response = await this.request('download', {'path': path}, { method: 'POST'});

            console.debug(response);
        }
        catch(error) {
            console.log(error);
        }
        finally {
            console.log('finally');
        }
    }
 }
Weblication.registerClass(GUI_ZipFolder);