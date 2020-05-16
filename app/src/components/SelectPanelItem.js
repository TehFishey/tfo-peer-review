import React from 'react';

export default class SelectPanelItem extends React.Component {
    render () {
        return (
                <button 
                    className="select-panel-item"
                    style={{
                        backgroundImage : 'url('+this.props.src+')',
                        backgroundRepeat : 'no-repeat', 
                        backgroundPosition : '50% 50%'
                    }}
                    onClick={() => this.props.onClick(this.props.code)}
                />
        )
    }
}