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
            importCreatures : [],
            displayCreatures : [],
            currentView : '',
        }
        this.iAPI = new internalAPI();
        this.eAPI = new ExternalAPI()
    }

    openLabView(labName) {
        this.eAPI.labRequest(labName, (data) => {
            if(!data.error) {
                delete data.error;
                delete data.errorCode;
                this.checkCreatures(Object.values(data));
                this.setState({labIsOpen : true});
            } else {
                //something something scroll not found...
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
                    this.updateDisplayCreatures()
                    console.log(data)
                });
            } else {
                this.iAPI.removeEntry(tuple[1], (data) => {
                    this.updateDisplayCreatures()
                    console.log(data)
                });
            };
        });

        this.setState({labIsOpen : false});
        this.setState({importCreatures : []});
    }

    updateViewUrl(code) {
        let url = 'https://finaloutpost.net/view/'+code;
        this.setState({ currentView : url });
        console.log('view is now: '+this.state.currentView)
    }

    updateDisplayCreatures() {
        this.iAPI.getAllEntries((data) => {
            this.setState({ displayCreatures : data.records });
        });
    }

    componentDidMount() {
        this.updateDisplayCreatures();
    }   

    render() {
        return (
            <div>
                <div className="stage-top">
                    {(this.state.labIsOpen) ? 
                        <ImportPanelSelect
                            key = {this.state.importCreatures} 
                            importCreatures = {this.state.importCreatures}
                            onSubmit = {(importCreatures) => this.submitLabView(importCreatures)}
                            onClose = {() => this.closeLabView()}
                        /> :
                        <ImportPanelSearch 
                            onSubmit = {(labName) => this.openLabView(labName)}
                        />
                    }
                </div>
                <div className="stage-bottom">
                    <SelectPanel 
                        creatures={this.state.displayCreatures} 
                        onCreaturePick={(code) => this.updateViewUrl(code)}
                    />
                    <ViewPanel currentView={this.state.currentView}/>
                </div>
            </div>
        )
    }
}