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
 * @module gallery-lib
 * @author Christian Schmidseder
 *
 * Simple module for scaling images.
 *
 */

/**
 * Rect is a class that represents a rectangle with width and height.
 */
class Rect {
    #width = 0;
    #height = 0;
    #ratio = 0;
    #isLandscape = null;

    /**
     * The contructor of the class.
     *
     * @param width
     * @param height
     */
    constructor(width, height) {
        this.width  = width;
        this.height = height;
        this.ratio  = width / height;
        this.isLandscape = (width > height);
    }

    /**
     * This method scales this rectangle proportionally into a frame.
     *
     * @param {Rect} frame The frame is also a rectangle into which the current rectangle is scaled.
     * @returns {Rect} A scaled rectangle that fits exactly into the frame is returned.
     */
    scaleTo(frame) {
        let newWidth = 0;
        let newHeight = 0;

        // console.debug(`> This: ${this.toString()} > Frame: ${frame.toString()}`);

        if (frame.ratio >= this.ratio) {
            newWidth = this.width * (frame.height / this.height);
            newHeight = frame.height;
        } else {
            newWidth = frame.width;
            newHeight = this.height * (frame.width / this.width);
        }
        return new Rect(newWidth, newHeight);
    }

    /** getter and setter **/
    get width() { return this.#width; }
    set width(width) { this.#width = width; }
    get height(){ return this.#height; }
    set height(height) { this.#height = height; }
    get ratio() { return this.#ratio; }
    set ratio(ratio) { this.#ratio = ratio;}
    get isLandscape() { return this.#isLandscape; }
    set isLandscape(isLandscape) { this.#isLandscape = isLandscape;}


    toString() {
        return `Rect(Width: ${this.width}, Height: ${this.height}, Ratio: ${this.ratio}, Format: ${this.isLandscape ? 'landscape' : 'portrait'})`;
    }
}

function checkParameter(image, width, height) {
    if (!(image instanceof Image)) {
        throw new Error('The Parameter must be a instance of Image!');
    }
    if (isNaN(width) || width <= 0) {
        throw new Error('The width must be numeric and greater than zero');
    }
    if (isNaN(height) || height <= 0) {
        throw new Error('The height must be numeric and greater than zero');
    }
}

/**
 * This function scales an image proportionally to the specified size (width, height)
 *
 * @param {image} image object
 * @param {string} width to scale to
 * @param {string} height to scale to
 */
function scaleImageTo(image, width, height) {
    checkParameter(image, width, height);

    // console.debug('old image size: ', image.width, image.height);
    const originalSize = new Rect(image.width, image.height);
    //console.debug(originalSize);
    const frame = new Rect(width, height);

    const newSize = originalSize.scaleTo(frame);
    image.width = newSize.width;
    image.height = newSize.height;
}

/**
 * 
 * @param {image}} image 
 * @param {number} width 
 * @param {number} height 
 * @returns {Rect}
 */
function getScaledImageDimensions(image, width, height) {
    checkParameter(image, width, height);
    return new Rect(image.width, image.height).scaleTo(new Rect(width, height));
}