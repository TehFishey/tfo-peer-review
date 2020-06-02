import React from 'react';

/**
 * Component which displays creature pages in an iFrame, as part of the "lower" window of the
 * tfo-peer-review Stage.
 * 
 * @property {string} currentView: URL of current creature code to display
 */
export default class ViewPanel extends React.Component {

    render () {
        return (
            <div className="view-panel">
                <iframe className="view-panel-iframe" src={this.props.currentView} title="CritterView"></iframe> 
            </div>
        )
    }
}