<template>
  <div class="card">
    <div v-if="clients && clients.clients.length" class="table-responsive">
      <table class="table align-middle mb-0 bg-white table-borderless table-hover">
        <thead class="bg-light">
        <tr>
          <th scope="row">#</th>
          <th scope="col">Nome</th>
          <th scope="col">Cpf</th>
          <th scope="col">Loja</th>
          <th scope="col">Cartão</th>
          <th scope="col">Saldo</th>
          <th scope="col">Transações</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(client, key) in clients.clients" :key="key">
          <th scope="row">
            <i class="fas fa-user"></i>
          </th>
          <td> {{formatName(client.users.name)}}</td>
          <td>{{ client.cpf }}</td>
          <td>{{ formatName(client.stores.name) }}</td>
          <td>{{ client.card }}</td>
          <td>R$ {{ client.amount }}</td>
          <td class="text-center">
            <i data-bs-toggle="modal" :data-bs-target="[`#transactionBackdrop-${key}`]" class="fas fa-eye"/>
          </td>
          <transaction-modal :identifier="key" :transactions="client.transactions"/>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="failed">
      <i class="fas fa-print-slash fa-2x"></i>
      <h3 class="text-center">Não há dados para serem exibidos</h3>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useStore } from 'vuex'
import TransactionModal from "@/pages/transactions/transaction-modal/transaction-modal.vue";
export default  defineComponent({
  name: "transactions",
  components: {TransactionModal},
  setup() {
    const store = useStore()

    let clients = ref([]);

    store.dispatch('operations/getAllOperations')
    store.dispatch('clients/getAllClients')

    clients = computed(() => store.state.clients);

    return {
      clients,
    }
  },
  methods: {
    formatName(nome: string): string {
       return nome
           .toLowerCase()
           .replace(/(?:^|\s)\S/g, (capitalize: string) => {
             return capitalize.toUpperCase();
           });
    }
  }
});
</script>

<style scoped lang="scss">
@import "transactions";
</style>