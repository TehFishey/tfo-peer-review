import React from 'react';
import { checkConsentCookie, createConsentCookie } from '../utilities/Cookies';
import './cookie-consent.css';
import './cookie-consent-mobile.css';

export default class CookieConsent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            visible : false
        }

        //Bind handler methods to class for easier html scripting.
        this.handleAccept = this.handleAccept.bind(this);
    }

    handleAccept() {
        createConsentCookie();
        this.setState({visible : false});
        window.location.reload();
    }

    componentDidMount() {
        this.setState({visible : !checkConsentCookie()});
    }

    render() {
        if (!this.state.visible) {
            return null;
        } else {
            return(
                <div className='cookie-consent-bar'>
                    <div className='cookie-consent-text'>
                        This website requires cookies in order to function properly - Nothing works without them!<br/> 
                        (Details on how we use cookies can be found in the privacy policy section.)
                    </div>
                    <button className='cookie-consent-button' onClick={this.handleAccept}>Accept Cookies</button>
                </div>
            )
        }
    }
}