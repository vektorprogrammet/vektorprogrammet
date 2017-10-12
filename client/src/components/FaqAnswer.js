import React, {Component} from 'react';
import './FaqAnswer.css';

class FaqAnswer extends Component {
    render() {
        return (
            <div className="faqAnswer">
                <p>{this.props.faqAnswer}</p>
            </div>
        );
    }
}

export default FaqAnswer;
