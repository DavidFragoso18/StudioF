<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

// Static Content (Single Example)
const item = {
    imageAfter: '/images/portfolio_after.jpg',
    imageBefore: '/images/portfolio_before.jpg',
    title: 'Transformation',
    description: "Un changement radical pour révéler la lumière naturelle. La technique de balayage permet de fondre les racines tout en apportant une brillance extrême sur les longueurs."
};

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
    <Head title="Portfolio" />

    <PublicLayout>
        <div class="min-h-screen bg-[#FDFBF7] flex flex-col justify-center py-20 relative">
            <!-- Navigation Indicator for Carousel (Optional context) -->
            <!-- <div class="fixed top-24 right-6 z-40 text-xs font-bold tracking-widest text-mocha/50 uppercase writing-vertical mix-blend-multiply hidden md:block">
                Studio F — Portfolio
            </div> -->

            <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full">
                
                <!-- Editorial Layout -->
                <div class="text-center mb-16 animate-slide-up">
                    <span class="text-sage uppercase tracking-[0.3em] text-sm font-bold mb-4 block">Notre Expertise</span>
                    <h1 class="text-5xl md:text-7xl font-serif text-charcoal mb-0">Le <span class="text-mocha">Portfolio</span></h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-0 gap-y-0 w-full relative pt-0 md:pt-0">
                    
                    <!-- Top Left: "avant." -->
                    <!-- Mobile: Added z-index and slight negative margin to sit tight ON TOP of image but not hidden. -->
                    <div class="flex flex-col justify-start md:justify-end items-start md:items-end md:pr-12 z-20 mb-[-2rem] md:mb-[-4rem]">
                        <h1 class="font-serif font-black text-[22vw] md:text-[8rem] lg:text-[10rem] leading-[0.8] tracking-tighter text-black mix-blend-multiply ml-4 md:ml-0">
                            avant.
                        </h1>
                    </div>

                    <!-- Top Right: Image (Before) -->
                    <!-- Mobile: Width 85%, aligned Right (ml-auto). Desktop: Width 85%, aligned Left. -->
                    <div class="w-[85%] md:w-[85%] aspect-[3/4] overflow-hidden relative z-10 ml-auto md:mr-auto md:ml-12 border-4 border-transparent">
                        <img :src="item.imageBefore" class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-700 ease-in-out" alt="Before Transformation">
                    </div>

                    <!-- Bottom Left: Image (After) -->
                    <!-- Mobile: Width 85%, aligned Left (mr-auto). Desktop: Width 85%, aligned Right. -->
                    <!-- Negative margin top for overlap. -->
                    <div class="w-[85%] md:w-[85%] aspect-[3/4] overflow-hidden -mt-24 md:-mt-48 z-20 mr-auto md:ml-auto md:mr-12 shadow-2xl">
                        <img :src="item.imageAfter" class="w-full h-full object-cover" alt="After Transformation">
                    </div>

                    <!-- Bottom Right: "après." -->
                    <!-- Mobile: Push down slightly to clear image bottom, align right. -->
                    <div class="flex flex-col pt-0 md:pt-8 md:pl-12 z-30 -mt-12 md:mt-24 items-end md:items-start">
                        <h1 class="font-serif font-black text-[22vw] md:text-[8rem] lg:text-[10rem] leading-[0.8] tracking-tighter text-black mb-6 md:mb-8 md:-mt-16 mix-blend-multiply mr-4 md:mr-0">
                            après.
                        </h1>
                    </div>

                </div>

            </div>

             <!-- Portfolio Carousel (Simple Gallery) -->
            <section class="pt-20 pb-0 md:pt-32 md:pb-0 relative overflow-visible border-t border-mocha/10">
                 <!-- Background elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-mocha/5 rounded-full blur-[100px]"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-8">
                        <span class="text-sage uppercase tracking-[0.3em] text-sm font-bold mb-4 block">Inspiration</span>
                        <h2 class="text-4xl md:text-5xl font-serif text-charcoal mb-0">Nos <span class="text-mocha">Réalisations</span></h2>
                    </div>

                    <div class="overflow-x-auto flex gap-4 md:gap-8 pb-4 snap-x snap-mandatory hide-scroll px-4 md:px-0">
                        <div v-for="(carouselItem, index) in portfolioItems" :key="index" class="w-[70vw] md:w-[400px] aspect-[2/3] md:aspect-[3/4] snap-center shrink-0 bg-white rounded-2xl md:rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-500 border border-transparent hover:border-mocha/20 select-none">
                             <img :src="carouselItem.image" class="w-full h-full object-cover hover:scale-105 transition duration-700 ease-out">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Standard CTA Button -->
            <div class="flex justify-center pb-4 pt-8">
                    <Link :href="route('contact')" class="bg-black/90 backdrop-blur-md text-white px-12 py-5 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-mocha transition duration-300 shadow-xl hover:shadow-2xl hover:scale-105 transform">
                    Réserver maintenant
                    </Link>
            </div>

        </div>
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
