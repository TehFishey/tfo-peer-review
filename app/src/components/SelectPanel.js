import React from 'react';
import CreatureItem from './CreatureItem';

export default class SelectPanel extends React.Component {
    createItems() {
        let items = []

        this.props.creatures.forEach(i => items.push(
            <CreatureItem code = {i.code} 
                src={i.imgsrc} 
                onClick={(code) => this.props.onCreaturePick(code)}
            />));

        return items;
    }

    render () {
        return (
            <div className="select-panel">
                {this.createItems()}
            </div>
        )
    }
}