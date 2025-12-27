<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    services: Array
});

// Group services by category
const groupedServices = computed(() => {
    return props.services.reduce((acc, service) => {
        if (!acc[service.category]) {
            acc[service.category] = [];
        }
        acc[service.category].push(service);
        return acc;
    }, {});
});

const categoryOrder = ['Femmes', 'Hommes', 'Epilation', 'Beauté des mains'];
</script>

<template>
    <Head title="Prestations" />

    <PublicLayout>
        <div class="py-24 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Spacer -->
                <div class="h-10"></div>

                <!-- Menu Card Container -->
                <div class="max-w-3xl mx-auto bg-white p-12 md:p-16 shadow-2xl shadow-concrete/20 relative animate-slide-up">
                    
                    <!-- decorative top -->
                    <div class="text-center mb-16">
                        <h2 class="text-sm font-bold tracking-[0.2em] text-charcoal uppercase mb-2">Studio F &bull; Misery-Courtion</h2>
                        <h1 class="font-serif text-5xl text-mocha mb-8">Menu des Services</h1>
                        <div class="w-16 h-px bg-mocha mx-auto"></div>
                    </div>

                    <div class="space-y-16">
                        <div 
                            v-for="(category, index) in categoryOrder" 
                            :key="category" 
                            v-if="groupedServices[category]"
                        >
                            <!-- Category Header (Like 'CUT & STYLE') -->
                            <div class="mb-8 border-b border-mocha/30 pb-2">
                                <h3 class="text-lg font-bold tracking-[0.15em] text-mocha uppercase">{{ category }}</h3>
                            </div>
                            
                            <!-- Items -->
                            <div class="space-y-6">
                                <div 
                                    v-for="service in groupedServices[category]" 
                                    :key="service.id" 
                                    class="flex justify-between items-baseline group"
                                >
                                    <div class="pr-8">
                                         <span class="text-charcoal text-lg font-medium block">{{ service.name }}</span>
                                         <span v-if="service.duration_minutes" class="text-xs text-gray-400 mt-1 uppercase tracking-wider block">Prescription personnalisée &bull; {{ service.duration_minutes }} min</span>
                                    </div>

                                    <div class="flex-shrink-0">
                                        <span class="text-charcoal/80 font-medium whitespace-nowrap">{{ service.price_display }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- Footer of Card -->
                     <div class="mt-20 text-center">
                        <p class="text-xs text-gray-400 italic mb-8 tracking-wide">Nous utilisons exclusivement des produits L'Oréal Professionnel.</p>
                        
                        <div class="inline-block">
                             <Link :href="route('contact')" class="inline-block bg-mocha text-white px-10 py-4 text-sm font-bold tracking-[0.2em] uppercase hover:bg-charcoal transition duration-300">
                                Réserver
                            </Link>
                        </div>
                     </div>
                </div>
                

            </div>
        </div>
    </PublicLayout>
</template>
