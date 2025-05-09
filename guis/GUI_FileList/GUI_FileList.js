/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */
class GUI_FileList extends GUI_Module
{
    rootElement = null;
    options = [];
    path;

    /**
     * Initial method of the module
     * 
     * @param {*} options 
     */
    init(options = {})
    {
        this.options = options;
        this.rootElement = this.element();
        this.path = this.options.path;
    }

    getPath() {
        return this.path;
    }
}
Weblication.registerClass(GUI_FileList);