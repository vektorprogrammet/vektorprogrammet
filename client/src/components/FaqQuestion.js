import React, {Component} from 'react';
import './FaqQuestion.css';

class FaqQuestion extends Component {
    render() {
        return (
            <h3 className="faqQuestion">
                {this.props.faqQuestion}
            </h3>
        );
    }
}

export default FaqQuestion;