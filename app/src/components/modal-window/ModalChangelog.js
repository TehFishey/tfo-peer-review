import React from 'react';

/**
 * Wrapper class for Help text. Renders simple static JSX.
 */
export default class Help extends React.Component {
    render() {
        return (
            <div>
                <h2>Version 1.0.1</h2>
                <ul>
                    <li> Added a changelog. (how meta)</li>
                    <li> Added a button for flagging currently opened creature as adult.</li>
                    <li> Proofread Privacy Policy, Disclaimer, and User Guide (thanks Shark!)</li>
                    <li> Adjusted spacing between creature tiles to make them a little more compact.</li>
                    <li> Updated click rollover timing. Previously, clicks were kept logged on the site for 24 hours. Clicks are now instead
                        reset by cron script at 6am EST daily (in keeping with how/when TFO resets its own click counters.)</li>
                    <li> Adjusted server metric collection database and behaviors. Server will now log records of unique creature codes
                        and labnames imported from TFO.</li>
                    <li> Adjusted frontend metrics widget to display new log data. They should be more intuitive/accurate for users, now.</li>
                    <li> Changed privacy policy to accurately represent new state of server logging.</li>
                </ul>
            </div>
        );
    }
}