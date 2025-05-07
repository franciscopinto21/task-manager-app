<template>
    <div class="modal fade" ref="modalRef" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="close"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <slot> Are you sure you want to continue? </slot>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" @click="close">
                        Cancel
                    </button>
                    <button class="btn btn-danger btn-sm" @click="confirm">
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Modal } from "bootstrap";

const props = defineProps({
    title: {
        type: String,
        default: "Confirm Action",
    },
});

const emit = defineEmits(["confirm", "close"]);

const modalRef = ref(null);
let modalInstance = null;

onMounted(() => {
    modalInstance = new Modal(modalRef.value);
});

onBeforeUnmount(() => {
    modalInstance?.hide();
});

const open = () => {
    modalInstance?.show();
};

const close = () => {
    modalInstance?.hide();
    emit("close");
};

const confirm = () => {
    emit("confirm");
    close();
};

defineExpose({ open, close });
</script>
