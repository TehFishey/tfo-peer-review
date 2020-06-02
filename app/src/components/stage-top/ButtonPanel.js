import React from 'react';
import ModalWindow from '../modal-window/Modal';
import ModalPrivacyPolicy from '../modal-window/ModalPrivacyPolicy';
import ModalDisclaimer from '../modal-window/ModalDisclaimer';
import ModalHelp from '../modal-window/ModalHelp';

/**
 * Component which displays interface buttons above top of the "ImportPanel" window/component. 
 * Handles opening, closing, and configuring the modal popup element. Modal window contents
 * are defined in the ModalHelp, ModalDisclaimer, and ModalPrivacyPolicy wrapper classes.
 */
export default class ButtonPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal : false,
            modalTitle : '',
            modalContents : ''
        }
    }

    /**
     * Re-renders and displays the modal popup window. Blurs non-modal elements.
     */
    openModal() {
        document.getElementById('root').style.filter = 'blur(5px)';
        this.setState({showModal : true});
    }

    /**
     * Re-renders and hides the modal popup window. Unblurs non-modal elements.
     */
    closeModal() {
        document.getElementById('root').style.filter = ''
        this.setState({showModal : false})
    }

    /**
     * Sets the modal window's contents to Help, and renders it.
     */
    showHelpInfo() {
        this.setState({
            modalTitle : (<h1>User Guide</h1>),
            modalContents : <ModalHelp/>
        });
        this.openModal()
    }

    /**
     * Sets the modal window's contents to Disclaimer, and renders it.
     */
    showDisclaimer() {
        this.setState({
            modalTitle : (<h1>Terms & Disclaimer</h1>),
            modalContents : <ModalDisclaimer/>
        });
        this.openModal()
    }

    /**
     * Sets the modal window's contents to PrivacyPolicy, and renders it.
     */
    showPrivacyPolicy() {
        this.setState({
            modalTitle : (<h1>Privacy Policy</h1>),
            modalContents : <ModalPrivacyPolicy/>
        });
            
        this.openModal()
    }

    render() {
        return (
            <div className="stage-top-buttons">
                <ModalWindow 
                    show={this.state.showModal} 
                    title={this.state.modalTitle}
                    children={this.state.modalContents}
                    onClose={()=>this.closeModal()}   
                />
                <button onClick={()=>this.showHelpInfo()}>User Guide</button>
                <button onClick={()=>this.showDisclaimer()}>Disclaimer</button>
                <button onClick={()=>this.showPrivacyPolicy()}>Privacy Policy</button>
            </div>
        );
    }
}