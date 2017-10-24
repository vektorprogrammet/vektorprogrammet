import React, {Component} from 'react';
import './Faq.css';

class FaqAnswer extends Component {
    render() {
        return (
            <p className="faqAnswer">
                {this.props.faqAnswer}
            </p>

        );
    }
}

export default FaqAnswer;