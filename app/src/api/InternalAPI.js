import axios from 'axios';

const rootUrl = 'https://xampp.test.environment/apis/internal';

export default class ExternalAPIService {
    constructor() {
        this.service = axios.create();
    }

    getAllEntries(callback) {
        const cmdUrl = '/read_all.php';
        let url = rootUrl + cmdUrl;

        console.log('InternalAPI: Attempting read_all AJAX request');

        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    getSingleEntry(code, callback) {
        const cmdUrl = '/read_code.php?code='+code;
        let url = rootUrl + cmdUrl;

        console.log('InternalAPI: Attempting read_code AJAX request with code: ' + code);
        
        this.service.get(url)
        .then(response => response.data)
        .then((data) => callback(data))
        .catch(error => {console.log(error)});
    }

    addEntry(entry, callback) {
        const cmdUrl = '/create.php';
        let url = rootUrl + cmdUrl;

        console.log('InternalAPI: Attempting add AJAX request for object with code: ' + entry.code);
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
        const cmdUrl = '/delete.php';
        let url = rootUrl + cmdUrl;

        console.log('InternalAPI: Attempting remove AJAX request for object with code: ' + entry.code);
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
