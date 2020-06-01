import React from 'react';
import Stage from './components/Stage';
import StatWidget from './components/StatWidget';
import CookieConsent from './components/CookieConsent';
import API from './utilities/API';
import './App.css';
import './App-mobile.css';



function App() {

  let APIService = new API();

  return (
    <div className="App">
      <CookieConsent/>
      <div className="App-header">
        <h1>TFO Peer Review Network</h1>
      </div>
      <Stage API={APIService}/>
      <div className="App-footer">
        <div style={{gridArea : "widget"}}>
          <StatWidget API={APIService}/>
        </div>
        <div style={{gridArea:"copyright", margin: "auto 0px", overflow: "hidden"}}>
          <div className='attribution' style={{fontSize: '10px', fontStyle: 'italic'}}>tfo-peer-review version 1.0.0</div>
          <div className='attribution'>TFO Peer Review © 2020 M. D'Attilo  <a href="https://github.com/TehFishey/tfo-peer-review/blob/master/LICENSE">View license</a></div>
          <div className='attribution'>The Final Outpost is property of Corteo</div>
          <div className='attribution'>All creature images are property of their respective authors</div>
          <div className='attribution'>"Scifi Surgery Room" image from <a href="https://www.pxfuel.com/en/free-photo-oojhf">pxfuel.com</a></div>
        </div>
      </div>
    </div>
  );
}

export default App;
