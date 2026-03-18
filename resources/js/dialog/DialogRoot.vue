<script setup lang="ts">
import AlertDialogBody from './AlertDialogBody.vue';
import ConfirmDialogBody from './ConfirmDialogBody.vue';
import { activeDialog, DialogType, resolveDialog } from './dialog-queue';
import ModalOverlay from './ModalOverlay.vue';
</script>

<template>
    <ModalOverlay v-if="activeDialog" @close="resolveDialog(activeDialog.id, false)">
        <ConfirmDialogBody
            v-if="activeDialog.type === DialogType.Confirm"
            :title="activeDialog.title"
            :message="activeDialog.message"
            @confirm="resolveDialog(activeDialog.id, true)"
            @cancel="resolveDialog(activeDialog.id, false)"
        />
        <AlertDialogBody
            v-else
            :title="activeDialog.title"
            :message="activeDialog.message"
            @acknowledge="resolveDialog(activeDialog.id, true)"
        />
    </ModalOverlay>
</template>
