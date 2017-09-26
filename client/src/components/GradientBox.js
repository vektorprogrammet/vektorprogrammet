import React, {Component} from 'react';
import './GradientBox.css';

class GradientBox extends Component {
    render() {
        return (
            <div className="gradient-box">
                { this.props.children }
            </div>
        )
    }
}

export default GradientBox;
