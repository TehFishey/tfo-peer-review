import React from 'react';

export default class SelectPanelItem extends React.Component {
    render () {
        return (
            <div className="select-panel-item"
                    style={{
                        backgroundImage : 'url('+this.props.src+')',
                        backgroundRepeat : 'no-repeat', 
                        backgroundPosition : '50% 50%'
                    }}
                    onClick={() => this.props.onClick(this.props.code)}>
                <div className="select-panel-remove-button" 
                    onClick={(event) => {
                    this.props.onRemovalClick(this.props.code)
                    event.stopPropagation()
                    }}
                >[X]</div>
            </div>
        )
    }
}