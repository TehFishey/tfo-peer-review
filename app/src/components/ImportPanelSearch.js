import React from 'react';

export default class ImportPanelSearch extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            name : ''
        };

        this.handleNameChange = this.handleNameChange.bind(this);
        this.handleNameSubmit = this.handleNameSubmit.bind(this);
    }

    handleNameChange(event) {
        this.setState({name : event.target.value})
    }

    handleNameSubmit(event) {
        this.props.onSubmit(this.state.name);
        this.setState({name : ''});
    }

    render () {
        return (
            <div className="import-panel">
                <div className="import-panel-search">
                    <label>Lab Name:  
                        <input type="text" value={this.state.name} onChange={this.handleNameChange}/>
                    </label>
                    <button text="Search" onClick={this.handleNameSubmit}/>
                </div>
            </div>
        )
    }
}
