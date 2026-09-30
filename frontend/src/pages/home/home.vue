<template>
  <main class="page">
    <header class="top">
      <h1>Finance</h1>
      <div class="import-wrap">
        <div class="actions">
          <button type="button" class="clear" :disabled="clearing" @click="clearData">
            Limpar
          </button>
          <label class="import">
            + Nova Transação
            <input id="import-file" type="file" accept="text/plain" @change="onChangeFile">
          </label>
        </div>
        <p v-if="importError" class="error-line">{{ importError }}</p>
      </div>
    </header>
    <transactions />
  </main>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useStore } from 'vuex';
import Transactions from '@/pages/transactions/transactions.vue';
import { TransactionFile } from '@/store/types/transactionType';

const linePattern = /(?<type>\d{1})(?<date_at>\d{8})(?<value>\d{10})(?<cpf>\d{11})(?<card>\S{12})(?<hour_at>\d{6})(?<name>.{14})(?<store_name>.{18})/g;

export default defineComponent({
  name: 'home',
  components: { Transactions },
  setup() {
    const store = useStore();
    const importError = ref('');
    const clearing = ref(false);

    function onChangeFile(event: Event) {
      const input = event.target as HTMLInputElement;
      const file = input.files?.[0];
      if (!file) {
        return;
      }

      const reader = new FileReader();
      reader.addEventListener('load', () => {
        const text = reader.result;
        if (typeof text === 'string') {
          void importFile(text);
        }
      });
      reader.addEventListener('error', () => {
        importError.value = 'Não foi possível importar o ficheiro.';
      });
      reader.readAsText(file, 'utf-8');
      input.value = '';
    }

    async function clearData() {
      importError.value = '';
      clearing.value = true;

      try {
        await store.dispatch('transactions/clearImportedData');
      } catch {
        importError.value = 'Não foi possível limpar os dados.';
      } finally {
        clearing.value = false;
      }
    }

    async function importFile(text: string) {
      importError.value = '';
      const rows = parseFile(text);

      if (!rows.length) {
        importError.value = 'Não foi possível importar o ficheiro.';
        return;
      }

      try {
        await store.dispatch('transactions/importTransactions', rows);
      } catch {
        importError.value = 'Não foi possível importar o ficheiro.';
      }
    }

    function parseFile(text: string): TransactionFile[] {
      const pattern = new RegExp(linePattern.source, 'g');
      const rows: TransactionFile[] = [];
      let match: RegExpExecArray | null;

      while ((match = pattern.exec(text)) !== null) {
        const groups = match.groups;
        if (!groups) {
          continue;
        }

        rows.push({
          type: Number(groups.type),
          date_at: groups.date_at,
          value: Number(groups.value) / 100,
          cpf: groups.cpf,
          card: groups.card,
          hour_at: groups.hour_at,
          name: groups.name.replace(/\s+$/g, ''),
          store_name: groups.store_name.replace(/\s+$/g, ''),
        });
      }

      return rows;
    }

    return {
      importError,
      clearing,
      onChangeFile,
      clearData,
    };
  },
});
</script>

<style scoped lang="scss">
@import "home";
</style>
