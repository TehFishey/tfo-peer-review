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
                </ul>
            </div>
        );
    }
}