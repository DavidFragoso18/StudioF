<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="bg-charcoal border-t border-concrete/10 text-concrete py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Brand -->
            <div class="flex flex-col items-center md:items-start">
                 <h2 class="font-serif text-2xl text-mocha font-bold tracking-widest mb-4">
                    STUDIO<span class="text-white">F</span>
                </h2>
                <p class="text-sm leading-relaxed text-center md:text-left text-gray-400">
                    Votre beauté, notre passion.<br>
                    Salon de coiffure moderne et élégant<br>
                    à 1721 Cournillens.
                </p>
            </div>

            <!-- Contact -->
            <div class="flex flex-col items-center">
                <h3 class="text-mocha font-bold uppercase tracking-widest mb-4">Contact</h3>
                <p class="text-sm mb-2 text-gray-300">Fernanda Conde</p>
                <p class="text-sm mb-2 text-gray-300">La Prairie 1, 1721 Cournillens, Suíça</p>
                <a href="tel:0795019212" class="text-sm hover:text-mocha transition text-white">079 501 92 12</a>
            </div>

            <!-- Hours/Links -->
            <div class="flex flex-col items-center md:items-end">
                <h3 class="text-mocha font-bold uppercase tracking-widest mb-4">Liens</h3>
                 <div class="flex space-x-4 mb-4">
                    <!-- Facebook Icon (Placeholder) -->
                    <a href="#" class="hover:text-mocha transition text-gray-400">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                             <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                </div>
                 <Link :href="route('services')" class="text-sm hover:text-mocha transition text-gray-300">Prestations</Link>
            </div>
        </div>
        <div class="border-t border-white/5 mt-8 pt-8 text-center text-xs text-gray-500">
            &copy; {{ new Date().getFullYear() }} StudioF Coiffure. Tous droits réservés.
        </div>
    </footer>
</template>
