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
            this.element('div.zip-link').innerHTML = '';
            this.element('span.loader').style.display = 'none';
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
            this.element('div.zip-link').innerHTML = '';
            this.element('span.loader').style.display = 'block';

            const path  = $Weblication.getModule('fileList').getPath();
            const response = await this.request('download', {'path': path}, { method: 'POST'});

            console.debug(response);
            if (response.success) {
                const zipUrl = new Url();
                zipUrl.setScript(response.zipUrl);
                zipUrl.setParam('zipDownload', true);
                // console.debug(zipUrl.getUrl());

                const link = document.createElement('a');
                link.href = zipUrl.getUrl();
                link.textContent = response.zipName;
                // link.target = '_blank';
                link.rel = 'noopener noreferrer';

                this.element('div.zip-link').appendChild(link);
            }
        }
        catch(error) {
            console.log(error);
        }
        finally {
            this.element('span.loader').style.display = 'none';
        }
    }
 }
Weblication.registerClass(GUI_ZipFolder);