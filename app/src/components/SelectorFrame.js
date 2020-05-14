import React from 'react';
import axios from 'axios';

export default class AppFrame extends React.Component {
    state = {
        creatures: []
    }

    componentDidMount() {
        const url = 'http://192.168.0.111:8888';
        console.log('attempting AJAX Request')
        axios.get(url).then(response => response.data)
        .then((data) => {
          this.setState({ creatures: data })
          console.log(typeof this.state.creatures)
          console.log(this.state.creatures)
         })
        
      }    
    
     // { this.state.creatures.map(creatures => <li>{creatures.code}</li>)}
    render () {
        return (
            <div className="appframe-selector">
                <ul>
               
                </ul>
            </div>
        )
    }
}