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