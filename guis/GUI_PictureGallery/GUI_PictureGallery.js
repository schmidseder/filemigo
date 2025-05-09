/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */
class GUI_PictureGallery extends GUI_Module
{
    overlayMode = false;
    originalPicture = null;
    shownPicture = null;
    resizeTimeout = false;
    resizeDelay = 250;
    arrowThreshold = 0.35; // Schwellenwert fürs Pfeile einblenden
    files = [];
    currentIndex = 0;

    init(options = {})
    {
        //console.debug('GUI_PictureGallery...', options);
        this.files = options.files;

        // original picture width and height remain the same 
        this.originalPicture = new Image();

        // shown picture weight and width are scaled to the window width
        // in the onloadShownPicture Event.
        this.shownPicture = new Image();
        this.shownPicture.onload = this.onloadShownPicture;

        // register events
        this.element('span.close-btn').addEventListener('click', this.onCloseBtnClick);
        window.addEventListener("resize", this.onResize);
        this.element('div.overlay').addEventListener('mousemove', this.onMouseMove);
        this.element('div.next-arrow').addEventListener('click', this.onNextClick);
        this.element('div.prev-arrow').addEventListener('click', this.onPrevClick);
    }

    showPicture(picture, currentIndex=0)
    {
        this.currentIndex = currentIndex;
        this.toggleArrow(this.element('div.prev-arrow'), false);
        this.toggleArrow(this.element('div.next-arrow'), false);

        this.element('div.overlay').style.display = 'block';
        this.element('div.overlay + span.close-btn').style.display = 'block';

        // set original picture (width, height) - need in scaleShownPictureToWindow
        this.originalPicture.src = picture.src;

        // set shown picture (width, height)
        this.shownPicture.src = picture.src;

        if (this.shownPicture.complete) {
            this.embedIntoPage(this.shownPicture);
        }
    }

    embedIntoPage(img) {
        this.element('div.overlay').appendChild(this.shownPicture);
        this.scaleShownPictureToWindow();
    }

    proceed(backwards=false) {
        const futureIndex = this.getFutureIndex(backwards);
        this.currentIndex = futureIndex;
        const newPictureSrc = this.files[futureIndex];

        this.originalPicture.src = newPictureSrc;
        this.shownPicture.src = newPictureSrc;
        if (this.shownPicture.complete) {
            this.embedIntoPage(this.shownPicture);
        }
    }

    getFutureIndex(backwards=false) {
        const offset = backwards ? -1 : 1;
        return (this.currentIndex + offset + this.files.length) % this.files.length;
    }

    hidePicture() {
        this.element('div.overlay').removeChild(this.element('div.overlay > img'));
        this.element('div.overlay + span.close-btn').style.display = 'none';
        this.element('div.overlay').style.display = 'none';
    }

    setOverlayMode(overlayMode= true) {
        this.overlayMode = overlayMode;
    }

    isOverlayMode() {
        return this.overlayMode;
    }

    scaleShownPictureToWindow() {
        const {width, height} = getScaledImageDimensions(this.originalPicture, window.innerWidth, window.innerHeight);
        this.shownPicture.width = width;
        this.shownPicture.height = height;
    }

    toggleArrow(arrow, force) {
        arrow.style.visibility = (force ?? arrow.style.visibility === 'hidden') ? 'visible' : 'hidden';
    }

    /* Events  */
    onCloseBtnClick = (evt) => {
        this.setOverlayMode(false);
        this.hidePicture();
    }

    onResize = (evt) => {
        if (this.isOverlayMode()) {
            // clear the timeout
            clearTimeout(this.resizeTimeout);
            // start timing for event "completion"
            this.resizeTimeout = setTimeout(() => {
                this.scaleShownPictureToWindow();
            }, this.resizeDelay);
        }
    }

    onMouseMove = (evt) => {
        // show arrows
        const overlay = evt.currentTarget;
        const overlayWidth = overlay.offsetWidth;
        const mouseX = evt.clientX;
        
        const prevArrow = this.element('div.prev-arrow');
        const nextArrow = this.element('div.next-arrow');
        
        const showPrev = mouseX < overlayWidth * this.arrowThreshold;
        const showNext = mouseX > overlayWidth * (1 - this.arrowThreshold);
        
        this.toggleArrow(prevArrow, showPrev);
        this.toggleArrow(nextArrow, showNext);    
    }
    
    onloadShownPicture = (evt) => {
        this.embedIntoPage(this.shownPicture);
    }

    onNextClick = (evt) => {
        this.proceed();
    }

    onPrevClick = (evt) => {
        this.proceed(true);
    }   
}
Weblication.registerClass(GUI_PictureGallery);