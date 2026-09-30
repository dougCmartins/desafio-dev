import getHttpClient, {ApiEnvelope} from "@/http";
import { toRaw } from "vue";
import {TransactionFile} from "@/store/types/transactionType";
const state = () => ({
    transactions: []
})

// getters
const getters = {
    listTransactions:(state: { transactions: any; }) => {
        return toRaw(state.transactions);
    }
}

// actions
const actions = {
   async getAllTransactions ({ commit }: any) {
       const response = await getHttpClient.get<ApiEnvelope<unknown[]>>('transactions')
       commit('setTransactions', response.data.data);
   },
    async importTransactions ({ commit }: any, rows: TransactionFile[]) {
        const response = await getHttpClient.post<ApiEnvelope<{
            clients: unknown[];
            transactions: unknown[];
        }>>('transactions/import', rows);
        commit('clients/setClients', response.data.data.clients, { root: true });
        commit('setTransactions', response.data.data.transactions);
    },
    async clearImportedData ({ commit }: any) {
        await getHttpClient.delete<ApiEnvelope<unknown[]>>('transactions');
        commit('setTransactions', []);
    },
    async createTransaction (_context: any, payload: TransactionFile) {
       if (payload) {
           await getHttpClient.post('transactions', payload);
       }
    }
}

// mutations
const mutations = {
    setTransactions (state: { transactions: any; }, transactions: Array<TransactionFile>) {
        state.transactions = [];
        state.transactions = transactions;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}