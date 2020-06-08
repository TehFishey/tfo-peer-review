import React from 'react';
import SelectPanelItem from './SelectPanelItem';
import {debounce} from '../../utilities/Limiters';

/**
 * Component for displaying clickable creature objects pulled from the server database (which, in turn, 
 * is populated from TFO.) This is the primary component for the "lower" window of the tfo-peer-review
 * Stage; it allows users to quickly open pages (iFrames) for numerous creatures on TFO, thereby giving 
 * those creatures "clicks".
 * 
 * @property {array} creatures: Array of creature objects available for "clicking".
 * @property {number} displayCount: Number of DisplayPanelItems that can currently fit into component.
 * @property {function} onCreaturePick: Function to be called when a creature is "clicked".
 * @property {function} onCreatureFlag: Function to be called when a creature is "flagged" by a user.
 * @property {function} onRender: Function to be called whenever this component is re-sized or re-rendered.
 */
export default class SelectPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            width : 0,
            height : 0,
        }

        // Bind handler method for easier event-listener scripting.
        this.handleUpdate = this.updateSize.bind(this);
    }
    
    /**
     * Returns an array of SelectPanelItem components based on the current creatures
     * property. Each component is associated with an element of the creatures array.
     * Array generation stops when it reaches the size limit, defined by the displayCount
     * property.
     * @returns {array} array of configured SelectPanelItem components.
     */
    createItems() {
        let panelItems = []

        if(this.props.creatures) {
            this.props.creatures.some((creature, index) => {
                panelItems.push(
                    <SelectPanelItem key = {index}
                        code = {creature.code} 
                        src={creature.imgsrc} 
                        onClick={(code) => this.props.onCreaturePick(code)}
                        onRemovalClick={(code) => this.props.onCreatureFlag(code)}
                    />
                )
                return (panelItems.length >= this.props.displayCount) ? true : false;
            });
            return panelItems;
        }
    }

    /**
     * Debounced function. Update's the component's height and width states based on the component's
     * current size. Used to calculate the number of SelectPanelItem components that can currently
     * be displayed.
     */
    updateSize = debounce(()=>{
        let currentWidth = this.panelDiv.clientWidth;
        let currentHeight = this.panelDiv.clientHeight;
        
        if (this.state.width !== currentWidth || this.state.height !== currentHeight) {
            if(window.ENV.DEBUG) console.log('Controller: SelectPanel is '+currentWidth+"x"+currentHeight);
            this.setState({width : currentWidth, height : currentHeight})
            this.props.onRender(currentWidth,currentHeight);
        }
    }, 1000);

    componentDidMount() {
        this.updateSize();
        window.addEventListener("resize", this.handleUpdate);
    }

    componentWillUnmount() {
        window.removeEventListener("resize", this.handleUpdate);
    }

    render () {
        return (
            <div className="select-panel" ref={(panelDiv) => {this.panelDiv = panelDiv}}>
                {this.createItems()}
            </div>
        )
    }
}