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
 * This module loads an image into the picture box and scales it to the desired size.
 * 
 * @author Christian Schmidseder
 */
class GUI_PictureBox extends GUI_Module
{
    rootElement = null;
    gallery = null;
    options = [];

    /**
     * Initial method of the module
     * 
     * @param {*} options 
     */
    init(options = {})
    {
        this.options = options;
        this.rootElement = this.element();
        this.gallery = this.getParent();
        this.loadImage(options);
        this.registerEvents();
    }

    /**
     * Various events are registered on the root element of the picture box.
     */
    registerEvents()
    {
        this.rootElement.addEventListener('mouseenter', this.onMouseEnter);
        this.rootElement.addEventListener('mouseleave', this.onMouseLeave);
        this.rootElement.addEventListener('click', this.onClick);
    }

    /**
     * The method loads the image into the picture box and scales it accordingly.
     * 
     * @param {*} options 
     */
    loadImage(options)
    {
        this.checkSession();

        if (options.notFound) {
            this.rootElement.innerHTML = options.imagePath + ' 404 not found';
            return;
        }

        const image = new Image();
        image.src = options.src;
        image.onload = () => {
            // console.debug(`Image ${image.src} (${image.width} x ${image.height})`);
            scaleImageTo(image, Number.MAX_VALUE, options.frameHeight);
            // console.debug(`Image:${image.src} ${image.width} x ${image.height}`);

            this.rootElement.style.width = `${image.width + 20}px`;
            this.rootElement.style.height =`${image.height + 25}px`;
            this.rootElement.appendChild(image);
        };
    }

    /* Events */
    onClick = (evt) => {
        this.checkSession();

        this.gallery.setOverlayMode(true);

        this.rootElement.classList.remove('hover-effect');
        this.rootElement.addEventListener('transitionend', this.afterHoverEffect, { once: true });
    }

    afterHoverEffect = (evt) => {
        this.gallery.showPicture(this.element('img'), this.options.index);
    }

    onMouseEnter = (evt) => {
        if (!this.gallery.isOverlayMode()) {
            this.rootElement.classList.add('hover-effect');
        }
    }

    onMouseLeave = (evt) => {
        if (!this.gallery.isOverlayMode()) {
            this.rootElement.classList.remove('hover-effect');
        }
    }

    checkSession() {
        try {
            const sessionChecker = $Weblication.getModule('sessionChecker');
            sessionChecker.checkSession();
        }
        catch (error) {
            // nothing to here - no session checking
        }
    }
}
Weblication.registerClass(GUI_PictureBox);