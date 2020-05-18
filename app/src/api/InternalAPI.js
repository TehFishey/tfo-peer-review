import axios from 'axios';

const rootUrl = 'https://xampp.test.environment/api/internal/';

export default class ExternalAPIService {
    constructor() {
        this.service = axios.create();
    }

    getAllEntries(callback) {
        const cmd = 'creature.getall.php';
        let url = rootUrl + cmd;

        console.log('InternalAPI: Attempting getall AJAX request');

        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    getSingleEntry(code, callback) {
        const cmd = 'creature.get?code='+code;
        let url = rootUrl + cmd;

        console.log('InternalAPI: Attempting get AJAX request with code: ' + code);
        
        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    addEntry(entry, callback) {
        const cmd = 'creature.update.php';
        let url = rootUrl + cmd;

        console.log('InternalAPI: Attempting update AJAX request for object with code: ' + entry.code);
        console.log(entry);
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

        console.log('InternalAPI: Attempting delete AJAX request for object with code: ' + entry.code);
        console.log(entry);
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
}
