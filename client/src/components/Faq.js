import React, {Component} from 'react';
import './Faq.css';
import FaqQuestion from './FaqQuestion';
import FaqAnswer from './FaqAnswer';

class Faq extends React.Component {
    render() {
        const qsAndAs = this.props.questionsAndAnswers;
        const resultQsAndAs = qsAndAs.map((item) =>
            <div>
                <h3>{item.question}</h3>
                <p>{item.answer}</p>
            </div>
        );
        return(
            <div>
                {resultQsAndAs}
            </div>
        );
    }
}

export default Faq;

