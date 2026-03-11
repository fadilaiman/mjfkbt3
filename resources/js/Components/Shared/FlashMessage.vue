<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info'].includes(value),
    },
});

const emit = defineEmits(['dismiss']);

const visible = ref(true);

const typeClasses = {
    success: 'bg-green-50 border-green-400 text-green-800',
    error: 'bg-red-50 border-red-400 text-red-800',
    info: 'bg-blue-50 border-blue-400 text-blue-800',
};

const iconName = {
    success: 'check_circle',
    error: 'error',
    info: 'info',
};

const iconClasses = {
    success: 'text-green-500',
    error: 'text-red-500',
    info: 'text-blue-500',
};

function dismiss() {
    visible.value = false;
    emit('dismiss');
}

onMounted(() => {
    setTimeout(() => {
        dismiss();
    }, 5000);
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-[-10px]"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-[-10px]"
    >
        <div
            v-if="visible"
            :class="[typeClasses[type], 'flex items-center gap-3 rounded-xl border px-4 py-3 shadow-sm']"
        >
            <span :class="[iconClasses[type], 'material-symbols-outlined text-xl']">{{ iconName[type] }}</span>
            <span class="flex-1 text-sm font-medium">{{ message }}</span>
            <button
                @click="dismiss"
                class="flex items-center justify-center rounded-lg p-1 hover:bg-black/5 transition-colors"
            >
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </Transition>
</template>
