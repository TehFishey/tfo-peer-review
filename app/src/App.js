import React from 'react';
import Stage from './components/Stage';
import CookieConsent from './components/CookieConsent';
import './App.css';



function App() {
  return (
    <div className="App">
      <CookieConsent/>
      <div className="App-header">
        <h1>TFO Peer Review Network</h1>
      </div>
      <Stage />
      <div className="App-footer">
      <label style={{display : 'inline-block'}}>
        TFO Peer Review © 2020 M. D'Attilo  <a href="https://github.com/TehFishey/tfo-peer-review/blob/master/LICENSE">View license</a>
      </label>
      <ul style={{listStyle : 'none'}}>
        <li>The Final Outpost is property of Corteo</li>
        <li>"Scifi Surgery Room" image from <a href="https://www.pxfuel.com/en/free-photo-oojhf">pxfuel.com</a></li>
        <li>All creature images are property of their respective authors</li>
      </ul>
      </div>
    </div>
  );
}

export default App;
