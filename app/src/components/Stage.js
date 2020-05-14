import React from 'react';
import axios from 'axios';
import ImportPanel from './ImportPanel';
import SelectPanel from './SelectPanel.js';
import ViewPanel from './ViewPanel.js';
import './stage-top.css';
import './stage-bottom.css';

export default class Stage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            creatures : [],
            currentView : '',
        }
    }

    updateViewUrl(code) {
        let url = 'https://finaloutpost.net/view/'+code;
        this.setState({ currentView : url });
        console.log('view is now: '+this.state.currentView)
    }

    componentDidMount() {
        const url = 'http://localhost:8888/api/creature/read.php';
        console.log('attempting AJAX Request')
        axios.get(url).then(response => response.data)
        .then((data) => { 
            this.setState({ creatures: data.records });
            console.log('AJAX Returned:');
            console.log(this.state.creatures);
        });
    }   

    render() {
        return (
            <div>
                <div className="stage-top">
                    <ImportPanel />
                </div>
                <div className="stage-bottom">
                    <SelectPanel 
                        creatures={this.state.creatures} 
                        onCreaturePick={(code) => this.updateViewUrl(code)}
                    />
                    <ViewPanel currentView={this.state.currentView}/>
                </div>
            </div>
        )
    }
}