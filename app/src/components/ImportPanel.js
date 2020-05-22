import React from 'react';
import ImportPanelSearch from './ImportPanelSearch';
import ImportPanelSelect from './ImportPanelSelect';

export default class ImportPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            labIsOpen : false,
            labError : '',
            importCreatures : [],
        };

        this.API = this.props.API;
    }

    openLabView(labName) {
        this.setState({labError : ''});
        this.API.tfoLabRequest(labName, (data) => {
            if(window.ENV.DEBUG) console.log(data);
            if(!data.error) {
                if(window.ENV.DEBUG) console.log('Controller: Found valid lab! Checking creatures and adding to state.');
                delete data.error;
                delete data.errorCode;
                this.checkCreatures(Object.values(data));
                this.setState({labIsOpen : true});
            } else {
                if(window.ENV.DEBUG) console.log('Controller: Lab Error, code: ' + (typeof data.errorCode !== 'undefined') ? data.errorCode.toString() : 'null');
                if(data.errorCode.toString() === '1') this.setState({labError : 'ERROR: Lab not found!'});
                else if(data.errorCode.toString() === '3') this.setState({labError : 'ERROR: Lab contains no growing creatures!'});
                else this.setState({labError : 'ERROR: Unspecified error! What happened?!?'});  
            }
        })
    }

    checkCreatures(importArray) {
        let creatures = [];

        importArray.forEach((item) => {
            this.API.getSingleEntry(item.code, (data) => {
                (data.found) ?
                creatures.push([true, item]) :
                creatures.push([false, item]) 
                this.setState({importCreatures : creatures})
            });
        })
    }

    closeLabView() {
        this.setState({labIsOpen : false});
        this.setState({importCreatures : []});
    }

    submitLabView(importCreatures) {
        importCreatures.forEach((tuple) => {
            if(tuple[0]) {
                this.API.addEntry(tuple[1], (data) => {
                    this.props.onCreatureUpdate();
                    if(window.ENV.DEBUG) console.log('Controller: Adding entry: ');
                    if(window.ENV.DEBUG) console.log(data);
                });
            } else {
                this.API.removeEntry(tuple[1], (data) => {
                    this.props.onCreatureUpdate();
                    if(window.ENV.DEBUG) console.log('Controller: Removing entry: ');
                    if(window.ENV.DEBUG) console.log(data);
                });
            };
        });

        this.setState({labIsOpen : false});
        this.setState({importCreatures : []});
    }

    render () {
        return (
            <div className="import-panel">
                {(this.state.labIsOpen) ? 
                    <ImportPanelSelect
                        key = {this.state.importCreatures} 
                        importCreatures = {this.state.importCreatures}
                        onSubmit = {(importCreatures) => this.submitLabView(importCreatures)}
                        onClose = {() => this.closeLabView()}
                    /> :
                    <ImportPanelSearch 
                        errorString = {this.state.labError}
                        onSubmit = {(labName) => this.openLabView(labName)}
                    />
                 }
            </div>
        )
    }
}
