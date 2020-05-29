import React from 'react';

/**
 * Wrapper class for Disclaimer text. Renders simple static JSX.
 */
export default class Disclaimer extends React.Component {
    render() {
        return (
            <div>
                <h2>Terms of Use</h2>
                
                <p>By using this website, you agree to comply with the following terms and conditions of use:</p>
                <ol>
                    <li>You will abide by all of the <a href="https://finaloutpost.net/terms">terms and conditions of use</a> for The Final Outpost adoptables webgame, accessible at FinalOutpost.net. You will not use this site in any way that would violate said terms and conditions.</li>
                    <li>You will not abuse this site, or any of its components, in any way that would threaten the stability of either itself or The Final Outpost webgame.</li>
                    <li>You agree to only access TFOPeerReview.click via a web browser, and that this site's resources are not to be used in any other manner.</li>
                    <li>You have read and understand all of the conditions in the disclaimer below:</li>
                </ol>

                <h2>Disclaimer</h2>

                <p>THIS SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
                IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
                FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
                AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
                LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
                OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
                SOFTWARE.</p>

                <p>Last updated: May 29, 2020.</p>

                <h2>Open Source Notice</h2>

                <p>The software powering TFO Peer Review is maintained as an open-source project 
                under the MIT license. Source files and further information can be found at 
                the <a href="https://github.com/TehFishey/tfo-peer-review">tfo-peer-review GitHub repo</a>.</p>
            </div>
        );
    }
}