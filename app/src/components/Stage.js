import React from 'react';
import PropTypes from 'prop-types';
import ImportPanel from './ImportPanel';
import SelectPanel from './SelectPanel.js';
import ViewPanel from './ViewPanel.js';
import API from '../utilities/API';
import {checkUUID} from '../utilities/cookies';
import {throttle} from '../utilities/limiters';
import './stage-top.css';
import './stage-bottom.css';

/**
 * Highest-order react component. Tracks globally shared states, maintains methods which write 
 * to them, instantiates API, and renders child components.
 */
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

    /**
     * Causes ViewPanel to to display creature {code}'s page by setting the currentView state.
     * @param {string} code - code of creature to display
     */
    openCreature(code) {
        let url = 'https://finaloutpost.net/view/'+code+'#main';
        this.setState({ currentView : url });
        if(window.ENV.DEBUG) console.log('Controller: View is now: '+this.state.currentView);
        this.clearCreature(code);
    }

    /**
     * Flags creature {code} for review in the server's database. Calls clearCreature(code) afterwards.
     * @param {string} code - code of flagged creature
     */
    flagCreature(code) {
        checkUUID();
        this.API.addCreatureFlag(code);
        if(window.ENV.DEBUG) console.log('Controller: Marking creature ' + code + ' as illegal.');
        this.clearCreature(code);
    }

    /**
     * Removes creature {code} from the list of currently displayed creatures, and records a user click
     * for UUID, {code} on the server's backend. Calls fetchDisplayCreatures({code}) afterwards.
     * @param {string} code - code of clicked creature
     */
    clearCreature(code) {
        checkUUID();
        this.API.addCreatureClick(code);
        this.setState({ displayCreatures : this.state.displayCreatures.filter(
            item => item.code !== code 
        )})

        this.fetchDisplayCreatures(code)
    }

    /**
     * Throttled function (max 1 call per 3 seconds). Checks the number of creature objects ready 
     * for display against the number that can currently be displayed. If the number is too low, fetches
     * more creatures entries from the server.
     * @param {string} clearedCode - code of creature that was recently cleared, if any. Used for filtering.
     */
    fetchDisplayCreatures = throttle((clearedCode)=>{
        // Multiplier for number of creatures to fetch beyond what can currently be displayed
        // mult = 1 attempts to fill displayCreatures to 2x the display capacity of current screen.
        const mult = 1;
        let current = this.state.displayCreatures.length;
        let min = this.state.displayCreatureLimit;
        let extra = Math.round(min*mult);

        // Fetches creatures if current list is within 10% of what can be displayed
        if(current <=  Math.round(min*1.1)) {
            // Attempts to fetch difference between what can be displayed and what is available,
            // plus extras based on mult.
            let fetchCount = min-current + extra;
            if(window.ENV.DEBUG) console.log('Controller: DisplayCreatures is getting low! Fetching '+min+'-'+current+'+'+extra+' ('+fetchCount+') new entries.');

            this.API.getCreatureEntries(fetchCount,
                (data) => { 
                    if(window.ENV.DEBUG) console.log(data);
                    if(data.found) {
                        // API results are filtered; stuff that's already in displayCreatures is removed,
                        // as well as clearedCode (if it exists). We don't want to display duplicates.
                        let displayCodes = this.state.displayCreatures.map((item)=>{return item.code});
                        if(clearedCode) displayCodes.push(clearedCode);

                        let newEntries = data.creatures.filter(item => !displayCodes.includes(item.code));

                        if(window.ENV.DEBUG) console.log('Controller: Recieved '+newEntries.length+' new entries. Adding to displayCreatures.');
                        this.setState({displayCreatures : this.state.displayCreatures.concat(newEntries)});
                    }
                }
            )
        }
    }, 3000); 

    /**
     * Calculates the number of "SelectPanelItem" tiles/buttons can fit in a defined space. Used to
     * determine how many creature entries should be fetched/cached from database. Updates displayCreatureLimit
     * state based on result.
     * @param {number} width 
     * @param {number} height 
     */
    updateDisplaySize(width, height) {
        // SelectPanelItem width + margin + borders
        let itemWidth = 77;
        // SelectPanelItem height + margin + borders
        let itemHeight = 87;

        let columns = Math.floor(width/itemWidth);
        let rows = Math.floor(height/itemHeight);

        if(window.ENV.DEBUG) console.log('Controller: SelectPanel can fit '+columns+'x'+rows+' ('+(columns*rows)+') components.');

        // After screen size is calculated/updated, fetch more creatures from server if necessary.
        this.setState({displayCreatureLimit : (columns * rows)}, ()=>{this.fetchDisplayCreatures()});
    }

    componentDidMount() {
        checkUUID();
    }

    render() {
        return (
            <div className="App-stage">
                <div className="stage-top">
                    <ImportPanel API={this.API} onCreatureUpdate={()=>{this.fetchDisplayCreatures()}}/>
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