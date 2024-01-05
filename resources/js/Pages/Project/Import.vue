<template>
    <div class="flex justify-center">
        <form class="flex mr-2">
            <div class="mr-2">
                <input class="w-16 rounded text-center" type="number" min="1" max="2" v-model="type">
            </div>
            <div>
                <input @change="setExcel" type="file" ref="file" class="hidden">
                <a @click.prevent="selectExcel" href="#"
                   class="block w-32 rounded bg-green-800 text-center text-white p-2">Choose a file</a>
            </div>
        </form>
        <div v-if="file">
            <a @click.prevent="importExcel" href="#"
               class="block w-32 rounded bg-violet-800 text-center text-white p-2">Import</a>
        </div>
    </div>
    <div v-if="$page.props.flash.message" class="mt-4 text-green-600 text-center">
        {{ $page.props.flash.message }}
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    name: 'Import',
    layout: MainLayout,

    data() {
        return {
            file: null,
            type: 1,
        };
    },

    methods: {
        selectExcel() {
            this.$refs.file.click();
        },
        setExcel(evt) {
            this.file = evt.target.files[0];
        },
        importExcel() {
            const formData = new FormData;
            formData.append('file', this.file);
            formData.append('type', this.type);
            this.$inertia.post('/projects/import', formData, {
                onSuccess: () => {
                    this.file = null;
                    this.$refs.file.value = null;
                },
            });
        },
    },
};
</script>


<style scoped>

</style>
