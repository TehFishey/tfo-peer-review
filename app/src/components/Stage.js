import React from 'react';
import ImportPanel from './stage-top/ImportPanel';
import SelectPanel from './stage-bottom/SelectPanel.js';
import ViewPanel from './stage-bottom/ViewPanel.js';
import ButtonPanel from './stage-top/ButtonPanel';
import {checkUUID} from '../utilities/Cookies';
import {throttle} from '../utilities/Limiters';
import './stage-top/stage-top.css';
import './stage-top/stage-top-mobile.css';
import './stage-bottom/stage-bottom.css';
import './stage-bottom/stage-bottom-mobile.css';


/**
 * Primary high-order react component. Tracks globally shared states, maintains methods which write 
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
        this.API = this.props.API;
    }

    /**
     * Causes ViewPanel to to display creature {code}'s page by setting the currentView state.
     * @param {string} code - code of creature to display
     */
    openCreature(code) {
        this.setState({ currentView : code });
        if(window.ENV.DEBUG) console.log(`Controller: View is now: https://finaloutpost.net/view/${this.state.currentView}#main`);
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

        this.fetchDisplayCreatures(code);
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

        // Fetches creatures if current list is within 10 clicks of what can be displayed
        if(current <=  min + 10) {
            // Attempts to fetch difference between what can be displayed and what is available,
            // plus extras based on mult. Always get 10 extra (buffer size; important on mobile)
            let fetchCount = min - current + extra + 10;
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
     * Calculates the number of "SelectPanelItem" components that can fit into a defined space. Used to
     * determine how many creature entries should be fetched/cached from database. Updates displayCreatureLimit
     * state based on result.
     * @param {number} width width of display area, in px
     * @param {number} height height of display area, in px
     * @param {number} padding size of display area's internal padding, if any
     */
    updateDisplaySize(width, height, padding) {
        // SelectPanelItem width + margin + borders
        let itemWidth = 65+(2*2)+3+4;
        // SelectPanelItem height + margin + borders
        let itemHeight = 75+(2*2)+3+4;

        let columns = Math.floor((width-padding*2)/itemWidth);
        let rows = Math.floor((height-padding*2)/itemHeight);

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
                <div className="stage-top-outer">
                    <ButtonPanel/>
                    <div className="stage-top-inner">
                        <ImportPanel API={this.API} onCreatureUpdate={()=>{this.fetchDisplayCreatures()}}/>
                    </div>
                </div>
                <div className="stage-bottom-outer">
                    <div className="stage-bottom-inner">
                    <SelectPanel 
                        creatures={this.state.displayCreatures} 
                        displayCount={this.state.displayCreatureLimit}
                        onCreaturePick={(code) => this.openCreature(code)}
                        onCreatureFlag={(code) => this.flagCreature(code)}
                        onRender={(width,height) => this.updateDisplaySize(width,height, 3)}
                    />
                    <ViewPanel currentView={this.state.currentView} onCreatureFlag={(code) => {this.flagCreature(code); this.setState({currentView : ''});}}/>
                    </div>
                </div>
            </div>
        )
    }
}