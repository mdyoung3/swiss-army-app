<template>
    <nav class="terminal-nav fixed top-0 left-0 right-0 z-50 border-b border-[#00ff6644] bg-[#0a0a0aee] backdrop-blur-sm">
        <div class="container mx-auto px-4">
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center justify-between h-16">
                <div class="flex items-center space-x-1">
                    <span class="text-2xl terminal-glow">></span>
                    <span class="text-xl font-bold terminal-text ml-2">Home Network Manager</span>
                </div>

                <div class="flex items-center space-x-6">
                    <a
                        href="/"
                        :class="[
                            'nav-link px-3 py-2 rounded transition-all duration-200',
                            isActive('/') ? 'active-link' : 'inactive-link'
                        ]"
                    >
                        <span class="mr-2">~</span>Home
                    </a>
                    <a
                        href="/manager"
                        :class="[
                            'nav-link px-3 py-2 rounded transition-all duration-200',
                            isActive('/manager') ? 'active-link' : 'inactive-link'
                        ]"
                    >
                        <span class="mr-2">+</span>Submit URL
                    </a>
                    <a
                        href="/urllist"
                        :class="[
                            'nav-link px-3 py-2 rounded transition-all duration-200',
                            isActive('/urllist') ? 'active-link' : 'inactive-link'
                        ]"
                    >
                        <span class="mr-2">#</span>URL List
                    </a>
                    <a
                        href="/parental-controls"
                        :class="[
                            'nav-link px-3 py-2 rounded transition-all duration-200',
                            isActive('/parental-controls') ? 'active-link' : 'inactive-link'
                        ]"
                    >
                        <span class="mr-2">@</span>Parental Controls
                    </a>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-1">
                        <span class="text-xl terminal-glow">></span>
                        <span class="text-lg font-bold terminal-text ml-2">Network Manager</span>
                    </div>

                    <button
                        @click="toggleMenu"
                        class="terminal-button p-2 rounded"
                        aria-label="Toggle menu"
                    >
                        <svg
                            v-if="!isMenuOpen"
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu Drawer -->
                <transition name="slide-down">
                    <div
                        v-if="isMenuOpen"
                        class="absolute top-16 left-0 right-0 terminal-card border-t border-[#00ff6644] shadow-lg"
                    >
                        <div class="flex flex-col space-y-2 p-4">
                            <a
                                href="/"
                                :class="[
                                    'nav-link px-4 py-3 rounded transition-all duration-200',
                                    isActive('/') ? 'active-link' : 'inactive-link'
                                ]"
                                @click="closeMenu"
                            >
                                <span class="mr-2">~</span>Home
                            </a>
                            <a
                                href="/manager"
                                :class="[
                                    'nav-link px-4 py-3 rounded transition-all duration-200',
                                    isActive('/manager') ? 'active-link' : 'inactive-link'
                                ]"
                                @click="closeMenu"
                            >
                                <span class="mr-2">+</span>Submit URL
                            </a>
                            <a
                                href="/urllist"
                                :class="[
                                    'nav-link px-4 py-3 rounded transition-all duration-200',
                                    isActive('/urllist') ? 'active-link' : 'inactive-link'
                                ]"
                                @click="closeMenu"
                            >
                                <span class="mr-2">#</span>URL List
                            </a>
                            <a
                                href="/parental-controls"
                                :class="[
                                    'nav-link px-4 py-3 rounded transition-all duration-200',
                                    isActive('/parental-controls') ? 'active-link' : 'inactive-link'
                                ]"
                                @click="closeMenu"
                            >
                                <span class="mr-2">@</span>Parental Controls
                            </a>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'NavBar',
    data() {
        return {
            isMenuOpen: false,
            currentPath: '',
        };
    },
    mounted() {
        this.currentPath = window.location.pathname;
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
        closeMenu() {
            this.isMenuOpen = false;
        },
        isActive(path) {
            return this.currentPath === path;
        },
    },
};
</script>

<style scoped>
.nav-link {
    font-family: 'JetBrains Mono', 'Fira Code', 'Source Code Pro', monospace;
    font-size: 0.95rem;
}

.inactive-link {
    color: #9ef2a2;
    border: 1px solid transparent;
}

.inactive-link:hover {
    color: #00ff66;
    border-color: rgba(0, 255, 102, 0.3);
    background: rgba(0, 255, 102, 0.05);
    text-shadow: 0 0 5px rgba(0, 255, 102, 0.5);
}

.active-link {
    color: #00ff66;
    background: rgba(20, 184, 102, 0.2);
    border: 1px solid #14b866;
    text-shadow: 0 0 8px rgba(0, 255, 102, 0.6);
    box-shadow:
        0 0 10px rgba(0, 255, 102, 0.2),
        inset 0 1px 0 rgba(0, 255, 102, 0.1);
}

.active-link:hover {
    box-shadow:
        0 0 15px rgba(0, 255, 102, 0.3),
        inset 0 1px 0 rgba(0, 255, 102, 0.15);
}

.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
