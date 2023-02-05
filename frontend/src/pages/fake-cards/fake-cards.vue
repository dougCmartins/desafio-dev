<template>
  <div class="fake-cards">
    <div class="card">
      <div class="card-body">
        <div class="d-flex p-md-1 justify-content-between">
          <div class="d-flex flex-wrap">
            <div class="align-self-center">
              <h2 class="mb-0 me-4">$36,000.00</h2>
            </div>
            <div>
              <h4>Custo total</h4>
              <p class="mb-0">Custo Mensal</p>
            </div>
          </div>
          <div class="align-self-center ml-icon">
            <i class="fas fa-wallet text-success fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="d-flex p-md-1 justify-content-between">
          <div class="d-flex flex-wrap">
            <div class="align-self-center">
              <h2 class="mb-0 me-4">$76,456.00</h2>
            </div>
            <div>
              <h4>Total de vendas</h4>
              <p class="mb-0">Vendas mensais</p>
            </div>
          </div>
          <div class="align-self-center ml-icon">
            <i class="fas fa-chart-line text-danger fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
export default defineComponent({
  name: "fake-cards"
});
</script>

<style scoped lang="scss">
@import "fake-cards";
</style>