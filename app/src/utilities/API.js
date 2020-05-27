import axios from 'axios';

const rootUrl = window.ENV.API_URL;

export default class APIService {
    constructor() {
        this.service = axios.create({
            withCredentials: true
        });
    }

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
}
