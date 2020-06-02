import React from 'react';

/**
 * Sub-component of ImportPanel; display is mutually exclusive with ImportPanelSelect. Provides user
 * with an input form for searching for TFO labs. Also displays error messages after failed lab
 * searches, and a text box which briefly explains the site's features.
 * 
 * @property {string} errorString: Import error code string to display (if any)
 * @property {function} onSubmit: Function to execute when user submits a lab search.
 */
export default class ImportPanelSearch extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name : ''
        };

        //Bind handler methods to class for easier html scripting.
        this.handleNameChange = this.handleNameChange.bind(this);
        this.handleNameEnterKey = this.handleNameEnterKey.bind(this);
        this.handleNameSubmit = this.handleNameSubmit.bind(this);
    }

    /**
     * Updates the name state. Intended handler for text input element.
     * @param {event} event 
     */
    handleNameChange(event) {
        this.setState({name : event.target.value})
    }
    
    /**
     * Calls onSubmit event when enter key is pressed. Intended handler for text input element.
     * @param {event} event 
     */
    handleNameEnterKey(event) {
        var code = event.keyCode || event.which;
        //13 is the enter keycode
        if (code === 13) { 
            this.handleNameSubmit();
        }
    }

    /**
     * Calls onSubmit event. Intended handler for button element.
     */
    handleNameSubmit() {
        this.props.onSubmit(this.state.name);
        this.setState({name : ''});
    }

    render () {
        return (
                <div className="import-panel-search">
                    <div className="import-panel-controls">
                        <div className="import-panel-label">Lab Name:</div>
                        <div><input 
                            className="import-panel-text-input"
                            type="text" 
                            value={this.state.name} 
                            onChange={this.handleNameChange}
                            onKeyPress={this.handleNameEnterKey}
                        /></div>
                        <button className='import-panel-button'
                            onClick={this.handleNameSubmit}>Open Lab</button>
                    </div>
                    <div className="import-panel-blurb">Welcome to the peer review network, a place for scientists to examine each other's growing creatures and help with their development. After all, peer review is a very important part of the scientific process!<br/><br/> Please start by entering your lab’s name and submitting your creatures. Then scroll down a bit and click away. If you find any adult creatures, please mark them by clicking the red [X] under their portrait. Every click helps! Thank you for doing your part.
                    </div>
                    <div className="import-panel-search-error">
                        {this.props.errorString}
                    </div>
                </div>
        )
    }
}
