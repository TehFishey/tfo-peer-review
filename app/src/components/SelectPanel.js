import React from 'react';
import SelectPanelItem from './SelectPanelItem';

export default class SelectPanel extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            width : 0,
            height : 0,
        }

        this.handleUpdate = this.updateSize.bind(this);
    }
    
    createItems() {
        let panelItems = []

        if(this.props.creatures) {
            this.props.creatures.forEach((creature, index) => panelItems.push(
                <SelectPanelItem key = {index}
                    code = {creature.code} 
                    src={creature.imgsrc} 
                    onClick={(code) => this.props.onCreaturePick(code)}
                    onRemovalClick={(code) => this.props.onCreatureFlag(code)}
                />
            ));
            return panelItems;
        } else return <label> Looks like there's nothing here... </label>   
    }

    debounce(func, ms) {
        let timer
        return () => {
            clearTimeout(timer)
            timer = setTimeout(() => {
                timer = null;
                func.apply(this,arguments);
            }, ms);
        };
    }

    componentDidMount() {
        this.updateSize();
        window.addEventListener("resize", this.handleUpdate);
    }

    componentWillUnmount() {
        window.removeEventListener("resize", this.handleUpdate);
    }

    updateSize = this.debounce(()=>{
        let currentWidth = this.panelDiv.clientWidth;
        let currentHeight = this.panelDiv.clientHeight;
        
        if (this.state.width !== currentWidth || this.state.height !== currentHeight) {
            if(window.ENV.DEBUG) console.log('Controller: SelectPanel is '+currentWidth+"x"+currentHeight);
            this.setState({width : currentWidth, height : currentHeight})
            this.props.onRender(currentWidth,currentHeight);
        }
    }, 1000);

    render () {
        return (
            <div className="select-panel" ref={(panelDiv) => {this.panelDiv = panelDiv}}>
                {this.createItems()}
            </div>
        )
    }
}