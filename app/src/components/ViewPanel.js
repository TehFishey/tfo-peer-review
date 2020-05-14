import React from 'react';

export default class ViewPanel extends React.Component {
    render () {
        return (
            <div className="view-panel">
                 <iframe className="view-panel-iframe" src={this.props.currentView} title="CritterView"></iframe> 
            </div>
        )
    }
}