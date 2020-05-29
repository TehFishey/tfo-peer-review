import React from 'react';
import './modals.css';

export default class ModalWindow extends React.Component {
    constructor(props) {
        super(props);

        this.handleClose = this.handleClose.bind(this);
    }

    handleClose() {
        this.props.onClose();
    };

    render() {
        if (!this.props.show) { return null; }
        return (
            <div className='modal-overlay' onClick={this.handleClose}>
                <div className='modal-window' onClick={(e)=>{e.stopPropagation()}}>
                    <h2 className='modal-title'>{this.props.title}</h2>
                    <div className='modal-content'>{this.props.children}</div>
                    <div className='modal-controls'>
                        <button className='stage-interface-button' onClick={this.handleClose}>
                            Close
                        </button>
                    </div>
                </div>
            </div>
        );
    }
}