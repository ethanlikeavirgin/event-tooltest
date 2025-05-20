<template>
  <section class="py-32">
    <Container>
      <h2 class="text-center font-bold pb-24">Checkout our <span class="text-primary">prices</span></h2>
      <div class="grid grid-cols-12 gap-6">
          <div v-for="plan in plans" class="lg:col-span-4 md:col-span-6 col-span-12">
              <div class="main-card primary">
                  <span>{{ plan.name }}</span>
                  <div class="flex flex-row gap-2 items-end pt-4">
                    <h2>€ {{ plan.price }}</h2>
                    <span>/once</span>
                  </div>
                  <a class="btn btn--white w-full mt-8 text-center !rounded-xl cursor-pointer" @click.prevent="submit(plan)">
                  Add to Cart
                  </a>
                  <div class="mt-8">
                    <div class="plan-options" v-html="plan.options"></div>
                  </div>
              </div>
          </div>
      </div>
    </Container>
  </section>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import Container from '../Components/Container.vue';

export default {
  props: {
    plans: Array,
    auth: Object,
  },
  components: {
    Container,
  },
  setup() {
    const form = useForm({
      item_id: '',
      name: '',
      price: '',
      counter: 1,
      type: '',
    });

    const submit = (plan) => {
      form.item_id = plan.id;
      form.name = plan.name;
      form.price = plan.price;
      form.counter = 1;
      form.type = 'plan',

      form.post(route('purchase.store'), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onFinish: () => {
          console.log(`✅ Plan "${plan.name}" added to cart.`);
        },
        onError: (errors) => {
          console.error('❌ Error adding plan to cart:', errors);
        },
      });
    };

    return {
      submit,
      form,
    };
  },
};
</script>