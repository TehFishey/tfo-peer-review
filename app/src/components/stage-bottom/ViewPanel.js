import React from 'react';

/**
 * Component which displays creature pages in an iFrame, as part of the "lower" window of the
 * tfo-peer-review Stage.
 * 
 * @property {string} currentView: Creature code of current creature to display.
 * @property {function} onCreatureFlag: Function to execute when currently-viewed creature is marked.
 */
export default class ViewPanel extends React.Component {
    render () {
        return (
            <div className="view-panel">
                <button 
                    className="view-panel-remove-button" 
                    onClick={()=>{this.props.onCreatureFlag(this.props.currentView)}}>
                        Mark Creature as Adult [X]
                </button>
                <iframe 
                    className="view-panel-iframe" 
                    src={`https://finaloutpost.net/view/${this.props.currentView}#main`} 
                    title="IFrame"
                />
            </div>
        )
    }
}