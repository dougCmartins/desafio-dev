import getHttpClient, {ApiEnvelope} from "@/http";
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
       const response = await getHttpClient.get<ApiEnvelope<unknown[]>>('operations')
       commit('setOperations', response.data.data);
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