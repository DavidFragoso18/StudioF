<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="À Propos" />

    <PublicLayout>
        <!-- Intro Section -->
        <div class="min-h-screen py-24 flex items-center relative overflow-hidden">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                 <div class="text-center mb-20 animate-fade-in">
                    <h1 class="text-4xl md:text-6xl font-serif text-charcoal mb-4">À Propos de <span class="text-mocha">StudioF</span></h1>
                    <div class="w-32 h-1 bg-mocha mx-auto mt-6"></div>
                </div>

                <div class="flex flex-col md:flex-row gap-16 items-center">
                    <div class="md:w-1/2 animate-slide-right order-2 md:order-1">
                         <div class="relative group">
                            <div class="absolute inset-0 border-2 border-mocha transform translate-x-4 translate-y-4 rounded-3xl transition duration-500 group-hover:translate-x-2 group-hover:translate-y-2 opacity-30"></div>
                            <img src="/images/logo-card.png" alt="StudioF Logo" class="relative z-10 w-full shadow-2xl rounded-3xl grayscale hover:grayscale-0 transition duration-700 transform group-hover:scale-[1.02]">
                        </div>
                    </div>

                    <div class="md:w-1/2 space-y-8 animate-slide-left order-1 md:order-2">
                        <div>
                            <h2 class="text-4xl font-serif text-charcoal mb-2">Fernanda Conde</h2>
                            <h3 class="text-xl text-mocha uppercase tracking-widest font-bold">Expertise & Passion</h3>
                        </div>
                        
                        <div class="text-charcoal/80 space-y-6 leading-relaxed font-light text-lg">
                            <p>
                                Avec une <strong class="text-charcoal">vaste expérience</strong> dans l'art de la coiffure, Fernanda Conde ne cesse de se perfectionner pour offrir le meilleur à sa clientèle à Misery-Courtion.
                            </p>
                            <p>
                                Au fil des années, elle a multiplié les <strong class="text-charcoal">formations spécialisées</strong> dans les techniques les plus avancées (Inoa, Dialight, Diacolor, et bien plus encore), maîtrisant chaque nuance de la colorimétrie et chaque précision de coupe.
                            </p>
                             <p class="text-xl text-charcoal italic border-l-4 border-mocha pl-6 py-2 bg-mocha/5">
                                "Que vous cherchiez une transformation radicale ou un soin d'entretien, mon savoir-faire certifié vous garantit un résultat professionnel."
                            </p>
                        </div>

                        <div class="pt-4">
                             <div class="flex gap-4">
                                <div class="px-4 py-2 border border-mocha/30 rounded-full text-mocha text-sm uppercase tracking-wide font-bold">Coloriste Experte</div>
                                <div class="px-4 py-2 border border-mocha/30 rounded-full text-mocha text-sm uppercase tracking-wide font-bold">Visagiste</div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
