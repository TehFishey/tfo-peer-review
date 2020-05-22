import React from 'react';
import ImportPanel from './ImportPanel';
import SelectPanel from './SelectPanel.js';
import ViewPanel from './ViewPanel.js';
import API from '../utilities/API';
import './stage-top.css';
import './stage-bottom.css';
import checkUUID from '../utilities/cookie';

export default class Stage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            displayCreatures : [],
            displayCreatureLimit : 25,
            currentView : '',
        }
        this.API = new API();
    }

    openCreature(code) {
        let url = 'https://finaloutpost.net/view/'+code+'#main';
        this.setState({ currentView : url });
        if(window.ENV.DEBUG) console.log('Controller: View is now: '+this.state.currentView);
        this.clearCreature(code);
    }

    flagCreature(code) {
        checkUUID();
        this.API.markForRemoval(code);
        if(window.ENV.DEBUG) console.log('Controller: Marking creature ' + code + ' as illegal.');
        this.clearCreature(code);
    }

    clearCreature(code) {
        checkUUID();
        this.API.addClick(code);
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

            this.API.getEntrySet(fetchCount,
                (data) => { 
                    if(window.ENV.DEBUG) console.log(data);
                    if(data.found) {
                        let displayCodes = this.state.displayCreatures.map((item)=>{return item.code});
                        if(clearedCode) displayCodes.push(clearedCode);

                        let newEntries = data.creatures.filter(item => !displayCodes.includes(item.code));

                        if(window.ENV.DEBUG) console.log('Controller: Adding '+newEntries.length+' new entries to displayCreatures.');
                        this.setState({displayCreatures : this.state.displayCreatures.concat(newEntries)});
                    }
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

    componentDidMount() {
        checkUUID();
    }

    render() {
        return (
            <div className="App-stage">
                <div className="stage-top">
                    <ImportPanel API={this.API} onCreatureUpdate={()=>{this.updateDisplayCreatures()}}/>
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