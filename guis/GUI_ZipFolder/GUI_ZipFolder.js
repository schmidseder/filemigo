/**
 * Copyright (C) 2025 schmidseder.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * ZipFolder Module
 *
 * @author Christian Schmidseder
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
            else {
                this.element('div.zip-link').innerHTML = response.message;
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