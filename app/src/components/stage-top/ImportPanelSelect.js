import React from 'react';
import ImportPanelSelectItem from './ImportPanelSelectItem';

/**
 * Sub-component of ImportPanel; display is mutually exclusive with ImportPanelSearch. Provides user
 * with an interface to add or remove creatures (taken from a TFO lab) to the site's database. Creatures
 * are selected by an array of buttons which can be 'checked' and 'unchecked'. Checked creatures are to be 
 * added to the database, unselected creatures are to be removed.
 * 
 * @property {state} key:  Dummy state input for forcing re-renders when state is updated
 * @property {array} importCreatures: Array of [boolean, {creature}] arrays for information dislay and submission. 
 * @property {function} onSubmit: Function to be executed when component data is submitted.
 * @property {function} onclose: Function to be executed when component is closed without submission.
 */
export default class ImportPanelSelect extends React.Component {    
    constructor(props) {
        super(props);
        this.state = {
            importCreatures : this.props.importCreatures,
            checkAllBox: false
        };

        //Bind handler methods to class for easier html scripting.
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleCheckAll = this.handleCheckAll.bind(this);
    }

    /**
     * Calls onSubmit function. Intended handler for submit button.
     */
    handleSubmit() {
        this.props.onSubmit(this.state.importCreatures);
    }

    /**
     * Calls onClose function. Intended handler for close button.
     */
    handleClose() {
        this.props.onClose();
    }

    /**
     * Updates the state for all importCreatures to true or false, based on the checkAllBox state. 
     * Intended handler for the (un)Check all button.
     */
    handleCheckAll() {
        this.setState({ checkAllBox : !this.state.checkAllBox });
        this.setState({ importCreatures : this.state.importCreatures.map(
            (tuple) => {return [!this.state.checkAllBox, tuple[1]]}
        )});
    }

    /**
     * Updates the state for selected importCreatures entry to true or false. 
     * Intended handler for ImportPanelSelectItem buttons.
     * @param {string} code string code associated with importCreatures entry to update
     * @param {boolean} checked boolean to update importCreatures entry with
     */
    onItemCheck(code, checked) {
        this.setState({ importCreatures : this.state.importCreatures.map(
            (tuple) => { return (tuple[1].code === code) ? [checked, tuple[1]] : tuple }
        )});
    }

    /**
     * Returns an array of ImportPanelSelectItem components based on the current importCreatures
     * state. Each component is associated with an element of the importCreatures array: the
     * element's first index is whether the Item is checked or not, and the second index is the 
     * associated creature data.
     * @returns {array} array of configured ImportPanelSelectItem components.
     */
    createPanelItems() {
        let panelItems = []
        this.state.importCreatures.forEach((tuple, index) => panelItems.push(
            <ImportPanelSelectItem  key = {index}
                code = {tuple[1].code} 
                src={tuple[1].imgsrc}
                checked ={tuple[0]}
                onCheck={(code, checked) => this.onItemCheck(code, checked)}
            />));

        return panelItems;
    }

    render () {
        return (
                <div className="import-panel-select">
                    <div className="import-panel-select-items">
                        {this.createPanelItems()}
                    </div>
                    <div className="import-panel-controls">
                        <label className="import-panel-label"><input type="checkbox" checked={this.state.checkAllBox} onChange={this.handleCheckAll}/>(Un)select All</label>
                        <div>
                            <button className='import-panel-button' onClick={this.handleSubmit}>Submit</button>
                            <button className='import-panel-button' onClick={this.handleClose}>Cancel</button>
                        </div>
                    </div>
                </div>
        )
    }
}