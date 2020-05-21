import axios from 'axios';

const rootUrl = window.ENV.API_URL+'internal/';

export default class ExternalAPIService {
    constructor() {
        this.service = axios.create();
    }

    getEntrySet(callback) {
        const cmd = 'creature.getset.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: Attempting to retrieve all creature entries');

        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    getSingleEntry(code, callback) {
        const cmd = 'creature.get.php?code='+code;
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: Attempting to retrieve creature entry with code: ' + code);
        
        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    addEntry(entry, callback) {
        const cmd = 'creature.update.php';
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: Attempting to add/update creature entry with code: ' + entry.code);

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

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: to delete creature entry with code: ' + entry.code);

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

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: Attempting to add ' + code + ' to markedkeys db table');

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

        if(window.ENV.DEBUG) console.log('InternalAPI AJAX: updating server click tracker for code ' + code);

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
}
