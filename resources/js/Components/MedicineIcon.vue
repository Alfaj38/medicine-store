<script setup>
defineProps({
    iconId: {
        type: String,
        default: 'tablet-white'
    }
});
</script>

<template>
    <div class="relative w-full h-full flex items-center justify-center">
        <!-- SVG Definitions for Shadows & Gradients -->
        <svg class="absolute w-0 h-0">
            <defs>
                <filter id="pill-shadow" x="-20%" y="-20%" width="150%" height="150%">
                    <feDropShadow dx="0" dy="8" stdDeviation="6" flood-color="#0f172a" flood-opacity="0.15"/>
                    <feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#0f172a" flood-opacity="0.1"/>
                </filter>
                <linearGradient id="grad-white-tablet" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#e2e8f0" />
                </linearGradient>
                <linearGradient id="grad-white-capsule" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#f8fafc" />
                    <stop offset="20%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#cbd5e1" />
                </linearGradient>
                <linearGradient id="grad-pink" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#f472b6" />
                    <stop offset="20%" stop-color="#fbcfe8" />
                    <stop offset="100%" stop-color="#be185d" />
                </linearGradient>
                <linearGradient id="grad-red" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#ef4444" />
                    <stop offset="20%" stop-color="#fca5a5" />
                    <stop offset="100%" stop-color="#b91c1c" />
                </linearGradient>
                <linearGradient id="grad-blue" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#38bdf8" />
                    <stop offset="20%" stop-color="#7dd3fc" />
                    <stop offset="100%" stop-color="#0284c7" />
                </linearGradient>
                <linearGradient id="grad-orange-cap" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#f97316" />
                    <stop offset="20%" stop-color="#fdba74" />
                    <stop offset="100%" stop-color="#c2410c" />
                </linearGradient>
                <linearGradient id="grad-orange-body" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#f59e0b" />
                    <stop offset="20%" stop-color="#fde68a" />
                    <stop offset="100%" stop-color="#b45309" />
                </linearGradient>
                <linearGradient id="grad-sachet" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0ea5e9" />
                    <stop offset="100%" stop-color="#0369a1" />
                </linearGradient>
            </defs>
        </svg>

        <!-- White Tablet (Napa style) -->
        <svg v-if="iconId === 'tablet-white-split'" viewBox="0 0 100 100" class="w-[90%] h-[90%] overflow-visible">
            <g filter="url(#pill-shadow)">
                <circle cx="50" cy="50" r="40" fill="url(#grad-white-tablet)" />
                <circle cx="50" cy="50" r="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" />
                <!-- Split line -->
                <line x1="20" y1="50" x2="80" y2="50" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" />
                <line x1="20" y1="52" x2="80" y2="52" stroke="#ffffff" stroke-width="2" stroke-linecap="round" />
                <!-- 3D Highlight -->
                <path d="M 22 35 A 32 32 0 0 1 78 35" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" opacity="0.9"/>
            </g>
        </svg>

        <!-- White Capsule (Cefixime style) -->
        <svg v-else-if="iconId === 'capsule-white'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform -rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <rect x="30" y="15" width="40" height="70" rx="20" fill="url(#grad-white-capsule)" />
                <rect x="30" y="15" width="40" height="70" rx="20" fill="none" stroke="#cbd5e1" stroke-width="1.5" />
                <!-- Middle joint -->
                <line x1="30" y1="50" x2="70" y2="50" stroke="#94a3b8" stroke-width="2" />
                <!-- Highlight -->
                <rect x="38" y="22" width="6" height="56" rx="3" fill="white" opacity="0.9" />
            </g>
        </svg>

        <!-- Pink & White Capsule (Panadol style) -->
        <svg v-else-if="iconId === 'capsule-pink-white'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform -rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <path d="M 30 50 L 30 35 A 20 20 0 0 1 70 35 L 70 50 Z" fill="url(#grad-pink)" />
                <path d="M 30 50 L 30 65 A 20 20 0 0 0 70 65 L 70 50 Z" fill="url(#grad-white-capsule)" />
                <!-- Middle joint -->
                <line x1="30" y1="50" x2="70" y2="50" stroke="#94a3b8" stroke-width="1.5" />
                <!-- Highlight -->
                <rect x="38" y="22" width="6" height="56" rx="3" fill="white" opacity="0.6" />
            </g>
        </svg>

        <!-- Red & White Capsule (Amoxicillin style) -->
        <svg v-else-if="iconId === 'capsule-red-white' || iconId === 'pill-red-white'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform -rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <path d="M 30 50 L 30 35 A 20 20 0 0 1 70 35 L 70 50 Z" fill="url(#grad-red)" />
                <path d="M 30 50 L 30 65 A 20 20 0 0 0 70 65 L 70 50 Z" fill="url(#grad-white-capsule)" />
                <line x1="30" y1="50" x2="70" y2="50" stroke="#94a3b8" stroke-width="1.5" />
                <rect x="38" y="22" width="6" height="56" rx="3" fill="white" opacity="0.5" />
            </g>
        </svg>

        <!-- Blue Round Tablet (Metformin style) -->
        <svg v-else-if="iconId === 'tablet-blue-round'" viewBox="0 0 100 100" class="w-[90%] h-[90%] overflow-visible">
            <g filter="url(#pill-shadow)">
                <circle cx="50" cy="50" r="40" fill="url(#grad-blue)" />
                <circle cx="50" cy="50" r="40" fill="none" stroke="#0369a1" stroke-width="1" />
                <line x1="22" y1="50" x2="78" y2="50" stroke="#0c4a6e" stroke-width="2" stroke-linecap="round" />
                <line x1="22" y1="52" x2="78" y2="52" stroke="#7dd3fc" stroke-width="1.5" stroke-linecap="round" opacity="0.5" />
                <!-- 3D Highlight -->
                <path d="M 22 35 A 32 32 0 0 1 78 35" fill="none" stroke="white" stroke-width="4" stroke-linecap="round" opacity="0.6"/>
            </g>
        </svg>
        
        <!-- Blue Oblong Tablet (Cetirizine style) -->
        <svg v-else-if="iconId === 'tablet-blue-oblong' || iconId === 'capsule-blue'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform -rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <rect x="30" y="20" width="40" height="60" rx="20" fill="url(#grad-blue)" />
                <rect x="30" y="20" width="40" height="60" rx="20" fill="none" stroke="#0369a1" stroke-width="1" />
                <line x1="30" y1="50" x2="70" y2="50" stroke="#0c4a6e" stroke-width="2" />
                <!-- Highlight -->
                <rect x="38" y="26" width="5" height="48" rx="2.5" fill="white" opacity="0.5" />
            </g>
        </svg>

        <!-- Orange Bottle (Vitamin C style) -->
        <svg v-else-if="iconId === 'bottle-orange'" viewBox="0 0 100 100" class="w-[80%] h-[90%] overflow-visible">
            <g filter="url(#pill-shadow)">
                <!-- Cap -->
                <rect x="35" y="10" width="30" height="15" rx="3" fill="url(#grad-orange-cap)" />
                <rect x="32" y="14" width="36" height="6" rx="2" fill="url(#grad-orange-cap)" />
                <!-- Body -->
                <path d="M 30 25 L 70 25 C 75 25 80 30 80 35 L 80 85 C 80 90 75 95 70 95 L 30 95 C 25 95 20 90 20 85 L 20 35 C 20 30 25 25 30 25 Z" fill="url(#grad-orange-body)" />
                <!-- Label -->
                <rect x="25" y="45" width="50" height="30" fill="white" />
                <circle cx="50" cy="60" r="8" fill="#f97316" />
                <!-- Highlight -->
                <rect x="28" y="30" width="6" height="60" rx="3" fill="white" opacity="0.4" />
            </g>
        </svg>

        <!-- Blue Eye Drop Bottle (drop-blue) -->
        <svg v-else-if="iconId === 'drop-blue'" viewBox="0 0 100 100" class="w-[75%] h-[85%] overflow-visible">
            <g filter="url(#pill-shadow)">
                <!-- Cap/Nozzle -->
                <path d="M 45 10 L 55 10 L 58 35 L 42 35 Z" fill="url(#grad-blue)" />
                <rect x="40" y="30" width="20" height="10" rx="2" fill="url(#grad-white-capsule)" />
                <!-- Body -->
                <rect x="30" y="40" width="40" height="50" rx="10" fill="url(#grad-white-capsule)" />
                <!-- Label -->
                <rect x="30" y="55" width="40" height="20" fill="url(#grad-blue)" />
                <!-- Highlight -->
                <rect x="33" y="45" width="6" height="40" rx="3" fill="white" opacity="0.6" />
            </g>
        </svg>

        <!-- Orange Tube (tube-orange) -->
        <svg v-else-if="iconId === 'tube-orange'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <!-- Cap -->
                <rect x="25" y="40" width="10" height="20" rx="2" fill="url(#grad-white-capsule)" />
                <!-- Body -->
                <path d="M 35 35 L 80 40 L 80 60 L 35 65 Z" fill="url(#grad-orange-body)" />
                <!-- End seal -->
                <rect x="75" y="38" width="5" height="24" rx="1" fill="#b45309" />
                <!-- Highlight -->
                <path d="M 38 42 L 75 45" stroke="white" stroke-width="4" stroke-linecap="round" opacity="0.4" />
            </g>
        </svg>

        <!-- Syringe (syringe) -->
        <svg v-else-if="iconId === 'syringe'" viewBox="0 0 100 100" class="w-[90%] h-[90%] transform -rotate-45 overflow-visible">
            <g filter="url(#pill-shadow)">
                <!-- Needle -->
                <line x1="15" y1="50" x2="30" y2="50" stroke="#94a3b8" stroke-width="2" />
                <!-- Body/Barrel -->
                <rect x="30" y="40" width="40" height="20" rx="2" fill="#f8fafc" opacity="0.8" stroke="#cbd5e1" stroke-width="1.5" />
                <!-- Plunger end -->
                <rect x="70" y="42" width="15" height="16" fill="url(#grad-white-capsule)" />
                <rect x="85" y="35" width="5" height="30" rx="2" fill="url(#grad-white-capsule)" />
                <!-- Markings -->
                <line x1="40" y1="40" x2="40" y2="46" stroke="#94a3b8" stroke-width="1.5" />
                <line x1="50" y1="40" x2="50" y2="46" stroke="#94a3b8" stroke-width="1.5" />
                <line x1="60" y1="40" x2="60" y2="46" stroke="#94a3b8" stroke-width="1.5" />
            </g>
        </svg>

        <!-- Default Generic Medical Box (Fallback) -->
        <svg v-else viewBox="0 0 100 100" class="w-[85%] h-[85%] overflow-visible">
            <g filter="url(#pill-shadow)">
                <rect x="20" y="25" width="60" height="50" rx="8" fill="url(#grad-white-capsule)" stroke="#cbd5e1" stroke-width="1.5" />
                <rect x="20" y="25" width="60" height="15" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1.5" />
                <path d="M 45 45 L 55 45 M 50 40 L 50 50" stroke="#0ea5e9" stroke-width="4" stroke-linecap="round" />
            </g>
        </svg>

    </div>
</template>
