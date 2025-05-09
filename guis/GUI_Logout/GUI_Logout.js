/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
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