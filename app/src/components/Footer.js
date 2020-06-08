import React from 'react';
import ModalWindow from './modal-window/Modal';
import ModalChangelog from './modal-window/ModalChangelog';
import StatWidget from './footer/StatWidget';

export default class Footer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal : false,
            modalTitle : '',
            modalContents : ''
        }

        this.API = this.props.API
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
    showChangelog() {
        this.setState({
            modalTitle : (<h1>Changelog</h1>),
            modalContents : <ModalChangelog/>
        });
        this.openModal()
    }

    render() {
        return(
            <div className="App-footer">
                <ModalWindow 
                    show={this.state.showModal} 
                    title={this.state.modalTitle}
                    children={this.state.modalContents}
                    onClose={()=>this.closeModal()}   
                />
                <div style={{gridArea : "widget"}}>
                    <StatWidget API={this.API}/>
                </div>
                <div style={{gridArea:"copyright", margin: "auto 0px", overflow: "hidden"}}>
                    <div className='footer-attribution' style={{fontSize: '10px', fontStyle: 'italic'}}>tfo-peer-review version 1.0.1  <button className='footer-changelog' onClick={()=>this.showChangelog()}>Changelog</button></div>
                    <div className='footer-attribution'>TFO Peer Review © 2020 M. D'Attilo  <a href="https://github.com/TehFishey/tfo-peer-review/blob/master/LICENSE">View license</a></div>
                    <div className='footer-attribution'>The Final Outpost is property of Corteo</div>
                    <div className='footer-attribution'>All creature images are property of their respective authors</div>
                    <div className='footer-attribution'>"Scifi Surgery Room" image from <a href="https://www.pxfuel.com/en/free-photo-oojhf">pxfuel.com</a></div>
                </div>
            </div>
        )
    }
}