import React, {Component} from 'react';
import './GradientBox.css';

class GradientBox extends Component {
    render() {
        return (
            <div className="gradient-box">
                <div className="team-header-text">
                    <h1 className="gradientBoxText">Vi får vektorprogrammet til å gå rundt!</h1>
                    <h3 className="gradientBoxText">Teamene har ansvar for alt fra rekruttering til drift av
                        nettsiden, sponsorer og lignende. Alle organisasjonelle oppgaver tas hånd om av
                        frivillige teammedlemmer</h3>
                </div>
            </div>
        )
    }
}

export default GradientBox;
