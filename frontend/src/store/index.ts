import operations from './modules/operations/operations';
import transactions from "@/store/modules/transactions/transactions";
import clients from "@/store/modules/clients/clients";
import { createStore } from 'vuex';

const store = createStore({
    modules: {
        operations,
        transactions,
        clients
    },
})

export default store;