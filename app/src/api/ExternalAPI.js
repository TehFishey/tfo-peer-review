import axios from 'axios';

const rootUrl = window.ENV.API_URL+'external/';

export default class ExternalAPIService {
    constructor() {
        this.service = axios.create();
    }

    labRequest(username, callback) {
        const cmd = 'lab.get.php'
        let url = rootUrl + cmd;

        if(window.ENV.DEBUG) console.log('ExternalAPI: Attempting lab request for username: ' + username);

        this.service.post(url, {action : 'lab', value : username})
        .then(response => response.data)
        .then(data => callback(data))
        .catch(error => {console.log(error)})
    }
}