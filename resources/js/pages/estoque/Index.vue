<script setup>
defineProps({ mensagem: String })
</script>

<template>
    <div class="p-10">
        <h1 class="text-2xl font-bold">{{ mensagem }}</h1>
        <p>Aqui você vai listar as vagas da Adzuna em breve!</p>
    </div>
</template>
