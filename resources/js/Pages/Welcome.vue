
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const portfolioItems = [
    { image: '/images/carousel_1.jpg' },
    { image: '/images/carousel_2.jpg' },
    { image: '/images/carousel_3.jpg' },
    { image: '/images/carousel_4.jpg' },
    { image: '/images/carousel_5.jpg' },
    { image: '/images/carousel_6.jpg' },
    { image: '/images/carousel_7.jpg' },
    { image: '/images/carousel_8.jpg' },
    { image: '/images/carousel_9.jpg' },
    { image: '/images/carousel_10.jpg' },
    { image: '/images/carousel_11.jpg' },
    { image: '/images/carousel_12.jpg' },
    { image: '/images/carousel_13.jpg' },
    { image: '/images/carousel_14.jpg' },
];
</script>

<template>
    <Head title="Accueil" />

    <PublicLayout>
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
            
            <!-- Animated Modern Background -->
            <div class="absolute inset-0 z-0">
                <!-- Base Transparent -->
                <div class="absolute inset-0"></div>
                
                <!-- Gentle Mocha Glow (Very subtle) -->
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-mocha/5 rounded-full blur-[120px] opacity-40 animate-pulse delay-700"></div>
                
                <!-- Sage Glow 2 (Very subtle) -->
                <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-sage/10 rounded-full blur-[150px] opacity-30"></div>
            </div>

            <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
                
                <!-- Main Title -->
                <h1 class="text-6xl md:text-8xl font-serif font-bold text-mocha mb-8 leading-tight animate-slide-up">
                    L'art de la <br/>
                    <span class="text-charcoal">Coiffure</span>
                </h1>

                <!-- Subtext -->
                <p class="text-xl md:text-2xl text-charcoal/80 font-light mb-12 max-w-2xl mx-auto leading-relaxed animate-slide-up animation-delay-300">
                    StudioF à Misery-Courtion. <br>
                    Révélez votre beauté dans un cadre unique.
                </p>

                <!-- Buttons -->
                <div class="flex flex-col items-center justify-center gap-6 animate-slide-up animation-delay-500">
                    
                    <div class="flex flex-col sm:flex-row gap-6 w-full justify-center">
                        <Link :href="route('contact')" 
                            class="inline-flex items-center justify-center px-12 py-5 font-bold text-white transition-all duration-300 bg-mocha hover:bg-charcoal shadow-lg hover:shadow-xl tracking-[0.2em] uppercase text-sm rounded-full w-full sm:w-auto">
                            Prendre Rendez-vous
                        </Link>
                        
                        <Link :href="route('portfolio')" 
                            class="inline-flex items-center justify-center px-10 py-5 font-bold text-mocha transition-all duration-300 bg-transparent border border-mocha/30 hover:bg-mocha/5 hover:border-mocha tracking-[0.2em] uppercase text-sm rounded-full w-full sm:w-auto backdrop-blur-sm">
                            Voir Portfolio
                        </Link>
                    </div>

                    <!-- New Button Below -->
                    <Link :href="route('studio')" 
                        class="inline-flex items-center justify-center px-10 py-5 font-bold text-mocha transition-all duration-300 bg-transparent border border-mocha/30 hover:bg-mocha/5 hover:border-mocha tracking-[0.2em] uppercase text-sm rounded-full w-full sm:w-auto backdrop-blur-sm">
                        Notre Espace
                    </Link>

                </div>

            <!-- End Hero Content -->
            </div>
        </div>

        <!-- Portfolio Carousel Section (Below Fold) -->
        <section class="py-20 w-full overflow-hidden bg-transparent relative z-10">
            <div class="max-w-7xl mx-auto px-6 text-center">
                 <div class="text-center mb-12">
                    <span class="text-sage uppercase tracking-[0.3em] text-xs font-bold mb-3 block">Inspiration</span>
                    <h2 class="text-3xl md:text-4xl font-serif text-charcoal mb-0">Nos <span class="text-mocha">Réalisations</span></h2>
                </div>

                <div class="overflow-x-auto flex gap-4 md:gap-6 pb-4 snap-x snap-mandatory hide-scroll px-4 justify-start md:justify-center">
                    <div v-for="(carouselItem, index) in portfolioItems" :key="index" class="w-[70vw] md:w-[350px] aspect-[2/3] snap-center shrink-0 bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-500 border border-transparent hover:border-mocha/20 select-none">
                            <img :src="carouselItem.image" class="w-full h-full object-cover hover:scale-105 transition duration-700 ease-out">
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.hide-scroll::-webkit-scrollbar {
    display: none;
}
.hide-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
