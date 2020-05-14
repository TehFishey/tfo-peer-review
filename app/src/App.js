import React from 'react';
import Frame from './components/AppFrame';
import Input from './components/Input';
import './App.css';

function App() {
  return (
    <div className="App">
      <div className="app-header">
        <h1>Header</h1>
      </div>
      <Input />
      <Frame />
      <div className="app-footer">
       <h1>Footer</h1>
      </div>
    </div>
  );
}

export default App;
