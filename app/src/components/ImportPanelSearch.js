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
                    <div className="import-panel-search-text">Welcome to the peer review network, a place to review other scientists’ creatures and to have your own reviewed as well. After all, peer review is a very important part of the scientific process!<br/><br/> Please start by entering your lab’s name and submitting your creatures. Then scroll down a bit and click away. If you find any adult creatures, please mark them by clicking the red [X] under their portrait. Every click helps! Thank you for doing your part.
                    </div>
                    <div className="import-panel-search-error">
                        {this.props.errorString}
                    </div>
                </div>
            </div>
        )
    }
}
