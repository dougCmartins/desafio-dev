<template>
  <main class="finance">
    <section class="finance__sidebar sidebar">
      <div class="finance__sidebar-container container">
        <strong class="logo"><i class="fas fa-landmark text-warning"></i></strong>
        <strong class="presentation">FINANCE</strong>
      </div>
    </section>
    <section class="finance__list container p-45px">
      <article class="w-100 mb-5">
         <fake-cards/>
      </article>
      <article class="finance__list-top">
        <h3><strong>Lista de clientes</strong></h3>
        <div class="finance__list-file">
          <button class="btn btn-info btn-sm">
            <label class="text-capitalize" for='input-file'>
              + Nova Transação
            </label>
          </button>
          <input id="input-file" type="file" accept="text/plain" @change="onChangeFile" />
        </div>
      </article>
      <article class="finance__list-card">
       <transactions />
      </article>
    </section>
  </main>
</template>

<script lang="ts">

import {useStore} from "vuex";
import {TransactionFile} from "@/store/types/transactionType";
import Transactions from "@/pages/transactions/transactions.vue";
import {defineComponent, reactive} from 'vue';
import FakeCards from "@/pages/fake-cards/fake-cards.vue";

export default defineComponent({
  name: "home",
  components: {FakeCards, Transactions},
  props: {
    msg: String,
  },
  setup() {
    const store = useStore()
    let newFileReader: any = reactive([]);

    function onChangeFile(fileList: any) {
      let file = fileList.target.files
      if (!file.length) {
        return;
      }

      readerFile(file[0])
    }

    const readerFile = (file: any) => {
      if (!file) {
        return;
      }

      let reader = new FileReader();
      reader.addEventListener("load",  (ev: any) => {
        let fileReader;
        fileReader = ev.target.result;
        if (fileReader) {
          parseFileReader(fileReader);
        }
      }, false);
      reader.readAsText(file, 'utf-8')
    }

    const sanitizeFile = (files: Array<TransactionFile>) => {
      const regex = /\s+$/gm;
      newFileReader = files.map((file: TransactionFile) => {
        file.name = file.name.replace(regex, '')
        file.store_name = file.store_name.replace(regex, '')
        file.type = +file.type
        file.value = +file.value / 100

        console.log(file);
        store.dispatch('transactions/createTransaction', file)
        store.dispatch('clients/getAllClients');
      });

      return newFileReader;
    }

    const parseFileReader = (file: any) => {
      const regex = /(?<type>\d{1})(?<date_at>\d{8})(?<value>\d{10})(?<cpf>\d{11})(?<card>\S{12})(?<hour_at>\d{6})(?<name>.{14})(?<store_name>.{18})/g;

      let reader: any[] = [];
      let item;

      while ((item = regex.exec(file)) !== null) {
        if (item.index === regex.lastIndex) {
          regex.lastIndex++;
        }
        reader.push(item.groups);
      }

      sanitizeFile(reader);
    }

    return {
      newFileReader,
      onChangeFile
    }
  },
});
</script>

<style scoped lang="scss">
@import "home";
</style>
