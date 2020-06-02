import React from 'react';
import {createPortal} from "react-dom";
import './modal-window.css';

const portalElement = document.getElementById("modal-root");

/**
 * Simple modal window class; displays an in-DOM popup window with dynamic content. 
 * createPortal from react-dom allows component to be rendered at document root while
 * still retaining react context.
 * 
 * @property {boolean} show: Whether or not to render the modal window.
 * @property {JSX} title: Title header for modal component.
 * @property {JSX} children: Document body for modal component.
 * @property {function} onClose: Function to execute when modal window is closed.
 */
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
            <div>
                {createPortal((
                    <div className='modal-overlay' onClick={this.handleClose}>
                        <div className='modal-window' onClick={(e)=>{e.stopPropagation()}}>
                            <div className='modal-title'>{this.props.title}</div>
                            <div className='modal-content'>{this.props.children}</div>
                            <div className='modal-controls'>
                                <button className='import-panel-button' onClick={this.handleClose}>Close</button>
                            </div>
                        </div>
                    </div>), 
                portalElement)}
            </div>
        );
    }
}