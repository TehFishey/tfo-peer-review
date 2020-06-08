import axios from 'axios';
const rootUrl = window.ENV.API_URL;

/**
 * Frontend API handler object; Uses Axios to execute AJAX communications with the internal API.
 * All methods accept callbacks, which execute on response {data} after AJAX promise is returned.
 * API url should be set in public/config.js. This can be changed after buildtime if necessary.
 */
export default class APIService {
    constructor() {
        this.service = axios.create({
            withCredentials: true
        });
    }

    /**
     * Gets up to {count} creature objects from server database. 
     * Response will only contain creatures which haven't been interacted with by user (identified by UUID cookie). 
     * Optionally executes {callback} on response data.
     * @param {number} count 
     * @param {function} callback
     * @example 
     * // Returns [{creature 1 json}, {creature 2 json}]
     * APIService.getCreatureEntities(2, (data)=>{return data.creatures;});
     */
    getCreatureEntries(count, callback) {
        const cmd = './creature/get.php';
        let url = rootUrl + cmd + '?count='+count;

        if(window.ENV.DEBUG) console.log('API: Attempting to retrieve '+count+' creature entries');

        if(callback !== undefined) {
            this.service.get(url)
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.get(url)
            .catch(error => {console.log(error)});
        }
    }

    /**
     * Tells server to add creatures with ids={codes} to database.
     * Added creatures must already exist in server cache (fetched during same session).
     * Optionally executes {callback} on response data.
     * @param {Array<string>} codes 
     * @param {function} callback
     * @example 
     * // Returns "(201) Creatures were created."
     * APIService.addCreatureEntries(['zzzzz', 'yyyyy'], (data)=>{return data.message;});
     */
    addCreatureEntries(codes, callback) {
        const cmd = './creature/create.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to add/update creature entries with codes: ' + codes.toString());

        if(callback !== undefined) {
            this.service.post(url, {'codes' : codes})
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, {'codes' : codes})
            .catch(error => {console.log(error)});
        }
    }

    /**
     * Tells server to delete creatures with ids={codes} from database.
     * Removed creatures must already exist in server cache (fetched during same session).
     * Optionally executes {callback} on response data.
     * @param {Array<string>} codes 
     * @param {function} callback
     * @example 
     * // Returns "(201) Creatures were deleted."
     * APIService.removeCreatureEntries(['zzzzz', 'yyyyy'], (data)=>{return data.message;});
     */
    removeCreatureEntries(codes, callback) {
        const cmd = './creature/delete.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to delete creature entries with codes: ' + codes.toString());

        if(callback !== undefined) {
            this.service.post(url, {'codes' : codes})
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, {'codes' : codes})
            .catch(error => {console.log(error)});
        }
    }

    /**
     * For each code in {codes}, tests if a corresponding creature exists in server database.
     * Optionally executes {callback} on response data.
     * @param {Array<string>} codes 
     * @param {function} callback
     * @example 
     * // Returns [['aaaaa', true], ['bbbbb', false]]
     * APIService.checkCreatureEntries(['aaaaa', 'bbbbb'], (data)=>{return data.exists;});
     */
    checkCreatureEntries(codes, callback) {
        const cmd = './creature/test.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Checking server for existing entries with codes: ' + codes.toString());

        if(callback !== undefined) {
            this.service.post(url, {'codes' : codes})
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, {'codes' : codes})
            .catch(error => {console.log(error)});
        }
    }

    /**
     * Marks creature with {code} in server database as invalid (dead/stunted/adult).
     * Optionally executes {callback} on response data.
     * @param {string} code
     * @param {function} callback
     * @example 
     * // Returns "(201) Creature was flagged for update."
     * APIService.addCreatureFla('xxxxx', (data)=>{return data.message;});
     */
    addCreatureFlag(code, callback) {
        const cmd = './flag/create.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to add ' + code + ' to markedkeys db table');

        if(callback !== undefined) {
            this.service.post(url, {'code' : code})
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, {'code' : code})
            .catch(error => {console.log(error)});
        }
    }

    /**
     * Logs interaction event between user's UUID (from cookie) and creature with {code} in server database.
     * Optionally executes {callback} on response data.
     * @param {string} code
     * @param {function} callback
     * @example 
     * // Returns "(201) Creature click was logged."
     * APIService.addCreatureClick('xxxxx', (data)=>{return data.message;});
     */
    addCreatureClick(code, callback) {
        const cmd = './click/create.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: updating server click tracker for code ' + code);

        if(callback !== undefined) {
            this.service.post(url, {'code' : code})
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, {'code' : code})
            .catch(error => {console.log(error)});
        }
    }

    /**
     * Fetches all growing creatures from {labname}'s lab on TFO and returns creature objects.
     * Also caches creature data on server for add/remove operations. Cache is tied to user session/ip.
     * Optionally executes {callback} on response data.
     * @param {string} code
     * @param {function} callback
     * @example 
     * // Returns {TFO cURL json}.
     * // See TFO API docs.
     * APIService.fetchByLabname('TehFishey', (data)=>{return data;});
     */
    fetchByLabname(username, callback) {
        const cmd = './creature/fetch.php';
        let url = rootUrl + cmd + '?labname='+username;

        if(window.ENV.DEBUG) console.log('API: Attempting lab request for username: ' + username);

        if(callback !== undefined) {
            this.service.get(url)
            .then(response => response.data)
            .then(data => callback(data))
            .catch(error => {console.log(error)})
        } else {
            this.service.get(url)
            .then(response => response.data)
            .catch(error => {console.log(error)})
        }
    }

    /**
     * Fetches general user activity metrics/data from server database.
     * Optionally executes {callback} on response data.
     * @param {function} callback
     * @example 
     * // Returns {"weekly": {"uniques": 1,"clicks": 273,"uniqueCreatures": 30,"uniqueLabs": 58},"allTime": {"clicks": 273,"uniqueCreatures": 30,"uniqueLabs": 58}}
     * APIService.getLogData((data)=>{return data});
     */
    getLogData(callback) {
        const cmd = './log/get.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to get server log data.');

        if(callback !== undefined) {
            this.service.get(url)
            .then(response => response.data)
            .then(data => callback(data))
            .catch(error => {console.log(error)})
        } else {
            this.service.get(url)
            .then(response => response.data)
            .catch(error => {console.log(error)})
        }
    }
}
