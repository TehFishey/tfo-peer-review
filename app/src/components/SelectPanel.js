import React from 'react';
import SelectPanelItem from './SelectPanelItem';

export default class SelectPanel extends React.Component {
    createItems() {
        let panelItems = []

        if(this.props.creatures) {
            this.props.creatures.forEach((creature, index) => panelItems.push(
                <SelectPanelItem key = {index}
                    code = {creature.code} 
                    src={creature.imgsrc} 
                    onClick={(code) => this.props.onCreaturePick(code)}
                    onRemovalClick={(code) => this.props.onCreatureFlag(code)}
                />
            ));
            return panelItems;
        } else return <label> Looks like there's nothing here... </label>
        
    }

    render () {
        return (
            <div className="select-panel">
                {this.createItems()}
            </div>
        )
    }
}