import React from 'react';
import Stage from './components/Stage';
import './App.css';

function App() {
  return (
    <div className="App">
      <div className="App-header">
        <h1>TLO Peer Review Laboratory</h1>
      </div>
      <Stage />
      <div className="App-footer">
      Copyright (c) 2020 M. D'Attilo<br/>
      This webapp is an open source project under the MIT liscense.<br/>
      Source code is available<a href="https://github.com/TehFishey/peereview.">GitHub</a>.
      </div>
    </div>
  );
}

export default App;
