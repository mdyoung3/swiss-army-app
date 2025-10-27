<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '../components/NavBar.vue';
import { onMounted, ref } from 'vue';

const typedText = ref('');
const fullText = 'Home Network Manager';
const typingSpeed = 100;

onMounted(() => {
    let index = 0;
    const typeText = () => {
        if (index < fullText.length) {
            typedText.value += fullText.charAt(index);
            index++;
            setTimeout(typeText, typingSpeed);
        }
    };
    setTimeout(typeText, 500);
});
</script>

<template>
    <Head title="Home Network Manager">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen scanline">
        <NavBar />

        <div class="flex items-center justify-center min-h-screen px-4 pt-16">
            <div class="max-w-4xl w-full text-center">
                <!-- Terminal Window Effect -->
                <div class="terminal-card rounded-lg p-8 md:p-12 shadow-2xl">
                    <!-- Terminal Header -->
                    <div class="flex items-center justify-start mb-8 pb-4 border-b border-[#00ff6633]">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 rounded-full bg-[#ff6b6b77]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#ffd93d77]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#6bcf7f77]"></div>
                        </div>
                        <div class="ml-4 terminal-secondary text-xs">system/network-manager</div>
                    </div>

                    <!-- Animated Prompt -->
                    <div class="mb-6 text-left">
                        <span class="terminal-text text-sm">root@homeserver:~$</span>
                        <span class="terminal-text text-sm ml-2">init network_manager</span>
                    </div>

                    <!-- Main Heading with Typing Effect -->
                    <h1 class="text-4xl md:text-6xl font-bold terminal-glow-strong mb-6">
                        {{ typedText }}<span class="animate-pulse">_</span>
                    </h1>

                    <!-- Description -->
                    <div class="terminal-secondary text-base md:text-lg mb-8 space-y-4 text-left md:text-center max-w-3xl mx-auto leading-relaxed">
                        <p>
                            <span class="terminal-text">></span> A centralized control system for managing your home network infrastructure.
                        </p>
                        <p>
                            <span class="terminal-text">></span> Integrate with Pi-hole and other network tools to filter content, manage access controls, and monitor network activity across your household.
                        </p>
                        <p class="text-sm opacity-80">
                            <span class="terminal-text">></span> Designed for personal and family use. Secure. Private. Under your control.
                        </p>
                    </div>

                    <!-- System Status Indicators -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 text-sm">
                        <div class="flex items-center justify-center space-x-2 terminal-card rounded p-3">
                            <div class="w-2 h-2 rounded-full bg-[#00ff66] animate-pulse"></div>
                            <span class="terminal-secondary">System Online</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2 terminal-card rounded p-3">
                            <div class="w-2 h-2 rounded-full bg-[#00ff66] animate-pulse"></div>
                            <span class="terminal-secondary">DNS Active</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2 terminal-card rounded p-3">
                            <div class="w-2 h-2 rounded-full bg-[#00ff66] animate-pulse"></div>
                            <span class="terminal-secondary">Secure Connection</span>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a
                            href="/manager"
                            class="terminal-button-primary terminal-button px-8 py-4 rounded text-lg font-semibold w-full sm:w-auto hover:scale-105 transition-transform"
                        >
                            <span class="mr-2">></span>Get Started
                        </a>
                        <a
                            href="/urllist"
                            class="terminal-button px-8 py-4 rounded text-lg font-semibold w-full sm:w-auto hover:scale-105 transition-transform"
                        >
                            <span class="mr-2">#</span>View URL List
                        </a>
                    </div>

                    <!-- Terminal Footer -->
                    <div class="mt-10 pt-6 border-t border-[#00ff6633] text-left">
                        <div class="terminal-secondary text-xs space-y-1">
                            <div><span class="terminal-text">[INFO]</span> Network monitoring: Active</div>
                            <div><span class="terminal-text">[INFO]</span> Parental controls: Available</div>
                            <div><span class="terminal-text">[INFO]</span> DNS filtering: Ready</div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="terminal-card rounded p-6 text-left">
                        <div class="text-2xl mb-3 terminal-glow">🔒</div>
                        <h3 class="terminal-text font-semibold mb-2 text-lg">Access Control</h3>
                        <p class="terminal-secondary text-sm">
                            Temporarily bypass DNS blocking for specific URLs when needed.
                        </p>
                    </div>

                    <div class="terminal-card rounded p-6 text-left">
                        <div class="text-2xl mb-3 terminal-glow">👨‍👩‍👧‍👦</div>
                        <h3 class="terminal-text font-semibold mb-2 text-lg">Parental Controls</h3>
                        <p class="terminal-secondary text-sm">
                            Block social media platforms and other sites with one click.
                        </p>
                    </div>

                    <div class="terminal-card rounded p-6 text-left">
                        <div class="text-2xl mb-3 terminal-glow">📊</div>
                        <h3 class="terminal-text font-semibold mb-2 text-lg">Activity Logs</h3>
                        <p class="terminal-secondary text-sm">
                            Track all network management actions in one central location.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap');

* {
    font-family: 'JetBrains Mono', 'Fira Code', 'Source Code Pro', monospace;
}
</style>
