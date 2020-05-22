import React from 'react';
import ImportPanel from './ImportPanel';
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
            displayCreatures : [],
            displayCreatureLimit : 25,
            currentView : '',
        }
        this.iAPI = new internalAPI();
        this.eAPI = new ExternalAPI()
    }

    openCreature(code) {
        let url = 'https://finaloutpost.net/view/'+code+'#main';
        this.setState({ currentView : url });
        if(window.ENV.DEBUG) console.log('Controller: View is now: '+this.state.currentView);
        this.clearCreature(code);
    }

    flagCreature(code) {
        this.iAPI.markForRemoval(code);
        if(window.ENV.DEBUG) console.log('Controller: Marking creature ' + code + ' as illegal.');
        this.clearCreature(code);
    }

    clearCreature(code) {
        this.iAPI.addClick(code);
        this.setState({ displayCreatures : this.state.displayCreatures.filter(
            item => item.code !== code 
        )})

        this.updateDisplayCreatures(code)
    }

    updateDisplayCreatures(clearedCode) {
        let current = this.state.displayCreatures.length;
        let min = this.state.displayCreatureLimit;
        
        if(current <= min) {
            let fetchCount = min-current + Math.round(min*.5);
            if(window.ENV.DEBUG) console.log('Controller: DisplayCreatures is getting low! Fetching '+min+'-'+current+'+'+Math.round(min*.5)+' ('+fetchCount+') new entries.');

            this.iAPI.getEntrySet(fetchCount,
                (data) => { 
                    let displayCodes = this.state.displayCreatures.map((item)=>{return item.code});
                    if(clearedCode) displayCodes.push(clearedCode);

                    let newEntries = data.records.filter(item => !displayCodes.includes(item.code));

                    if(window.ENV.DEBUG) console.log('Controller: Adding '+newEntries.length+' new entries to displayCreatures.');
                    this.setState({displayCreatures : this.state.displayCreatures.concat(newEntries)});
                }
            )
        }
    }

    updateDisplaySize(width, height) {
        // width + margin + borders
        let itemWidth = 77;
        // height + margin + borders
        let itemHeight = 87;

        let columns = Math.floor(width/itemWidth);
        let rows = Math.floor(height/itemHeight);

        if(window.ENV.DEBUG) console.log('Controller: SelectPanel can fit '+columns+'x'+rows+' ('+(columns*rows)+') components.');

        this.setState({displayCreatureLimit : (columns * rows)}, ()=>{this.updateDisplayCreatures()});

    }

    /*
    shouldComponentUpdate(nextProps, nextState) {
        // state.creatureLimit is passed up from SelectPanel.componentDidMount() and
        // is only used for invisible API-related calculations. Re-rendering the entire app
        // on change is unnecessary and potentially inefficient.
        if(this.state.creatureLimit !== nextState.creatureLimit) return false;
        return true
    }
    */

    render() {
        return (
            <div className="App-stage">
                <div className="stage-top">
                    <ImportPanel eAPI={this.eAPI} iAPI={this.iAPI} onCreatureUpdate={()=>{this.updateDisplayCreatures()}}/>
                </div>
                <div className="stage-bottom-outer">
                    <div className="stage-bottom-inner">
                    <SelectPanel 
                        creatures={this.state.displayCreatures} 
                        onCreaturePick={(code) => this.openCreature(code)}
                        onCreatureFlag={(code) => this.flagCreature(code)}
                        onRender={(width,height) => this.updateDisplaySize(width,height)}
                    />
                    <ViewPanel currentView={this.state.currentView}/>
                    </div>
                </div>
            </div>
        )
    }
}