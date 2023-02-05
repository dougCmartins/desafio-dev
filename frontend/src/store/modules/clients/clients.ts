import getHttpClient from "@/http";
import { toRaw } from "vue";
const state = () => ({
    clients: []
})

// getters
const getters = {
    listClients:(state: { clients: any; }) => {
        return toRaw(state.clients);
    }
}

// actions
const actions = {
   async getAllClients ({ commit }: any) {
       let res = { data: [] }
       res = await  getHttpClient.get('clients')
       commit('setClients', res.data);
   }
}

// mutations
const mutations = {
    setClients (state: { clients: any; }, clients: any) {
        state.clients = [];
        state.clients = clients;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}