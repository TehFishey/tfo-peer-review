import axios from 'axios';

const rootUrl = 'https://xampp.test.environment/apis/external';

export default class ExternalAPIService {
    constructor() {
        this.service = axios.create();
    }

    labRequest(username, callback) {
        const cmdUrl = '/api.php'
        let url = rootUrl + cmdUrl;

        console.log('ExternalAPI: Attempting lab request for username: ' + username)

        this.service.post(url, {action : 'lab', value : username})
        .then(response => response.data)
        .then(data => callback(data))
        .catch(error => {console.log(error)})
    }
}