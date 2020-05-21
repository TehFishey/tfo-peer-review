import React from 'react';
import ImportPanelSearch from './ImportPanelSearch';
import ImportPanelSelect from './ImportPanelSelect';
import SelectPanel from './SelectPanel.js';
import ViewPanel from './ViewPanel.js';
import internalAPI from '../api/InternalAPI';
import ExternalAPI from '../api/ExternalAPI';
import './stage-top.css';
import './stage-bottom.css';

export default class Stage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            labIsOpen : false,
            labError : false,
            importCreatures : [],
            displayCreatures : [],
            currentView : '',
        }
        this.iAPI = new internalAPI();
        this.eAPI = new ExternalAPI()
    }

    openLabView(labName) {
        this.setState({labError : false});
        this.eAPI.labRequest(labName, (data) => {
            if(!data.error) {
                delete data.error;
                delete data.errorCode;
                this.checkCreatures(Object.values(data));
                this.setState({labIsOpen : true});
            } else {
                this.setState({labError : true})
            }
        })
    }

    checkCreatures(importArray) {
        let creatures = [];

        importArray.forEach((item) => {
            this.iAPI.getSingleEntry(item.code, (data) => {
                (typeof data.code !== 'undefined') ?
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
                this.iAPI.addEntry(tuple[1], (data) => {
                    this.updateDisplayCreatures();
                    if(window.ENV.DEBUG) console.log(data);
                });
            } else {
                this.iAPI.removeEntry(tuple[1], (data) => {
                    this.updateDisplayCreatures()
                    if(window.ENV.DEBUG) console.log(data);
                });
            };
        });

        this.setState({labIsOpen : false});
        this.setState({importCreatures : []});
    }

    updateViewUrl(code) {
        let url = 'https://finaloutpost.net/view/'+code+'#main';
        this.setState({ currentView : url });
        if(window.ENV.DEBUG) console.log('view is now: '+this.state.currentView);
        this.clearCreature(code);
    }

    flagCreature(code) {
        this.iAPI.markForRemoval(code);
        if(window.ENV.DEBUG) console.log('marking creature ' + code + ' as illegal!');
        this.clearCreature(code);
    }

    clearCreature(code) {
        this.iAPI.addClick(code);
        this.setState({ displayCreatures : this.state.displayCreatures.filter(
            item => item.code !== code 
        )})

        if(this.state.displayCreatures.length <= 25) {
            this.iAPI.getEntrySet(
                (data) => { 
                    let displayCodes = this.state.displayCreatures.map((item)=>{return item.code});
                    displayCodes.push(code);
                    let newEntries = data.records.filter(item => !displayCodes.includes(item.code));

                    this.setState({displayCreatures : this.state.displayCreatures.concat(newEntries)});
                }
            )
        }
    }

    updateDisplayCreatures() {
        this.iAPI.getEntrySet((data) => {
            this.setState({ displayCreatures : data.records });
        });
    }

    componentDidMount() {
        this.updateDisplayCreatures();
    }   

    render() {
        return (
            <div className="App-stage">
                <div className="stage-top">
                    {(this.state.labIsOpen) ? 
                        <ImportPanelSelect
                            key = {this.state.importCreatures} 
                            importCreatures = {this.state.importCreatures}
                            onSubmit = {(importCreatures) => this.submitLabView(importCreatures)}
                            onClose = {() => this.closeLabView()}
                        /> :
                        <ImportPanelSearch 
                            isError = {this.state.labError}
                            onSubmit = {(labName) => this.openLabView(labName)}
                        />
                    }
                </div>
                <div className="stage-bottom-outer">
                    <div className="stage-bottom-inner">
                    <SelectPanel 
                        creatures={this.state.displayCreatures} 
                        onCreaturePick={(code) => this.updateViewUrl(code)}
                        onCreatureFlag={(code) => this.flagCreature(code)}
                    />
                    <ViewPanel currentView={this.state.currentView}/>
                    </div>
                </div>
            </div>
        )
    }
}