import getHttpClient from "@/http";
import { toRaw } from "vue";
const state = () => ({
    operations: []
})

// getters
const getters = {
    listOperations:(state: { operations: any; }) => {
        return toRaw(state.operations);
    }
}

// actions
const actions = {
   async getAllOperations ({ commit }: any) {
       let res = { data: [] }
       res = await  getHttpClient.get('operations')
       commit('setOperations', res.data);
   }
}

// mutations
const mutations = {
    setOperations (state: { operations: any; }, operations: any) {
        state.operations = [];
        state.operations = operations;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}