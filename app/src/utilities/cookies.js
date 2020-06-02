import cookies from 'js-cookie';
import {v4 as uuid} from 'uuid';

/**
 * Assigns a 36-character UUID cookie to user if one does not already exist. Cookie expires in 1.5 days.
 * Can optionally reset existing UUID cookie's expiration date, when possible.
 * @param {boolean} update whether or not to update existing cookie's date.
 */
export function checkUUID(update) {  
    
    let consent = cookies.get('tfopr-consent');

    if(consent) {
        if(window.ENV.DEBUG) console.log('Controller: checking for valid-ish UUID cookie');

        let cookieID = cookies.get('tfopr-uuid');

        if(!(cookieID && cookieID.length === 36)) {
            if(window.ENV.DEBUG) console.log(cookieID);
            if(window.ENV.DEBUG) console.log('Controller: Invalid cookie, creating...');

            cookies.set('tfopr-uuid', uuid(), {expires: 1.5})
        } else if(update) {
            cookies.set('tfopr-uuid', cookieID, {expires: 1.5})
            if(window.ENV.DEBUG) console.log('Controller: Cookie validated! Updating age.');
        } else {
            if(window.ENV.DEBUG) console.log('Controller: Cookie validated!');
        }
    } else {
        if(window.ENV.DEBUG) console.log('Controller: user has no consent cookie! Exiting...');
    }
}

/**
 * Assigns a consent cookie to user. Cookie expires in 365 days.
 */
export function createConsentCookie() {  
    cookies.set('tfopr-consent', 'true', {expires: 365})
}

/**
 * Checks if user has a consent cookie.
 * @returns {boolean} consent cookie exists.
 */
export function checkConsentCookie() {  
    if(window.ENV.DEBUG) console.log('Controller: checking for valid-ish UUID cookie');
    let cookieID = cookies.get('tfopr-consent');

    return cookieID && cookieID === 'true';
}
