import React from 'react';

/**
 * Wrapper class for PrivacyPolicy text. Renders simple static JSX.
 */
export default class PrivacyPolicy extends React.Component {
    render() {
        return (
            <div>
                <p>TFO Peer Review puts a high priority on protecting users' privacy.
                This Privacy Policy details the types of information 
                collected by TFO Peer Review and explains how that information is used.</p>

                <p>If you have additional questions or require more information about 
                our Privacy Policy, do not hesitate to contact us.</p>

                <h2>Log Files and Metrics</h2>

                <p>TFO Peer Review is hosted by a webserver, which follows the industry-standard 
                practice of logging visitors when they visit the website. Most hosting companies do 
                this as a part of hosting services' internal analytics. The information collected by 
                these logs include the visitor's internet protocol (IP) address, browser type, and Internet 
                Service Provider (ISP), a date and time stamp, referring/exit pages, and 
                possibly the number of clicks. These are not linked to any information that 
                is personally identifiable. The purpose of this logging is to provide information needed to
                analyze trends, administer the site, track users' movement within the site, and 
                gather demographic information.</p>

                <p>TFO Peer Review additionally collects a limited amount of metric data 
                regarding user interactions with the site, to help measure site use 
                and performance. These metrics include counts for each API operation performed by
                the server each week, as well as logs of: user IP addresses, creature codes added to 
                the site, and lab names sucessfully queried by the site. In the interest of privacy,
                however, records tied to user IP addresses are NOT retained for longer than one week.</p>

                <h2>Cookies Policy</h2>

                <p>Like most modern websites, TFO Peer Review uses "cookies". These cookies 
                are used to associate users with creatures that they have already clicked, 
                to prevent the same creature from appearing to a user twice over 24 hours. 
                We also use cookies to remember which users have consented to our use of cookies
                (which is pretty ironic, isn't it?) The cookies created by this site are NOT used for tracking, 
                logging, or any purpose other than the two stated here.</p>

                <p>Web pages opened through TFO Peer Review may also attempt to read, write, or use 
                cookies to influence their own behaviors, (such as TFO attempting to read a 
                user's login cookie to associate clicks with a specific user account). You 
                may choose to disable cookies - or cross-site cookie reading - through your 
                individual browser options, with the understanding that doing so will limit 
                the functionality of this website. You can find more detailed information 
                about cookie management for specific web browsers on the browsers' respective 
                websites.</p>
                    
                <p>For more general information on cookies, please read the "What Are Cookies" 
                article on the <a href="https://www.cookieconsent.com/what-are-cookies/">
                Cookie Consent website</a>.</p>
                    
                <h2>Third Party Privacy Policies</h2>
                    
                <p>TFO Peer Review makes use of an Inline-Frame (or "IFrame") component as part 
                of its normal operation. This component allows users to open external web pages 
                (such as creature pages on TFO) without opening a new tab or window. Depending 
                on a user's browser settings, web pages opened through this IFrame may serve them 
                cookies or JavaScript scripts, track Web Beacons, track or log the user's IP,
                or apply other such technologies. TFO Peer Review's Privacy Policy does 
                not apply to any third-party websites, and you are advised to consult the respective 
                policies of third-party services for more information. (You can find The Final Outpost's privacy 
                policy on their <a href="https://finaloutpost.net/terms">Terms and Conditions</a> page.)</p>
                    
                <h2>Children Under 13 Years of Age</h2>
                    
                <p>TFO Peer Review does not knowingly collect any Personal Identifiable Information from 
                children under the age of 13. If you think that your child provided this kind of information 
                to our website, we strongly encourage you to contact us immediately, and we will do our best 
                to promptly remove such information from our records.</p>
                    
                <h2>Limitations</h2>
                    
                <p>This Privacy Policy applies only to the TFO Peer Review website at www.tfopeerreview.click, 
                and is only valid regarding the information that is shared by or collected from visitors of that
                site. This policy is not applicable to any information collected offline or via channels other 
                than the TFO Peer Review webpage.</p>
                    
                <h2>Consent</h2>
                    
                <p>By using our website, you hereby consent to our Privacy Policy and 
                agree to its Terms and Conditions.</p>
                    
                <p>This Privacy Policy was created with the help of the <a href="https://www.privacypolicygenerator.org">
                Free Privacy Policy Generator</a> and the <a href="https://www.privacypolicyonline.com/privacy-policy-generator/">
                Privacy Policy Generator Online</a>. This policy was last updated on June 8, 2020</p>
            </div>
        );
    }
}