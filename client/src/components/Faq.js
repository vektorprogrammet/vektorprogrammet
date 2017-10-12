import React, {Component} from 'react';
import './Faq.css';
import FaqQuestion from './FaqQuestion';
import FaqAnswer from './FaqAnswer';

class Faq extends Component {
    render() {
        const qsAndAs = this.props.questionsAndAnswers.map((item, index) =>
            <div key={index}>
                <FaqQuestion faqQuestion={item.question}/>
                <FaqAnswer faqAnswer={item.answer}/>
                <br/>
            </div>
        );
        return(
            <div>
                <h1>Ofte stilte spørsmål og svar</h1>
                <br/><br/>
                {qsAndAs}
            </div>
        );
    }
}

export default Faq;

