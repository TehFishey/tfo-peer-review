import React from 'react';

export default class ImportPanelSearch extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name : ''
        };

        this.handleNameChange = this.handleNameChange.bind(this);
        this.handleNameEnterKey = this.handleNameEnterKey.bind(this);
        this.handleNameSubmit = this.handleNameSubmit.bind(this);
    }

    handleNameChange(event) {
        this.setState({name : event.target.value})
    }
    
    handleNameEnterKey(event) {
        var code = event.keyCode || event.which;
        //13 is the enter keycode
        if (code === 13) { 
            this.handleNameSubmit();
        }
    }

    handleNameSubmit() {
        this.props.onSubmit(this.state.name);
        this.setState({name : ''});
    }

    render () {
        return (
            <div className="import-panel">
                <div className="import-panel-search">
                    <div className="import-panel-controls">
                        <div style={{textAlign : 'left'}}>Lab Name:</div>
                        <div><input 
                            type="text" 
                            style={{width: '150'}}
                            value={this.state.name} 
                            onChange={this.handleNameChange}
                            onKeyPress={this.handleNameEnterKey}
                        /></div>
                        <button onClick={this.handleNameSubmit}>Open Lab</button>
                    </div>
                    <div className="import-panel-search-text">
                    </div>
                    <div className="import-panel-search-error">
                        {this.props.isError ? 'ERROR: Lab not found!' : ''}
                    </div>
                </div>
            </div>
        )
    }
}
