<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    seo: {
        type: Object,
        required: true,
        default: () => ({
            title: 'SaaSMedi',
            description: '',
            keywords: '',
            canonical: '',
            open_graph: {},
            twitter: {},
            schema: null
        })
    }
});

const og = computed(() => props.seo?.open_graph || {});
const twitter = computed(() => props.seo?.twitter || {});
const schemaData = computed(() => props.seo?.schema);
</script>

<template>
    <Head>
        <title>{{ seo?.title || 'SaaSMedi' }}</title>
        
        <meta v-if="seo?.description" name="description" :content="seo.description" />
        <meta v-if="seo?.keywords" name="keywords" :content="seo.keywords" />
        <link v-if="seo?.canonical" rel="canonical" :href="seo.canonical" />

        <!-- Open Graph Data -->
        <meta v-if="og['og:title']" property="og:title" :content="og['og:title']" />
        <meta v-if="og['og:description']" property="og:description" :content="og['og:description']" />
        <meta v-if="og['og:image']" property="og:image" :content="og['og:image']" />
        <meta v-if="og['og:url']" property="og:url" :content="og['og:url']" />
        <meta v-if="og['og:type']" property="og:type" :content="og['og:type']" />
        <meta v-if="og['og:site_name']" property="og:site_name" :content="og['og:site_name']" />

        <!-- Twitter Card -->
        <meta v-if="twitter['twitter:card']" name="twitter:card" :content="twitter['twitter:card']" />
        <meta v-if="twitter['twitter:title']" name="twitter:title" :content="twitter['twitter:title']" />
        <meta v-if="twitter['twitter:description']" name="twitter:description" :content="twitter['twitter:description']" />
        <meta v-if="twitter['twitter:image']" name="twitter:image" :content="twitter['twitter:image']" />
        
        <!-- JSON-LD Schema -->
        <component :is="'script'" v-if="schemaData" type="application/ld+json" v-html="schemaData"></component>

        <!-- Tracking & Verification -->
        <meta v-if="seo?.tracking?.gsc" name="google-site-verification" :content="seo.tracking.gsc" />
        <component :is="'script'" v-if="seo?.tracking?.ga" async :src="`https://www.googletagmanager.com/gtag/js?id=${seo.tracking.ga}`"></component>
        <component :is="'script'" v-if="seo?.tracking?.ga" v-html="`window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '${seo.tracking.ga}');`"></component>
    </Head>
</template>
