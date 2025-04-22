<template>
    <Container>
      <div>
        <h1>Bedankt voor je aankoop!</h1>
        <div>Order nummer: {{ current_id }}</div>
  
        <div v-if="orders">
          <div>Bestelling ID: {{ orders.id }}</div>
  
          <div>
            <h2>Items:</h2>
            <ul>
              <li v-for="(item, index) in parsedItems" :key="index">
                {{ item.name }} (x{{ item.counter }}) - €{{ item.price }} per stuk, totaal: €{{ item.line_total }}
              </li>
            </ul>
          </div>
  
          <div><strong>Totaalbedrag: €{{ orders.total }}</strong></div>
        </div>
      </div>
    </Container>
  </template>
  
  <script>
  import Container from '../../Components/Container.vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  export default {
    props: {
      current_id: Object,
      orders: Object
    },
    components: {
      Container,
      AppLayout
    },
    computed: {
      parsedItems() {
        if (typeof this.orders?.items === 'string') {
          try {
            return JSON.parse(this.orders.items);
          } catch (e) {
            console.error("Invalid JSON in orders.items:", e);
            return [];
          }
        }
        return this.orders?.items || [];
      }
    }
  }
  </script>