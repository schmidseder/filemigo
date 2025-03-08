/**
 * Logout Module
 *
 * @author Christian Schmidseder <c.schmidseder@gmx.de>
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
     * InactiveTime
     */
    inactiveTime = 1;

    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        this.session_name = options.session_name;

        const button = this.element('button');
        button.addEventListener('click', this.onLogout);

        if (options.monitorInactivity) {
            console.debug('Überwache Inaktivität....');
            this.resetTimer();
            const events = ["mousemove", "keypress", "scroll", "click"];
            events.forEach(event => document.addEventListener(event, this.resetTimer, true));  // useCapture = true
        }
    }

    resetTimer = () =>
    {
        // console.debug('timer reset');
        clearTimeout(this.timer);
        this.timer = setTimeout(this.logoutUser, this.inactiveTime * 60 * 1000);
    }

    logoutUser = () =>
    {
        console.debug('logout user');
        //alert("Du wurdest wegen Inaktivität ausgeloggt");
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
        window.location.reload();
    }
}
Weblication.registerClass(GUI_Logout);