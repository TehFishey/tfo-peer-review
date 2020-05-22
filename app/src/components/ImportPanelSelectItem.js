import React from 'react';

export default class ImportPanelSelectItem extends React.Component {
    render () {
        return (
            <div className="import-panel-item" 
                onClick={() => this.props.onCheck(this.props.code, !this.props.checked)} >
                <div className="import-creature-image"
                    style={{
                        backgroundImage : 'url('+this.props.src+')',
                        backgroundRepeat : 'no-repeat', 
                        backgroundPosition : '50% 50%'
                    }}/>
                <div>
                    <label>{this.props.code}</label>
                    <input type="checkbox" readOnly checked={this.props.checked} />
                </div>
            </div>
        )
    }
}