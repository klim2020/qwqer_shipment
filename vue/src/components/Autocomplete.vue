
<style scoped>
</style>

<script setup>
import autoComplete from "@tarekraafat/autocomplete.js";
import { ref, onMounted } from 'vue';

const msg = ref('Welcome to Your Vue.js App');

const configMain = {
            placeHolder: "Search for Food...",
            data: {
                src: ["Sauce - Thousand Island", "Wild Boar - Tenderloin", "Goat - Whole Cut"],
                cache: false,
            },
            resultItem: {
                highlight: true
            },
            events: {
                input: {
                    selection: (event) => {
                        const selection = event.detail.selection.value;
                        autoCompleteJS.input.value = selection;
                    }
                }
            }
        }

onMounted(() => {
    console.log('fsdf')
    const autoCompleteJS = new autoComplete({ configMain });

});

</script>

<template>
    <div>
        <div class="autoComplete_wrapper">
            <input id="autoComplete" type="text" tabindex="1">
        </div>
    </div>
</template>