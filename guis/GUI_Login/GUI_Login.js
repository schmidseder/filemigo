/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
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