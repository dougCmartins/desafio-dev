---
globs: *.vue, *.ts, *.js
alwaysApply: false
---

# 💻 Frontend Standards (Vue 3 / TypeScript)

## Ambiente do projeto
- Projeto novo começa com Docker, Makefile na raiz e MySQL. O frontend entra no mesmo `docker-compose.yml` e nos alvos do Makefile. Não se troca o banco nem se arranca o projeto fora desse Docker, salvo pedido explícito.

## 1. Ecossistema Vue 3 e TypeScript
- **Composition API:** O padrão absoluto para a construção de componentes é a Composition API, utilizando a tag `<script setup lang="ts">`.
- **TypeScript Obrigatório:** Utilize TypeScript de forma rigorosa para garantir a segurança de tipos, interfaces claras e um melhor *intellisense*, reduzindo erros em tempo de execução.

## 2. Reatividade e Performance
- **Pensar em Performance:** Construa código reativo de forma consciente. Distinga claramente o uso de `ref()` para valores primitivos e `reactive()` para objetos.
- **Otimização de Dados:** Para grandes volumes de dados que não precisam de reatividade profunda (ex: listas massivas de leitura), considere utilizar `shallowRef` ou `markRaw` para poupar memória e processamento.
- **Propriedades Computadas:** Utilize `computed()` para derivar estados e lógicas matemáticas, evitando ao máximo recalcular expressões complexas diretamente no HTML.

## 3. Componentização e Templates Limpos
- **Componentização Inteligente:** Divida interfaces pesadas em componentes menores, granulares e reutilizáveis (Padrão *Smart/Dumb Components*).
- **Sem HTML Inflado (Anti-v-if Exagerado):** Não polua os templates com árvores gigantes e aninhadas de `v-if` / `v-else-if`. Se a lógica de renderização condicional se tornar complexa:
    1. Abstraia esse bloco para um subcomponente dedicado.
    2. Resolva a lógica através de variáveis ou propriedades computadas (`computed`).
    3. Se a alternância de exibição for muito frequente e custosa para o DOM, prefira utilizar o `v-show`.

## 4. Gestão de Estado
- Para estados globais, partilha de dados entre ecrãs e fluxos assíncronos complexos, utilize o **Pinia**.
- Mantenha o fluxo de dados unidirecional: componentes emitem eventos (`emits`) para informar mudanças e recebem dados através de propriedades (`props`).
- **Corpo da API:** A store lê `response.data`, que já é a Data. Não leia `response.data.data` à espera de um envelope que o controller não monta. O envelope (`message`, `code`) só aparece no erro.
- **Mesmo contexto do backend:** O domínio nasce no backend. O frontend não abre contexto próprio. Ecrã, store e model desse domínio ficam na pasta com o mesmo nome de `backend/src/Domain/`.

## 5. Testes Automatizados (Vitest)
- **Ferramenta Nativa:** A biblioteca padrão para testes unitários e de componentes no nosso ecossistema frontend é o **Vitest** (integrado nativamente com o Vite).
- **Foco dos Testes:** Priorize testar a lógica de negócio encapsulada em *composables* e o comportamento dos componentes (ex: se as *props* são renderizadas corretamente e se os cliques disparam os *emits* esperados), evitando testar detalhes rígidos de implementação do framework.
- **Teste no contexto:** O Vitest fica nessa mesma pasta do domínio que o backend já definiu. Não numa pasta `tests/` global. O foco continua a ser o comportamento da store e do componente.