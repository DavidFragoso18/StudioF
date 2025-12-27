<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navLinks = [
    { name: 'Accueil', route: 'welcome' },
    { name: 'Prestations', route: 'services' },
    { name: 'Notre Espace', route: 'studio' },
    { name: 'Portfolio', route: 'portfolio' },
    { name: 'Contact', route: 'contact' },
];
</script>

<template>
    <nav 
        :class="[
            'fixed w-full z-50 transition-all duration-500 ease-in-out',
            isScrolled || isMobileMenuOpen ? 'bg-white/95 backdrop-blur-md shadow-lg py-2 border-b border-concrete/20' : 'bg-transparent py-4 lg:py-6'
        ]"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center transition-all duration-300">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <Link :href="route('welcome')" class="group flex flex-col items-center">
                        <span 
                            :class="[
                                'text-2xl md:text-3xl font-serif font-bold tracking-widest transition duration-300',
                                isScrolled || isMobileMenuOpen ? 'text-charcoal group-hover:text-mocha' : 'text-charcoal group-hover:text-mocha'
                            ]"
                        >
                            STUDIO<span 
                                :class="[
                                    'transition duration-300',
                                    isScrolled || isMobileMenuOpen ? 'text-mocha group-hover:text-charcoal' : 'text-mocha group-hover:text-charcoal'
                                ]"
                            >F</span>
                        </span>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-12 items-center">
                    <Link 
                        v-for="link in navLinks" 
                        :key="link.name"
                        :href="route(link.route)" 
                        :class="[
                            'text-sm font-medium uppercase tracking-widest transition-all duration-300 relative group py-2',
                            route().current(link.route) ? 'text-mocha' : (isScrolled ? 'text-charcoal hover:text-mocha' : 'text-charcoal/80 hover:text-mocha')
                        ]"
                    >
                        {{ link.name }}
                        <span 
                            :class="[
                                'absolute bottom-0 left-0 w-0 h-[1px] bg-mocha transition-all duration-300 group-hover:w-full',
                                route().current(link.route) ? 'w-full' : ''
                            ]"
                        ></span>
                    </Link>
                </div>

                <!-- Mobile Menu Button -->
                <div class="-mr-2 flex items-center md:hidden">
                    <button @click="toggleMobileMenu" class="text-mocha hover:text-charcoal focus:outline-none p-2 transition duration-300 z-50 relative">
                        <span class="sr-only">Open menu</span>
                         <div class="w-8 h-6 relative flex flex-col justify-between">
                            <span :class="['w-full h-0.5 bg-current transform transition duration-500', isMobileMenuOpen ? 'rotate-45 translate-y-2.5' : '']"></span>
                            <span :class="['w-full h-0.5 bg-current transition duration-300', isMobileMenuOpen ? 'opacity-0 translate-x-4' : '']"></span>
                            <span :class="['w-full h-0.5 bg-current transform transition duration-500', isMobileMenuOpen ? '-rotate-45 -translate-y-3' : '']"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <transition
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="opacity-0 translate-y-[-20px]"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-[-20px]"
        >
            <div v-if="isMobileMenuOpen" class="md:hidden absolute top-full left-0 w-full bg-white backdrop-blur-xl border-b border-concrete/20 shadow-2xl overflow-hidden rounded-b-3xl">
                <div class="px-6 py-8 space-y-4">
                    <Link 
                        v-for="(link, index) in navLinks" 
                        :key="link.name"
                        :href="route(link.route)" 
                        class="block text-2xl font-serif font-bold text-center uppercase tracking-widest transition duration-500 transform hover:scale-105 hover:text-mocha"
                        :class="[
                            'animate-slide-up', 
                            route().current(link.route) ? 'text-mocha' : 'text-charcoal'
                        ]"
                        :style="{ animationDelay: `${index * 100}ms` }"
                        @click="isMobileMenuOpen = false"
                    >
                        {{ link.name }}
                    </Link>
                </div>
            </div>
        </transition>
    </nav>
</template>
