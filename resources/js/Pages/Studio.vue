<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Le Studio" />

    <PublicLayout>
        <div class="min-h-screen bg-[#FDFBF7] flex flex-col justify-center py-24 relative overflow-hidden">
            
            <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full relative z-10">
                
                <div class="text-center mb-12 md:mb-24">
                    <span class="text-sage uppercase tracking-[0.3em] text-sm font-bold mb-4 block">Découvrir</span>
                    <h1 class="text-5xl md:text-7xl font-serif text-charcoal mb-6">Notre <span class="text-mocha">Espace</span></h1>
                </div>

                <!-- 3-Image Overlapping Layout (Zig-Zag) -->
                <div class="flex flex-col w-full max-w-6xl mx-auto relative pt-12 pb-8">
                    
                    <!-- Image 1: Left -->
                    <div class="w-[85%] md:w-[55%] aspect-[3/4] md:aspect-[4/3] relative z-10 mr-auto shadow-2xl overflow-hidden rounded-sm group">
                        <img src="/images/studio_1.jpg" class="w-full h-full object-cover transform scale-100 group-hover:scale-105 transition duration-[1.5s] ease-out" alt="Studio Interior 1">
                    </div>

                    <!-- Image 2: Right (Overlaps 1) -->
                    <div class="w-[85%] md:w-[55%] aspect-[3/4] md:aspect-[4/3] relative z-20 ml-auto -mt-24 md:-mt-48 shadow-2xl overflow-hidden rounded-sm border-8 border-[#FDFBF7] group">
                         <img src="/images/studio_2.jpg" class="w-full h-full object-cover transform scale-100 group-hover:scale-105 transition duration-[1.5s] ease-out" alt="Studio Interior 2">
                    </div>

                     <!-- Image 3: Left (Overlaps 2) -->
                     <div class="w-[85%] md:w-[55%] aspect-[3/4] md:aspect-[4/3] relative z-30 mr-auto -mt-24 md:-mt-48 shadow-2xl overflow-hidden rounded-sm border-8 border-[#FDFBF7] group">
                         <img src="/images/studio_3.jpg" class="w-full h-full object-cover transform scale-100 group-hover:scale-105 transition duration-[1.5s] ease-out" alt="Studio Interior 3">
                    </div>

                </div>

                <div class="text-center mt-4">
                     <p class="text-charcoal/70 text-lg md:text-xl font-light italic max-w-2xl mx-auto">
                        "Offrez-vous une parenthèse de beauté dans un cadre moderne, lumineux et chaleureux."
                    </p>
                </div>

            </div>

            <!-- Standard CTA Button -->
            <div class="flex justify-center pb-4 pt-24">
                    <Link :href="route('contact')" class="bg-black/90 backdrop-blur-md text-white px-12 py-5 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-mocha transition duration-300 shadow-xl hover:shadow-2xl hover:scale-105 transform">
                    Réserver maintenant
                    </Link>
            </div>
        </div>
    </PublicLayout>
</template>
