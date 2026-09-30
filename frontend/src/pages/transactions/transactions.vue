<template>
  <p v-if="loading" class="status-line">A carregar movimentos…</p>
  <p v-else-if="loadError" class="status-line error-line">Não foi possível carregar os dados.</p>
  <p v-else-if="!stores.length" class="status-line">Importe os dados para visualizar.</p>
  <div v-else class="grid">
    <section v-for="storeCard in stores" :key="storeCard.name" class="store-card">
      <div class="card-head">
        <h2>{{ storeCard.name }}</h2>
        <p class="balance" :class="balanceClass(storeCard.balance)">
          {{ formatBalance(storeCard.balance) }}
        </p>
      </div>
      <article v-for="(movement, index) in storeCard.movements" :key="index" class="movement">
        <div>
          <p class="desc">{{ movement.description }}</p>
          <p class="meta">
            {{ movement.nature }} · {{ movement.clientName }}
            <span>{{ movement.when }}</span>
          </p>
        </div>
        <p class="amount" :class="movement.inflow ? 'is-in' : 'is-out'">
          {{ formatAmount(movement.value, movement.inflow) }}
        </p>
      </article>
    </section>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, ref } from 'vue';
import { useStore } from 'vuex';

type ClientRow = {
  id: number;
  name: string;
  amount: number;
  store_name: string | null;
};

type TransactionRow = {
  id: number;
  client_id: number;
  value: number;
  description: string;
  type_description: string;
  date_at: string;
  hour_at: string;
};

type Movement = {
  description: string;
  nature: string;
  clientName: string;
  when: string;
  value: number;
  inflow: boolean;
};

type StoreCard = {
  name: string;
  balance: number;
  movements: Movement[];
};

const money = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
});

export default defineComponent({
  name: 'transactions',
  setup() {
    const store = useStore();
    const loading = ref(true);
    const loadError = ref(false);

    Promise.all([
      store.dispatch('clients/getAllClients'),
      store.dispatch('transactions/getAllTransactions'),
    ]).catch(() => {
      loadError.value = true;
    }).finally(() => {
      loading.value = false;
    });

    const stores = computed((): StoreCard[] => {
      const clientState = store.state.clients as { clients?: ClientRow[] };
      const transactionState = store.state.transactions as { transactions?: TransactionRow[] };
      const clients = clientState.clients ?? [];
      const transactions = transactionState.transactions ?? [];

      if (!transactions.length) {
        return [];
      }

      const grouped = new Map<string, { name: string; balance: number; movements: Movement[] }>();

      clients.forEach((client) => {
        const storeName = formatName(client.store_name || 'Sem loja');
        if (!grouped.has(storeName)) {
          grouped.set(storeName, {
            name: storeName,
            balance: 0,
            movements: [],
          });
        }
      });

      transactions.forEach((transaction) => {
        const owner = clients.find((client) => client.id === transaction.client_id);
        if (!owner) {
          return;
        }

        const storeName = formatName(owner.store_name || 'Sem loja');
        const current = grouped.get(storeName);
        if (!current) {
          return;
        }

        const inflow = isInflow(transaction.type_description);
        const value = Number(transaction.value);
        current.balance += inflow ? value : -value;
        current.movements.push({
          description: transaction.description,
          nature: inflow ? 'Entrada' : 'Saída',
          clientName: formatName(owner.name),
          when: formatWhen(transaction.date_at, transaction.hour_at),
          value,
          inflow,
        });
      });

      return Array.from(grouped.values())
        .filter((card) => card.movements.length > 0)
        .map((card) => ({
        name: card.name,
        balance: card.balance,
        movements: card.movements,
      }));
    });

    function balanceClass(value: number): string {
      if (value > 0) {
        return 'is-in';
      }

      if (value < 0) {
        return 'is-out';
      }

      return '';
    }

    function formatBalance(value: number): string {
      const formatted = money.format(Math.abs(value));
      return value < 0 ? `− ${formatted}` : formatted;
    }

    function formatAmount(value: number, inflow: boolean): string {
      const formatted = money.format(Math.abs(value));
      return inflow ? `+ ${formatted}` : `− ${formatted}`;
    }

    return {
      loading,
      loadError,
      stores,
      balanceClass,
      formatBalance,
      formatAmount,
    };
  },
});

function formatName(value: string): string {
  return value
    .toLocaleLowerCase('pt-BR')
    .replace(/(?:^|\s)\S/g, (letter) => letter.toLocaleUpperCase('pt-BR'));
}

function isInflow(typeDescription: string): boolean {
  const normalized = typeDescription.toLocaleLowerCase('pt-BR');
  return normalized === 'entrada' || normalized === 'inflow';
}

function formatWhen(dateAt: string, hourAt: string): string {
  return `${formatDate(dateAt)} · ${formatHour(hourAt)}`;
}

function formatDate(value: string): string {
  const iso = value.slice(0, 10);
  if (/^\d{4}-\d{2}-\d{2}$/.test(iso)) {
    const [year, month, day] = iso.split('-');
    return `${day}/${month}/${year}`;
  }

  if (/^\d{8}$/.test(value)) {
    return `${value.slice(6, 8)}/${value.slice(4, 6)}/${value.slice(0, 4)}`;
  }

  return value;
}

function formatHour(value: string): string {
  if (/^\d{6}$/.test(value)) {
    return `${value.slice(0, 2)}:${value.slice(2, 4)}`;
  }

  if (/^\d{2}:\d{2}/.test(value)) {
    return value.slice(0, 5);
  }

  return value;
}
</script>

<style scoped lang="scss">
@import "transactions";
</style>
