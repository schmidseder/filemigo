/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */

/**
 * Session Checker Module
 *
 * @author Christian Schmidseder
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