/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */
class GUI_Breadcrumb extends GUI_Module
{
    /**
     *
     */
    options;

    /**
     * Initial method of the module
     *
     * @param {*} options
     */
    init(options = {})
    {
        this.options = options;
        //console.debug('GUI_Breadcrumb');
    }
}
Weblication.registerClass(GUI_Breadcrumb);