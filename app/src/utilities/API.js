import axios from 'axios';

const rootUrl = window.ENV.API_URL;

export default class APIService {
    constructor() {
        this.service = axios.create({
            withCredentials: true
        });
    }

    getEntrySet(count, callback) {
        const cmd = 'creature.getset.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to retrieve '+count+' creature entries');

        this.service.post(url, {'count' : count})
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    getSingleEntry(code, callback) {
        const cmd = 'creature.get.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to retrieve creature entry with code: ' + code);
        
        this.service.post(url, {'code' : code})
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    addEntry(entry, callback) {
        const cmd = 'creature.update.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting to add/update creature entry with code: ' + entry.code);

        if(callback !== undefined) {
            this.service.post(url, entry)
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, entry)
            .catch(error => {console.log(error)});
        }
    }

    removeEntry(entry, callback) {
        const cmd = 'creature.delete.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: to delete creature entry with code: ' + entry.code);

        if(callback !== undefined) {
            this.service.post(url, entry)
            .then(response => response.data)
            .then((data) => callback(data))
            .catch(error => {console.log(error)});
        } else {
            this.service.post(url, entry)
            .catch(error => {console.log(error)});
        }
    }

    markForRemoval(code, callback) {
        const cmd = 'ckey.update.php';
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

    addClick(code, callback) {
        const cmd = 'click.create.php';
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

    tfoLabRequest(username, callback) {
        const cmd = 'curl.tfo.lab.php'
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('API: Attempting lab request for username: ' + username);

        this.service.post(url, {'labname' : username})
        .then(response => response.data)
        .then(data => callback(data))
        .catch(error => {console.log(error)})
    }
}
