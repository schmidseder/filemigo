"use strict"

function isStrict()
{
    if (!this) return true;
    return false;
}
console.debug('Hello from app.js');
console.debug("Strict Mode ? " + isStrict());

