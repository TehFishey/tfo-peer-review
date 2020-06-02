import React from 'react';

/**
 * Button component used by ImportPanelSelect; displays data regarding a single creature object, and allows
 * users to select/deselect that creature.
 * 
 * @property {string} code: code of associated creature object.
 * @property {string} src: image path of associated creature object (external TFO url).
 * @property {boolean} checked: whether the associated creature is currently selected or unselected.
 * @property {function} onCheck: Function to be executed when button is clicked.
 */
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
                    <label className="import-panel-item-text">{this.props.code}</label>
                    <input type="checkbox" readOnly checked={this.props.checked} />
                </div>
            </div>
        )
    }
}