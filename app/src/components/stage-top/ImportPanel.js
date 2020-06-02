import React from 'react';
import ImportPanelSearch from './ImportPanelSearch';
import ImportPanelSelect from './ImportPanelSelect';

/**
 * High-order component for the "top" window of the tfo-peer-review Stage. ImportPanel contents swap
 * between "ImportPanelSearch" and "ImportPanelSelect" sub-components based on state. Manages states for
 * itself and sub-components, as well as methods which interact with them. 
 * 
 * @property {class} API: API service class
 * @property {function} onCreatureUpdate: Function to execute after creature imports are completed.
 */
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

    /**
     * Attempts to fetch creatures from a TFO lab; opens ImportPanelSelect component if successful.
     * @param {string} labName TFO username of lab to fetch from.
     */
    openLabView(labName) {
        this.setState({labError : ''});
        this.API.fetchByLabname(labName, (data) => {
            if(window.ENV.DEBUG) console.log(data);
            // data.error is true if there is an error on TFO's end
            // for internal API errors or request failures, errors are caught in API class.
            if(!data.error) {
                if(window.ENV.DEBUG) console.log('Controller: Found valid lab! Checking creatures and adding to state.');
                delete data.error;
                delete data.errorCode;
                this.checkCreatures(Object.values(data));
                this.setState({labIsOpen : true});
            } 
            // Read TFO error codes: 1-not found, 2-lab hidden, 3-no valid creatures
            else {
                if(window.ENV.DEBUG) console.log('Controller: Lab Error, code: ' + (typeof data.errorCode !== 'undefined') ? data.errorCode.toString() : 'null');
                if(data.errorCode.toString() === '1') this.setState({labError : 'ERROR: Lab not found!'});
                else if(data.errorCode.toString() === '2') this.setState({labError : 'ERROR: Lab is hidden!'});
                else if(data.errorCode.toString() === '3') this.setState({labError : 'ERROR: Lab contains no growing creatures!'});
                else this.setState({labError : 'ERROR: Unspecified error! What happened?!?'});  
            }
        })
    }

    /**
     * Checks an array of creature objects against ones already existing in site database. Sets importCreatures state
     * to an array of 2-element arrays; the first element is bool creature exists, the second is the creature object.
     * @param {array} importArray Array of creature objects to check
     */
    checkCreatures(importArray) {
        let codeArray = importArray.map((creature)=> {return creature.code})
        let creatureTuples = [];
        

        if(codeArray.length > 0) {
            this.API.checkCreatureEntries(codeArray, (data)=> {
                if(window.ENV.DEBUG) console.log('Controller: Checking creature entries: '+ codeArray.toString());
                if(window.ENV.DEBUG) console.log('Server Response: ');
                if(window.ENV.DEBUG) console.log(data);

                creatureTuples = importArray.map((creature)=> {
                    let bool = data.exists[creature.code];
                    return [bool, creature];
                });
                this.setState({importCreatures : creatureTuples});
            });
        }
        else {
            if(window.ENV.DEBUG) console.log('Controller: No creatures found to check! Exiting...');
            this.setState({importCreatures : []});
        }
    }

    /**
     * Closes the ImportPanelSelect component, opens the ImportPanelSearch component, and clears the importCreatures state.
     */
    closeLabView() {
        this.setState({labIsOpen : false});
        this.setState({importCreatures : []});
    }

    /**
     * Attempts to add or remove creatures from site database based on inputs. If input boolean is true, the
     * creature is added; otherwise, the creature is removed.
     * @param {array} importCreatures array of [bool, {creature}] arrays to add or remove from site database.
     */
    submitLabView(importCreatures) {
        let addCodes = [];
        let removeCodes = [];

        importCreatures.forEach((tuple) => {
            if(tuple[0]) addCodes.push(tuple[1].code);
            else removeCodes.push(tuple[1].code);
        });

        if(addCodes.length > 0) {
            this.API.addCreatureEntries(addCodes, (data) => {
                if(window.ENV.DEBUG) console.log('Controller: Adding entries: '+ addCodes.toString());
                if(window.ENV.DEBUG) console.log('Server Response: '+ data.message);
                this.props.onCreatureUpdate();
            });
        }
        if(removeCodes.length > 0) {
            this.API.removeCreatureEntries(removeCodes, (data) => {
                if(window.ENV.DEBUG) console.log('Controller: Removing entries: '+ removeCodes.toString());
                if(window.ENV.DEBUG) console.log('Server Response: '+ data.message);
                this.props.onCreatureUpdate();
            });
        }
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
