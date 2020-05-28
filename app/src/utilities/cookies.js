import cookies from 'js-cookie';
import {v4 as uuid} from 'uuid';

/**
 * Assigns a 36-character UUID cookie to user if one does not already exist. Cookie expires in 1.5 days.
 * If {update} is true, resets existing UUID cookie's expiration date as well.
 * @param {boolean} update 
 */
export function checkUUID(update) {  
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
}
