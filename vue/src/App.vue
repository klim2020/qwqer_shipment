<script setup>
import Button from "primevue/button"
import Autocomplete from '../src/components/Autocomplete.vue';

import { ref } from 'vue';

const hello = ref('hello');

const options = [
    { label: 'Apple', value: 'Apple' },
    { label: 'Orange', value: 'Orange' },
    { label: 'Banana', value: 'Banana' },
    { label: 'Mango', value: 'Mango' },
    { label: 'Watermelon', value: 'Watermelon' },
    { label: 'Pineapple', value: 'Pineapple' },
    { label: 'Grapes', value: 'Grapes' },
    { label: 'Strawberry', value: 'Strawberry' },
];
const suggestions = ["Apple", "Watermelon", "Pineapple", "Banana", "Orange"];


const value = ref("");
const items = ref(suggestions);

const search = (event) => {
    items.value = ["appla", "Watermelon"];
}

</script>

<template>
    <div class="card flex justify-content-center">
        <Autocomplete>cvsxv</Autocomplete>
    </div>
    
    <Button>hekkp</Button>
</template>

<style scoped>

</style>
