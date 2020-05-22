import cookies from 'js-cookie';
import {v4 as uuid} from 'uuid';

export default function checkUUID(update) {  
    if(window.ENV.DEBUG) console.log('Controller: checking for valid-ish UUID cookie');
    let cookieID = cookies.get('tfopr-uuid');

    if(!(cookieID && cookieID.length === 36)) {
        if(window.ENV.DEBUG) console.log(cookieID);
        if(window.ENV.DEBUG) console.log('Controller: Invalid cookie, creating...');

        cookies.set('tfopr-uuid', uuid(), {expires: 432000})
    } else if(update) {
        cookies.set('tfopr-uuid', cookieID, {expires: 432000})
        if(window.ENV.DEBUG) console.log('Controller: Cookie validated! Updating age.');
    } else {
        if(window.ENV.DEBUG) console.log('Controller: Cookie validated! Updating age.');
    }
}
