import getHttpClient from "@/http";
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
       let res = { data: [] }
       res = await  getHttpClient.get('transactions')
       commit('setTransactions', res.data.data);
   },
    async createTransaction ({ dispatch }: any, payload: Array<TransactionFile>) {
       if (payload) {
           await getHttpClient.post('transactions', payload);
       }

       await dispatch('getAllTransactions');
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