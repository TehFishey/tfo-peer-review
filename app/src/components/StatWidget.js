import React from 'react';
import {throttle} from '../utilities/Limiters';
import './stat-widget.css';

export default class StatWidget extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            allTimeClicks : 0,
            allTimeCurls : 0,
            allTimeAdds : 0,
            weeklyUniques : 0,
            weeklyClicks : 0,
            WeeklyCurls : 0,
            WeeklyAdds : 0
        }

        this.API = this.props.API
    }

    updateData = throttle(()=> {
        this.API.getLogData((data)=>{
            this.setState({
                allTimeClicks : data.allTime.clicks,
                allTimeCurls : data.allTime.curls,
                allTimeAdds : data.allTime.creatureAdds,
                weeklyUniques : data.weekly.uniques,
                weeklyClicks : data.weekly.clicks,
                WeeklyCurls : data.weekly.curls,
                WeeklyAdds : data.weekly.creatureAdds,
            })
        });
    }, 180000);

    componentDidMount() {
        this.updateData();
    } 
/*
            <div>
                <div className='stat-widget-grid'>
                    <div style={{gridArea:'title'}}>PR Network Activity Report:</div>
                    <div style={{gridArea:'label1'}}>Weekly:</div>
                        <div style={{gridArea:'wcur'}}><label>Labs: </label>{this.state.WeeklyCurls}</div>
                        <div style={{gridArea:'wadd'}}><label>Creatures: </label>{this.state.WeeklyAdds}</div>
                        <div style={{gridArea:'wcli'}}><label>Clicks: </label>{this.state.weeklyClicks}</div>
                    <div style={{gridArea:'label2'}}>Total:</div>
                        <div style={{gridArea:'blnk'}}></div>
                        <div style={{gridArea:'acur'}}><label>Labs: </label>{this.state.allTimeCurls}</div>
                        <div style={{gridArea:'aadd'}}><label>Creatures: </label>{this.state.allTimeAdds}</div>
                        <div style={{gridArea:'acli'}}><label>Clicks: </label>{this.state.allTimeClicks}</div>
                    <div style={{gridArea:'wuni'}}><label>Scientists this week: </label>{this.state.weeklyUniques}</div>
                </div>
            </div>
*/
    render() {
        return(
            <div>
                <div className='stat-widget-grid'>
                    <div style={{gridArea:'title'}}>PR Network Activity:</div>
                    <div className='stat-widget-cell' style={{gridArea:'label1'}}>Weekly:</div>
                        <div className='stat-widget-cell' style={{gridArea:'wcur'}}><label>Labs </label><br/>{this.state.WeeklyCurls}</div>
                        <div className='stat-widget-cell' style={{gridArea:'wadd'}}><label>Creatures </label><br/>{this.state.WeeklyAdds}</div>
                        <div className='stat-widget-cell' style={{gridArea:'wcli'}}><label>Clicks </label><br/>{this.state.weeklyClicks}</div>
                    <div className='stat-widget-cell' style={{gridArea:'label2'}}>Total:</div>
                        <div className='stat-widget-cell' style={{gridArea:'blnk'}}></div>
                        <div className='stat-widget-cell' style={{gridArea:'acur'}}><label>Labs </label><br/>{this.state.allTimeCurls}</div>
                        <div className='stat-widget-cell' style={{gridArea:'aadd'}}><label>Creatures </label><br/>{this.state.allTimeAdds}</div>
                        <div className='stat-widget-cell' style={{gridArea:'acli'}}><label>Clicks </label><br/>{this.state.allTimeClicks}</div>
                    <div className='stat-widget-cell' style={{gridArea:'wuni'}}><label>Scientists this week: </label>{this.state.weeklyUniques}</div>
                </div>
            </div>
        )
    }
}