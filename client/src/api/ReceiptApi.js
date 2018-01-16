import {apiEndpoint} from './Api';

export const ReceiptApi = {
    getAll: async () => {
        const res = await fetch(`${apiEndpoint}/receipts`);
        return res.json();
    },

    getMyReceipts: async () => {
        const res = await fetch(`${apiEndpoint}/myreceipts`, {credentials:"include"});
        return res.json();
    },
};