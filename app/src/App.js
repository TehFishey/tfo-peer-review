import React from 'react';
import Stage from './components/Stage';
import './App.css';

function App() {
  return (
    <div className="App">
      <div className="App-header">
        <h1>TLO Peer Review</h1>
      </div>
      <Stage />
      <div className="App-footer">
       <h1>Footer</h1>
      </div>
    </div>
  );
}

export default App;
