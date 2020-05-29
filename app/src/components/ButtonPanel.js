import React from 'react';
import {createPortal} from "react-dom";
import ModalWindow from './ButtonPanelModal';
import ModalPrivacyPolicy from './ModalPrivacyPolicy';
import ModalDisclaimer from './ModalDisclaimer';
import ModalHelp from './ModalHelp';

const portalElement = document.getElementById("portal");

export default class ButtonPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal : false,
            modalTitle : '',
            modalContents : ''
        }
    }

    openModal() {
        document.getElementById('root').style.filter = 'blur(5px)';
        this.setState({showModal : true});
    }

    closeModal() {
        document.getElementById('root').style.filter = ''
        this.setState({showModal : false})
    }

    showHelpInfo() {
        this.setState({
            modalTitle : (<h1>User Guide</h1>),
            modalContents : <ModalHelp/>
        });
        this.openModal()

        this.openModal()
    }

    showDisclaimer() {
        this.setState({
            modalTitle : (<h1>Terms & Disclaimer</h1>),
            modalContents : <ModalDisclaimer/>
        });
        this.openModal()
    }

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
                {createPortal(
                <ModalWindow 
                    show={this.state.showModal} 
                    title={this.state.modalTitle}
                    children={this.state.modalContents}
                    onClose={()=>this.closeModal()}   
                />, portalElement)}
                <button onClick={()=>this.showHelpInfo()}>User Guide</button>
                <button onClick={()=>this.showDisclaimer()}>Disclaimer</button>
                <button onClick={()=>this.showPrivacyPolicy()}>Privacy Policy</button>
            </div>
        );
    }
}