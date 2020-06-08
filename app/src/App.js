import React from 'react';
import Stage from './components/Stage';
import Footer from './components/Footer';
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
      <Footer API={APIService}/>
    </div>
  );
}

export default App;
