import React from 'react';
import ImportPanelItem from './ImportPanelItem';

export default class ImportPanelSelect extends React.Component {    
    constructor(props) {
        super(props);
        this.state = {
            importCreatures : this.props.importCreatures
        };

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleClose = this.handleClose.bind(this);
    }

    handleSubmit() {
        this.props.onSubmit(this.state.importCreatures);
    }

    handleClose() {
        this.props.onClose();
    }

    onItemCheck(code, checked) {
        this.setState({ importCreatures : this.state.importCreatures.map(
            (tuple) => { return (tuple[1].code === code) ? [checked, tuple[1]] : tuple }
        )});
        console.log('checked state for ' + code + ' is now: ' + checked.toString())
    }

    createPanelItems() {
        let panelItems = []
        this.state.importCreatures.forEach((tuple, index) => panelItems.push(
            <ImportPanelItem  key = {index}
                code = {tuple[1].code} 
                src={tuple[1].imgsrc}
                checked ={tuple[0]}
                onCheck={(code, checked) => this.onItemCheck(code, checked)}
            />));

        return panelItems;
    }

    render () {
        return (
            <div className="import-panel">
                <div className="import-panel-select">
                    {this.createPanelItems()}
                    <button text="Submit" onClick={this.handleSubmit}/>
                    <button text="Cancel" onClick={this.handleClose}/>
                </div>
            </div>
        )
    }
}