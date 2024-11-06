<template>
    <v-card class="mt-5" align="center">
        <nav class="my-5">
            <template v-for="link in links" :key="link.label">

                <Link :href="link.url ?? ''" v-html="link.label" :style="{ pointerEvents: link.url ? 'auto' : 'none' }"
                    :class="{ 'bg-grey': link.active, 'text-grey': !link.url }" class="mt-3 px-4 py-2"
                    preserve-scroll />
            </template>
        </nav>
    </v-card>
</template>
<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: { Link },
    props: {
        links: {
            type: Object,
            Required: true
        }
    }
}
</script>