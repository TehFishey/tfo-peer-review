import React from 'react';
import SelectorFrame from './SelectorFrame.js';
import ViewFrame from './ViewFrame.js';
import './AppFrame.css';

export default class AppFrame extends React.Component {
    render () {
        return (
            <div className="appframe">
                    <SelectorFrame />
                    <ViewFrame />
            </div>
        )
    }
}