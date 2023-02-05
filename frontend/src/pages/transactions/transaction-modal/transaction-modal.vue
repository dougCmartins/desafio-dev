<template>
  <div class="modal fade modal-xl" :id="[`transactionBackdrop-${identifier}`]" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" :aria-labelledby="[`transactionBackdrop-${identifier}Label`]" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header px-4">
          <h1 class="modal-title fs-5" :id="[`transactionBackdrop-${identifier}Label`]">Transações</h1>
          <button type="button" class="btn-close btm-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="table-responsive">
            <table class="table align-middle mb-0 bg-white">
              <thead class="bg-light">
              <tr>
                <th scope="row">#</th>
                <th scope="col">Descrição</th>
                <th scope="col">Tipo</th>
                <th scope="col">Valor</th>
                <th scope="col">Saldo</th>
                <th scope="col">Data</th>
                <th scope="col">Hora</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(transaction, key) in transactions" :key="key">
                <th scope="row">
                  <i class="fas fa-dollar-sign"></i>
                </th>
                <th scope="col">{{transaction.operations.description}}</th>
                <th scope="col">{{transaction.operations.type_description}}</th>
                <th scope="col">R$ {{transaction.value}}</th>
                <th scope="col">R$ {{transaction.amount}}</th>
                <th scope="col">{{dateNow(transaction.date_at)}}</th>
                <th scope="col">{{ transaction.hour_at }}</th>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary text-capitalize" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent} from 'vue';

export default defineComponent( {
  name: "transaction-modal",
  props: {
    transactions: Array,
    identifier: null,
  },
  methods: {
    dateNow(val: string) {
      let date = new Date(val);
      return date.toLocaleDateString('en-GB');
    }
  }
})
</script>

<style scoped lang="scss">
@import "transaction-modal";
</style>