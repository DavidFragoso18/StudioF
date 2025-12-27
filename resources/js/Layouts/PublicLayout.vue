<script setup>
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
</script>

<template>
    <div class="min-h-screen bg-cover bg-fixed bg-center flex flex-col font-sans text-charcoal" style="background-image: url('/images/bg-marble.png');">
        <NavBar />
        
        <main class="flex-grow">
            <slot />
        </main>

        <Footer />
    </div>
</template>
