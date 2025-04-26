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
class GUI_Login extends GUI_Module
{
    /**
     * csrf_token
     */
    csrf_token;

    /**
     * the form
     */
    form;
    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        const user_field = this.element('input#user');
        user_field.focus();

        this.csrf_token = options.csrf_token;
        this.form = this.element('form');
        this.form.addEventListener('submit', this.submit);
    }

    submit = (evt) =>
    {
        let hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'csrf_token';
        hiddenField.id =  'csrf_token';
        hiddenField.value = this.csrf_token;
        this.form.appendChild(hiddenField);

        // evt.preventDefault();
    }
}
Weblication.registerClass(GUI_Login);