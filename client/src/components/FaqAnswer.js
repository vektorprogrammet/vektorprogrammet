import React, {Component} from 'react';
import './FaqAnswer.css';

class FaqAnswer extends React.Component {
    render() {
        return (
            <div>
                <p>{this.props.faqAnswer}</p>
            </div>
        );
    }
}

export default FaqAnswer;
