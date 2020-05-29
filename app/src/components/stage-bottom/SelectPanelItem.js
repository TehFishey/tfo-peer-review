import React from 'react';

/**
 * Button component used by SelectPanel; displays data regarding a single creature object, and allows
 * users to select or flag that creature.
 * 
 * @property {string} code: code of associated creature object.
 * @property {string} src: image path of associated creature object (external TFO url).
 * @property {function} onClick: Function to be executed when button is clicked.
 * @property {function} onRemovalClick: Function to be executed when flag button is clicked.
 */
export default class SelectPanelItem extends React.Component {
    render () {
        return (
            <div className="select-panel-item" 
                onClick={() => this.props.onClick(this.props.code)} >
                <div className="select-creature-image"
                    style={{
                        backgroundImage : 'url('+this.props.src+')',
                        backgroundRepeat : 'no-repeat', 
                        backgroundPosition : '50% 50%'
                    }}>
                    <button className="select-panel-remove-button" 
                        onClick={(event) => {
                        this.props.onRemovalClick(this.props.code)
                        event.stopPropagation()
                        }}>[X]</button>
                </div>
            </div>
        )
    }
}