import React, {Component} from 'react';
import PageHeader from '../components/PageHeader';
import MyReceiptsContainer from '../components/MyReceipts/MyReceiptsContainer';
import './ReceiptPage.css';

export default class ReceiptPage extends Component {
    render() {
        return (
            <div className="container">
                <PageHeader>
                    <h1>Utlegg</h1>
                    <hr className="receipt-page"/>
                    <p>Her kan du registrere utlegg du har gjort for vektorprogrammet som du ønsker å få refundert. For
                        å få utlegget refundert må du laste opp en kvittering som bekrefter utlegget ditt.</p>
                    <h2>Hva kan jeg få refundert?</h2>
                    <p>Du kan typisk få refusjon for bussbilletter til og fra skole, kaffeposer til stand, kake til
                        arrangementer og lignende. Det er ellers lurt å høre med en leder om du kan få utlegget ditt
                        refundert før du legger ut. Om du har spørsmål kan du kontakte økonomiteamet på
                        <a href="mailto:okonomi@vektorprogrammet.no"> okonomi@vektorprogrammet.no</a>.</p>
                    <hr className="receipt-page"/>
                </PageHeader>
                <MyReceiptsContainer />
            </div>
        );
    }
}