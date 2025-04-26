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
class GUI_Logout extends GUI_Module
{
    /**
     * Timer
     */
    timer;

    /**
     * Sessionname
     */
    session_name;

    /**
     * session maxlifetime (seconds)
     */
    session_maxlifetime;

    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        this.session_name = options.session_name;
        this.session_maxlifetime = options.session_maxlifetime;

        const button = this.element('button');
        button.addEventListener('click', this.onLogout);

        if (options.monitorInactivity) {
            this.resetTimer();
            const events = ["mousemove", "keypress", "scroll", "click"];
            events.forEach(event => document.addEventListener(event, this.resetTimer, true));  // useCapture = true
        }
    }

    resetTimer = () =>
    {
        clearTimeout(this.timer);
        this.timer = setTimeout(this.logoutUser, this.session_maxlifetime * 1000);
    }

    logoutUser = () =>
    {
        this.request('doLogout', {}, { method: 'POST'}).then(
            (value) => this.reload(),
            (error) => console.debug(error)
        );
    }

    onLogout = async (evt) => {
        const button = evt.target;
        button.disabled = true;
        try {
            const response = await this.request('doLogout', {}, { method: 'POST'});
            if (response.success) {
                this.reload();
            }
        }
        catch(error) {
            console.log(error);
        }
        finally {
            button.disabled = false;
        }
    }

    reload() {
        document.cookie = `${this.session_name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        const url = new URL(window.location);
        url.search = ''; // remove query params
        window.location.href = url.toString();
    }
}
Weblication.registerClass(GUI_Logout);