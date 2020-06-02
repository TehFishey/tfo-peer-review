import React from 'react';

/**
 * Wrapper class for Help text. Renders simple static JSX.
 */
export default class Help extends React.Component {
    render() {
        return (
            <div>
                <h2>What is this site?</h2>

                <p>TFO Peer Review is a fansite for interacting with the adoptables webgame at FinalOutpost.net. 
                The purpose of this site is to provide the Final Outpost community with tools for quickly and easily 
                interacting with each other's growing creatures by giving them "clicks".</p>

                <h2>What is a "Click"?</h2>

                <p>In The Final Outpost, clicking on a creature (i.e. opening its view page) will raise one or more of that 
                creature's "stats". A creature's stats can only be raised like this once per day by each player, so getting 
                other players to click on your creatures is important for reaching high stat totals. Getting clicks is especially 
                important for growing creatures, as the "hardiness" stat cannot be improved after a creature reaches maturity.</p>

                <p>If the clicking player is logged in, clicks also have a chance of giving the clicker a small amount of gold, up to 
                a certain limit each day.</p>

                <h2>How do I use this site?</h2>

                <p>First, enter your Final Outpost lab name into the search field and click "Open Lab". You will be able to 
                select any of your currently-growing creatures to enter into TFO Peer Review. Once in the site, your creatures 
                will appear as clickable tiles in the lower window, along with creatures from other users. Clicking on these 
                tiles will quickly open the respective creature's page in the neighboring frame, giving that creature bonus stats. 
                Be sure to click on as many creatures as possible, and come back every day! Rest assured that other users will be doing 
                the same.</p>

                <h2>What is the red [X] for?</h2>

                <p>To provide as many clicks for growing creatures as possible, 
                this site does not allow adults in its creature pool. While adults are periodically 
                removed from this site's database, this can sometimes take some time; If you come across an adult creature that 
                hasn't been removed yet, please click the red [X] on its tile so that it can be looked at by the server.</p>

                <h2>How do I get gold for clicks on this site?</h2>

                <p>In order to get gold for clicking on creatures, you will need to open FinalOutpost.net in a different page or 
                tab and log in. Once you're logged in, clicks made on this site should count as coming from your account, 
                occasionally giving you gold!</p>

                <h2>Help! I'm logged in on TFO, but not on this site!</h2>

                <p>To block cross-site tracking and advertisements, some web browsers now prevent sites from reading cookies 
                created by other sites. This means that, depending on your settings, some browsers may prevent TFO from 
                seeing your login when clicking from this website.</p>

                <p>Cross-site tracking protection is important, so you shouldn't turn it off. Instead, you will want to add a 
                specific exception for TFOPeerReview and/or TFO to your browser's tracking protection rules. 
                The exact method for doing this varies by browser:</p>

                <p>For Chrome:</p> 
                <ul>
                    <li>Go to <a href="chrome://settings/content/cookies">chrome://settings/content/cookies</a></li>
                    <li>Check that "Block third-party cookies" is on (it should be, if you're having trouble!)</li>
                    <li>Under "Allow", click "Add", and add finaloutpost.net as an exception to the third-party cookie policy</li>
                    <li>Close settings; your login should now work on TFOPeerReview.click</li>
                </ul>

                <p>For Firefox:</p>
                    <ul><li>Follow the instructions <a href="https://support.mozilla.org/en-US/kb/add-trusted-websites-your-allow-list-firefox-focus">here</a> to turn off tracking protection on TFOPeerReview.click</li></ul>
            
                <h2>I have other questions...</h2>
                <p>Feel free to contact me @TehFishey#8171 on the TFO Discord server! Alternatively, if you've found a 
                bug, feel free to open an issue at the 
                <a href="https://github.com/TehFishey/tfo-peer-review/issues">tfo-peer-review issue tracker.</a></p>
            </div>
        );
    }
}